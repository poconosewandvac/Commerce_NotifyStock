<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Create extends Page
{
    public $key = 'notifystock/create';
    public $title = 'commerce_notifystock.add_request';

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title,
        ]);

        $section->addWidget((new Form($this->commerce, [
            'id' => 0,
            'added_on' => date('Y-m-d H:i:s')
        ]))->setUp());
        $this->addSection($section);

        return $this;
    }
}
