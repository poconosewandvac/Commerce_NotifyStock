<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Create extends Page
{
    public $key = 'notifystock/messages/create';
    public $title = 'commerce_notifystock.add_message';

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title,
        ]);

        $section->addWidget((new Form($this->commerce, ['id' => 0]))->setUp());
        $this->addSection($section);

        return $this;
    }
}
