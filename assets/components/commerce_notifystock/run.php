<?php

/**
 * Initializes the Notify Stock cron
 * This file is what actually initiates messages sending to customers
 *
 * Set up this file to run in your web crontab or with a web cronjob service
 */

// Load MODX
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH.'config/' .MODX_CONFIG_KEY. '.inc.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');

// For added security, check if the script is being run from the CLI (cron)
// This only runs when the commerce_notifystock.web_cron setting is set to FALSE
$allowWebRun = (bool) $modx->getOption('commerce_notifystock.web_cron', null, true);

if (!$allowWebRun && php_sapi_name() !== 'cli') {
    http_response_code(406); // not acceptable

    echo json_encode([
        'success' => false,
        'message' => 'Cannot be run from web.',
    ]);

    die();
}

// Load Commerce
$commercePath = $modx->getOption('commerce.core_path', null, $modx->getOption('core_path') . 'components/commerce/') . 'model/commerce/';
/** @var \Commerce $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $commercePath, ['mode' => $modx->getOption('commerce.mode')]);

// Load NotifyStock
$corePath = $modx->getOption('commerce_notifystock.core_path', null, $modx->getOption('core_path') . 'components/commerce_notifystock');
require_once $corePath . '/vendor/autoload.php';

$scheduledRunner = new \PoconoSewVac\NotifyStock\Cron\ScheduledRunner($commerce);
$scheduledRunner->run();
