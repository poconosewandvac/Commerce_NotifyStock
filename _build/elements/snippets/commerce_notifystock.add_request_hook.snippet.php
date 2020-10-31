<?php

/**
 * Notify Stock Request
 * Formit hook to add a product notify request
 */

$path = $modx->getOption('commerce.core_path', null, MODX_CORE_PATH . 'components/commerce/') . 'model/commerce/';
$params = ['mode' => $modx->getOption('commerce.mode')];
/** @var \Commerce|null $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $path, $params);
if (!($commerce instanceof \Commerce)) {
    return '<p class="error">Could not load Commerce.</p>';
}

if ($commerce->isDisabled()) {
    return $commerce->adapter->lexicon('commerce.mode.disabled.message');
}

// Validation
$conditions = $modx->getOption('conditions', $scriptProperties, '');
$messageId = $modx->getOption('message', $scriptProperties, 0);
$email = $hook->getValue('email');
$productId = $hook->getValue('product');
$userId = $modx->user->get('id');

$decodedConditions = json_decode($conditions, true);

if (empty($conditions) || json_last_error() !== JSON_ERROR_NONE || count($decodedConditions) === 0) {
    $hook->addError('product', 'Conditions must be set as valid JSON and not empty.');
    return false;
}

$message = $modx->getObject('NotifyStockMessage', $messageId);
if (!$message) {
    $hook->addError('product', 'Invalid message ID.');
    return false;
}

if (!$email || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $hook->addError('email', $modx->lexicon('commerce_notifystock.form.email_error'));
    return false;
}

if (!$productId) {
    $hook->addError('product', $modx->lexicon('commerce_notifystock.form.product_error_nf'));
    return false;
}

$product = $modx->getObject('comProduct', $productId);
if (!$product) {
    $hook->addError('product', $modx->lexicon('commerce_notifystock.form.product_error_nf'));
    return false;
}

/** @var \NotifyStockRequest $notifyRequest */
$notifyRequest = $modx->newObject('NotifyStockRequest');
$notifyRequest->fromArray([
    'user' => $userId,
    'message' => $messageId,
    'conditions' => $decodedConditions,
    'email' => $email,
    'product' => $productId,
    'added_on' => time(),
]);

return $notifyRequest->save();
