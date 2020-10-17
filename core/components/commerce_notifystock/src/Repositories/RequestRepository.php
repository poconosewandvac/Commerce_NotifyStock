<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Repositories;

class RequestRepository extends AbstractRepository
{
    public const CLASS_KEY = 'NotifyStockRequest';

    /**
     * Return pending notify requests
     *
     * @return array|\NotifyStockRequest[]|\xPDOObject[]|null
     */
    public function getPending()
    {
        return $this->adapter->getCollection(self::CLASS_KEY, [
            'removed' => false,
            'completed' => false,
        ]);
    }
}