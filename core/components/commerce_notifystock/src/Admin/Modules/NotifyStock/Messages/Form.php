<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Widgets\Form\TextareaField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;
use PoconoSewVac\NotifyStock\Admin\Widgets\Form\Validation\Email;

class Form extends FormWidget
{
    protected $classKeyAction = 'notify_stock_message';
    protected $classKey = 'NotifyStockMessage';

    public function getFields(array $options = [])
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'name',
            'label' => $this->adapter->lexicon('commerce_notifystock.name'),
            'validation' => [new Required()],
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'subject',
            'label' => $this->adapter->lexicon('commerce_notifystock.subject'),
            'validation' => [new Required()],
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'from',
            'label' => $this->adapter->lexicon('commerce_notifystock.from'),
            'validation' => [new Required(), new Email()],
        ]);

        $fields[] = new TextareaField($this->commerce, [
            'name' => 'content',
            'label' => $this->adapter->lexicon('commerce_notifystock.content'),
        ]);

        return $fields;
    }

    public function getFormAction(array $options = [])
    {
        if ($this->record->get('id')) {
            return $this->adapter->makeAdminUrl('notifystock/messages/update', ['id' => $this->record->get('id')]);
        }

        return $this->adapter->makeAdminUrl('notifystock/messages/create');
    }
}
