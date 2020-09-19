<?php

declare(strict_types=1);

use modmore\Commerce\Traits\SoftDelete;

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
}
