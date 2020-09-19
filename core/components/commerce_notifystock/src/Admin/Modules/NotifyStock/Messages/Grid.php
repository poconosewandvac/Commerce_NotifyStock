<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;

class Grid extends GridWidget
{
    public $key = 'notifystock\messages';
    public $defaultSort = 'id';

    public function getItems(array $options = [])
    {
        $items = [];

        $q = $this->adapter->newQuery('NotifyStockMessage');
        $q->where(['NotifyStockMessage.removed' => 0]);

        if (array_key_exists('search', $options) && strlen($options['search'])) {
            $q->where([
                'NotifyStockMessage.name:LIKE' => '%' . $options['search_by_name'] . '%',
                'NotifyStockMessage.subject:LIKE' => '%' . $options['search_by_name'] . '%',
            ]);
        }

        $count = $this->adapter->getCount('NotifyStockMessage', $q);
        $this->setTotalCount($count);

        $q->limit($options['limit'], $options['start']);
        $collection = $this->adapter->getCollection('NotifyStockMessage', $q);
        foreach ($collection as $object) {
            $items[] = $this->prepareItem($object);
        }

        return $items;
    }

    public function getColumns(array $options = [])
    {
        return [
            new Column('name', $this->adapter->lexicon('commerce_notifystock.name'), true),
            new Column('subject', $this->adapter->lexicon('commerce_notifystock.subject'), true),
            new Column('from', $this->adapter->lexicon('commerce_notifystock.from'), true),
        ];
    }

    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-message',
            'title' => $this->adapter->lexicon('commerce_notifystock.add_message'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('notifystock/messages/create'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'icon-plus',
            'modal_title' => $this->adapter->lexicon('commerce_notifystock.add_message'),
            'position' => 'top'
        ];

        $toolbar[] = [
            'name' => 'search',
            'title' => $this->adapter->lexicon('commerce_notifystock.search'),
            'type' => 'textfield',
            'value' => array_key_exists('search_by_name', $options) ? (int)$options['search'] : '',
            'position' => 'top',
            'width' => 'six wide'
        ];

        $toolbar[] = [
            'name' => 'limit',
            'title' => $this->adapter->lexicon('commerce.limit'),
            'type' => 'textfield',
            'value' => (int) $options['limit'],
            'position' => 'bottom',
            'width' => 'two wide',
        ];

        return $toolbar;
    }

    public function prepareItem(\NotifyStockMessage $notifyStockMessage): array
    {
        $item = $notifyStockMessage->toArray('', false, true);

        $item['actions'] = [];

        $editUrl = $this->adapter->makeAdminUrl('notifystock/messages/update', ['id' => $item['id']]);
        $item['actions'][] = (new Action())
            ->setUrl($editUrl)
            ->setTitle($this->adapter->lexicon('commerce_notifystock.update'))
            ->setIcon('icon-edit');

        $deleteUrl = $this->adapter->makeAdminUrl('notifystock/messages/delete', ['id' => $item['id']]);
        $item['actions'][] = (new Action())
            ->setUrl($deleteUrl)
            ->setTitle($this->adapter->lexicon('commerce_notifystock.delete'))
            ->setIcon('icon-trash');

        return $item;
    }

    public function render(array $phs)
    {
        return $this->commerce->view()->render('admin/widgets/grid.twig', $phs);
    }
}
