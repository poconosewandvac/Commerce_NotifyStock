<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Overview extends Page
{
    public $key = 'notifystock/messages';
    public $title = 'Notify Stock Messages';

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
