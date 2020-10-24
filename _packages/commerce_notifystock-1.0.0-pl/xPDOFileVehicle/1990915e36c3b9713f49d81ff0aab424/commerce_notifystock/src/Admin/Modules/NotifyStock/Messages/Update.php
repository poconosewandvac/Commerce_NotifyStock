<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Page;

class Update extends Page
{
    public $key = 'notifystock/messages/update';
    public $title = 'commerce_notifystock.update_message';

    public function setUp()
    {
        $objectId = (int) $this->getOption('id', 0);
        $exists = $this->adapter->getCount('NotifyStockMessage', ['id' => $objectId, 'removed' => false]);

        if ($exists) {
            $section = new Section($this->commerce, [
                'title' => $this->title,
            ]);

            $section->addWidget((new Form($this->commerce, ['isUpdate' => true, 'id' => $objectId]))->setUp());
            $this->addSection($section);

            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}
