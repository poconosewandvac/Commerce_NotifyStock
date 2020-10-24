<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Overview extends Page
{
    public $key = 'notifystock';
    public $title = 'Notify Stock';

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);

        $section->addWidget(new Grid($this->commerce));
        $this->addSection($section);

        return $this;
    }
}
