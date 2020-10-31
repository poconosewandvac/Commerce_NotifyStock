<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Repositories;

class MessageRepository extends AbstractRepository
{
    public const CLASS_KEY = 'NotifyStockMessage';

    /**
     * Get all available messages
     *
     * @return \NotifyStockMessage[]|null
     */
    public function getMessages()
    {
        return $this->adapter->getCollection(self::CLASS_KEY, [
            'removed' => false,
        ]);
    }
}