<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Repositories;

use modmore\Commerce\Adapter\AdapterInterface;

class AbstractRepository
{
    /**
     * @var \Commerce
     */
    protected $commerce;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * AbstractRepository constructor.
     * @param \Commerce $commerce
     */
    public function __construct(\Commerce $commerce)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
    }
}