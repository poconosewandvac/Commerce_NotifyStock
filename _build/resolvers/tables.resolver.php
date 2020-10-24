<?php

/* @var modX $modx */

if ($transport->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $transport->xpdo;

            $corePath = $modx->getOption('commerce.core_path', null, $modx->getOption('core_path') . 'components/commerce/');
            $commerce =& $modx->getService('commerce', 'Commerce', $corePath . 'model/commerce/' , ['isSetup' => true]);

            $path = MODX_CORE_PATH . 'components/commerce_notifystock/model/';
            if (!$commerce->adapter->loadPackage('commerce_notifystock', $path)) {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load commerce_notifystock model');
            }

            $manager = $modx->getManager();
            $logLevel = $modx->setLogLevel(modX::LOG_LEVEL_ERROR);

            $objects = [
                'NotifyStockMessage',
                'NotifyStockRequest',
            ];

            foreach ($objects as $obj) {
                $manager->createObjectContainer($obj);
            }

            $modx->setLogLevel(modX::LOG_LEVEL_FATAL);

            break;
    }
}

return true;
