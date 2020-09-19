<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock;

use modmore\Commerce\Admin\Widgets\Form\ProductField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;
use PoconoSewVac\NotifyStock\Admin\Widgets\Form\ConditionField;

class Form extends FormWidget
{
    protected $classKeyAction = 'notify_stock_request';
    protected $classKey = 'NotifyStockRequest';

    public function getFields(array $options = [])
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'email',
            'label' => $this->adapter->lexicon('commerce_notifystock.email'),
        ]);

        $fields[] = new ProductField($this->commerce, [
            'name' => 'product',
            'label' => $this->adapter->lexicon('commerce.product'),
            'validation' => [new Required()],
        ]);

        $fields[] = new ConditionField($this->commerce, [
            'name' => 'conditions',
            'label' => $this->adapter->lexicon('commerce_notifystock.conditions'),
        ]);

        $fields[] = new SelectField($this->commerce, [
            'name' => 'message',
            'label' => $this->adapter->lexicon('commerce_notifystock.message'),
            'options' => $this->getMessageOptions(),
            'validation' => [new Required()],
        ]);

        return $fields;
    }

    public function getFormAction(array $options = [])
    {
        if ($this->record->get('id')) {
            return $this->adapter->makeAdminUrl('notifystock/update', ['id' => $this->record->get('id')]);
        }

        return $this->adapter->makeAdminUrl('notifystock/create');
    }

    private function getMessageOptions(): array
    {
        $notifyStockMessages = $this->adapter->getCollection('NotifyStockMessage', [
            'removed' => false,
        ]);

        $availableMessages = [];
        $availableMessages[] = [
            'label' => $this->adapter->lexicon('commerce_notifystock.select_message'),
            'value' => '',
        ];

        foreach ($notifyStockMessages as $notifyStockMessage) {
            $availableMessages[] = [
                'label' => $notifyStockMessage->get('name'),
                'value' => $notifyStockMessage->get('id'),
            ];
        }

        return $availableMessages;
    }
}
