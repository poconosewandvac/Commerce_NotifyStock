<?php

declare(strict_types=1);

use modmore\Commerce\Exceptions\ViewException;
use modmore\Commerce\Traits\SoftDelete;
use PoconoSewVac\NotifyStock\Services\ProductCondition;

/**
 * NotifyStock for Commerce.
 *
 * Copyright 2020 by Tony Klapatch <tony@klapatch.net>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_notifystock
 * @license See core/components/commerce_notifystock/docs/license.txt
 */
class NotifyStockRequest extends comSimpleObject
{
    use SoftDelete;

    /**
     * @var \comProduct $_product cached product instance
     */
    private $_product;

    /**
     * Get product associated with the request
     *
     * @return comProduct|null
     */
    public function getProduct(): ?\comProduct
    {
        if ($this->_product) {
            return $this->_product;
        }

        $product = $this->adapter->getObject('comProduct', $this->get('product'));
        $this->_product = $product;

        return $product;
    }

    /**
     * Get user instance. Returns null if user could not be found or if user is guest (id 0)
     *
     * @return \modUser|xPDOSimpleObject|null
     */
    public function getUser(): ?\modUser
    {
        return $this->adapter->getObject('modUser', $this->get('user'));
    }

    /**
     * Get the attached message instance
     *
     * @return \NotifyStockMessage|xPDOSimpleObject|null
     */
    public function getMessage()
    {
        return $this->adapter->getObject('NotifyStockMessage', $this->get('message'));
    }

    /**
     * Check if conditions are set
     *
     * @return bool
     */
    public function hasConditions(): bool
    {
        return is_array($this->get('conditions')) && count($this->get('conditions')) > 0;
    }

    /**
     * Check if the request message can be sent
     *
     * @return bool
     */
    public function conditionsMet(): bool
    {
        $conditionals = $this->get('conditions');

        $product = $this->getProduct();

        if (!$product) {
            $this->adapter->log(1, "[NotifyStock] Unable to get product with ID {$this->get('product')} while checking conditionals for notify request {$this->get('id')}");
            return false;
        }

        foreach ($conditionals as $conditional) {
            $condition = new ProductCondition($this, $conditional);

            if ($condition->check()) {
                $this->adapter->log(4, '[NotifyStock] Product "' . $product->get('id') . '" passed conditional for schedule ' . $this->get('id'));
                continue;
            } else {
                $this->adapter->log(4, '[NotifyStock] Product "' . $product->get('id') . '" failed conditional for schedule ' . $this->get('id'));
                return false;
            }
        }

        return true;
    }

    /**
     * Mark the request as completed
     */
    public function markCompleted(): void
    {
        $this->set('completed', true);
        $this->set('completed_on', time());
    }

    /**
     * Send email notify message. Returns true on success
     *
     * @return bool
     */
    public function send(): bool
    {
        $mail = $this->adapter->getService('mail', 'mail.modPHPMailer');
        if (!$mail instanceof \modMail) {
            $this->adapter->log(1, '[NotifyStockRequest] Could not send email: unable to load modMail');
            return false;
        }

        if (filter_var($this->get('email'), \FILTER_VALIDATE_EMAIL) === false) {
            $this->adapter->log(1, "[NotifyStockRequest] Email {$this->get('email')} on notify request {$this->get('id')} is invalid");
            return false;
        }

        $product = $this->getProduct();
        $user = $this->getUser();
        $message = $this->getMessage();
        $placeholders = [
            'product' => $product ? $product->toArray() : null,
            'user' => $user ? $user->toArray() : null,
            'conditions' => $this->get('conditions'),
            'email' => $this->get('email'),
            'added_on' => $this->get('added_on'),
            'message' => $message ? $message->toArray() : null,
            'config' => [
                'site_url' => $this->commerce->getOption('site_url'),
                'site_name' => $this->commerce->getOption('site_name'),
                'email_header' => $this->commerce->getOption('commerce.email_header_url'),
                'email_footer' => $this->commerce->getOption('commerce.email_footer_text'),
            ],
        ];

        try {
            $body = $this->commerce->view()->renderString($message->get('content'), $placeholders);
            $body = $this->adapter->parseMODXTags($body);
        } catch (ViewException $e) {
            $this->adapter->log(1, '[NotifyStockRequest] Could not send email for notify request ' . $this->get('id') . ' due to template rendering exception: ' . $e->getMessage());
            return false;
        }

        try {
            $subject = $this->commerce->view()->renderString($this->get('subject'), $placeholders);
            $subject = $this->adapter->parseMODXTags($subject);
        } catch (ViewException $e) {
            $this->adapter->log(1, '[NotifyStockRequest] Could not send email for notify request ' . $this->get('id') . ' due to template rendering exception in the subject: ' . $e->getMessage());
            return false;
        }

        $mail->reset();

        $from = $message->get('from');
        if (empty($from)) {
            $from = $this->commerce->getOption('commerce.email_from', null, $this->commerce->getOption('emailsender'), true);
        }

        $mail->set(\modMail::MAIL_BODY, $body);
        $mail->set(\modMail::MAIL_FROM, $from);
        $mail->set(\modMail::MAIL_FROM_NAME, $this->commerce->getOption('site_name'));
        $mail->set(\modMail::MAIL_SUBJECT, $subject);
        $mail->address('to', $this->get('email'));
        $mail->setHTML(true);

        if (!$mail->send()) {
            $this->adapter->log(1, '[NotifyStock] An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo);
            $mail->reset();
            return false;
        }

        return true;
    }
}
