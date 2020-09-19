<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;
use modmore\Commerce\Admin\Widgets\TextWidget;

class Delete extends Page
{
    public $key = 'notifystock/messages/delete';
    public $title = 'commerce_notifystock.delete_message';

    public function setUp()
    {
        $requestId = $this->getOption('id', 0);
        $request = $this->adapter->getObject('NotifyStockMessage', ['id' => $requestId]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title,
        ]);

        if ($request) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce.delete',
            ]);

            $widget->setRecord($request);
            $widget->setClassKey('NotifyStockRequest');
            $widget->setFormAction($this->adapter->makeAdminUrl('notifystock/messages/delete', ['id' => $request->get('id')]));
            $widget->setUp();
        } else {
            $widget = (new TextWidget($this->commerce, ['text' => 'Notify stock message not found.']))->setUp();
        }

        $section->addWidget($widget);
        $this->addSection($section);

        return $this;
    }
}
