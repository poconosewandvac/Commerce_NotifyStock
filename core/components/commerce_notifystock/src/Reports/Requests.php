<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Reports;

use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Reports\BaseReport;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Reports\Data\Header;
use modmore\Commerce\Reports\Data\Row;
use PoconoSewVac\NotifyStock\Repositories\MessageRepository;

class Requests extends BaseReport
{
    public function getName()
    {
        return $this->adapter->lexicon('commerce_notifystock.request_report');
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_notifystock.request_report_desc');
    }

    public function getGroup()
    {
        return 'notifystock';
    }

    public function getOptions()
    {
        $fields = [];

        $fields[] = new SelectField($this->commerce, [
            'name' => 'message',
            'label' => $this->adapter->lexicon('commerce_notifystock.message'),
            'options' => $this->getMessages(),
            'validation' => [new Required()],
        ]);

        $fields[] = new SelectField($this->commerce, [
            'name' => 'completed',
            'label' => $this->adapter->lexicon('commerce_notifystock.completed'),
            'options' => [
                [
                    'label' => 'All',
                    'value' => -1,
                ],
                [
                    'label' => 'Yes',
                    'value' => 1,
                ],
                [
                    'label' => 'No',
                    'value' => 0,
                ],
            ],
            'value' => -1,
        ]);

        return $fields;
    }

    public function getDataHeaders()
    {
        $headers = [];

        $headers[] = new Header('id', 'id', true);
        $headers[] = new Header('user', 'user', true);
        $headers[] = new Header('email', 'email', true);
        $headers[] = new Header('conditions', 'conditions', true);
        $headers[] = new Header('product', 'product_id', true);
        $headers[] = new Header('product_target', 'product_target', true);
        $headers[] = new Header('product_name', 'product_name', true);
        $headers[] = new Header('product_sku', 'product_sku', true);
        $headers[] = new Header('message_name', 'message_name', true);
        $headers[] = new Header('added_on', 'added_on', true);
        $headers[] = new Header('completed', 'completed', true);
        $headers[] = new Header('completed_on', 'completed_on', true);

        return $headers;
    }

    public function getDataRows()
    {
        $q = $this->adapter->newQuery('NotifyStockRequest');
        $q->select('NotifyStockRequest.id, NotifyStockRequest.user, NotifyStockRequest.email, NotifyStockRequest.conditions, NotifyStockRequest.product, comProduct.target AS product_target, comProduct.name AS product_name, comProduct.sku AS product_sku, NotifyStockMessage.name AS message_name, NotifyStockRequest.added_on, NotifyStockRequest.completed, NotifyStockRequest.completed_on');
        $q->innerJoin('comProduct', 'comProduct', ['NotifyStockRequest.product = comProduct.id']);
        $q->innerJoin('NotifyStockMessage', 'NotifyStockMessage', ['NotifyStockRequest.message = NotifyStockMessage.id']);
        $q->where([
            'NotifyStockRequest.removed' => false,
        ]);

        $messageId = $this->getOption('message');
        if ($messageId) {
            $q->where([
                'NotifyStockRequest.message' => $messageId,
            ]);
        }

        $completed = $this->getOption('completed');
        if ($completed != -1) { // not all
            $q->where([
                'NotifyStockRequest.completed' => $completed
            ]);
        }

        // Group products together
        $q->sortby('NotifyStockRequest.product');
        $q->sortby('NotifyStockRequest.added_on');

        $rows = [];
        foreach ($this->adapter->getIterator('NotifyStockRequest', $q) as $request) {
            $req = $request->toArray();

            if ($req['completed_on'] == null || $req['completed_on'] == 0) {
                $req['completed_on'] = $this->adapter->lexicon('commerce_notifystock.not_applicable');
            }

            $rows[] = new Row($req);
        }

        return $rows;
    }

    public function getAvailableCharts()
    {
        return [];
    }

    private function getMessages()
    {
        $messageRepository = new MessageRepository($this->commerce);
        $messages = $messageRepository->getMessages();
        $output = [];

        if (!$messages) {
            return $output;
        }

        foreach ($messages as $message) {
            $output[] = [
                'label' => $message->get('name'),
                'value' => $message->get('id'),
            ];
        }

        return $output;
    }
}