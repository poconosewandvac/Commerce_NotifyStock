<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Cron;

/**
 * Interface Runnable
 * @package PoconoSewVac\NotifyStock\Cron
 */
interface Runnable
{
    public function run();
}