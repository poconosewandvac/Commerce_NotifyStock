<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Section as BaseSection;

class Section extends BaseSection
{
    public function setUp()
    {
        return $this;
    }

    public function getTitle()
    {
        return $this->adapter->lexicon('commerce_notifystock.messages');
    }
}
