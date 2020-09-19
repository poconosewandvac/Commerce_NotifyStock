<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock;

use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;

class Grid extends GridWidget
{
    public $key = 'notifystock';
    public $defaultSort = 'added_on';

    public function getItems(array $options = [])
    {
        $items = [];

        $q = $this->adapter->newQuery('NotifyStockRequest');
        $q->where(['NotifyStockRequest.removed' => 0]);
        $q->leftJoin('modUser', 'User', ['User.id = NotifyStockRequest.user']);
        $q->leftJoin('modUserProfile', 'UserProfile', ['UserProfile.internalKey = User.id']);

        if (array_key_exists('message', $options) && strlen($options['message'])) {
            $q->where([
                'NotifyStockRequest.message' => $options['message'],
            ]);
        }

        if (array_key_exists('completed', $options) && strlen($options['completed'])) {
            $q->where([
                'NotifyStockRequest.completed' => $options['completed'],
            ]);
        }

        if (array_key_exists('search_by_email', $options) && strlen($options['search_by_email']) > 0) {
            $q->where([
                'NotifyStockRequest.email:LIKE' => '%' . $options['search_by_email'] . '%',
                'OR:UserProfile.email:LIKE' => '%' . $options['search_by_email'] . '%',
            ]);
        }

        $count = $this->adapter->getCount('NotifyStockRequest', $q);
        $this->setTotalCount($count);

        $q->limit($options['limit'], $options['start']);
        $collection = $this->adapter->getCollection('NotifyStockRequest', $q);
        foreach ($collection as $object) {
            $items[] = $this->prepareItem($object);
        }

        return $items;
    }

    public function getColumns(array $options = [])
    {
        return [
            new Column('user', $this->adapter->lexicon('commerce_notifystock.user')),
            new Column('message', $this->adapter->lexicon('commerce_notifystock.message'), true),
            new Column('email', $this->adapter->lexicon('commerce_notifystock.email'), true),
            new Column('conditions', $this->adapter->lexicon('commerce_notifystock.conditions')),
            new Column('added_on', $this->adapter->lexicon('commerce_notifystock.added_on'), true),
            new Column('completed', $this->adapter->lexicon('commerce_notifystock.completed'), true, true),
            new Column('completed_on', $this->adapter->lexicon('commerce_notifystock.completed_on'), true),
        ];
    }

    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-request',
            'title' => $this->adapter->lexicon('commerce_notifystock.add_request'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('notifystock/create'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'icon-plus',
            'modal_title' => $this->adapter->lexicon('commerce_notifystock.add_request'),
            'position' => 'top'
        ];

        $toolbar[] = [
            'name' => 'search_by_email',
            'title' => $this->adapter->lexicon('commerce_notifystock.search_by_email'),
            'type' => 'textfield',
            'value' => array_key_exists('search_by_email', $options) ? (int)$options['search_by_email'] : '',
            'position' => 'top',
            'width' => 'six wide'
        ];

        $toolbar[] = [
            'name' => 'completed',
            'title' => $this->adapter->lexicon('commerce_notifystock.completed'),
            'type' => 'select',
            'value' => array_key_exists('completed', $options) ? (int) $options['completed'] : '',
            'options' => [
                [
                    'label' => 'Yes',
                    'value' => 1,
                ],
                [
                    'label' => 'No',
                    'value' => 0,
                ],
            ],
            'position' => 'top',
            'width' => 'three wide',
        ];

        $toolbar[] = [
            'name' => 'message',
            'title' => $this->adapter->lexicon('commerce_notifystock.message'),
            'type' => 'select',
            'value' => array_key_exists('message', $options) ? (int) $options['message'] : '',
            'options' => $this->getMessageOptions(),
            'position' => 'top',
            'width' => 'three wide',
        ];

        $toolbar[] = [
            'name' => 'limit',
            'title' => $this->adapter->lexicon('commerce.limit'),
            'type' => 'textfield',
            'value' => (int)$options['limit'],
            'position' => 'bottom',
            'width' => 'two wide',
        ];

        return $toolbar;
    }

    public function prepareItem(\NotifyStockRequest $notifyStockRequest): array
    {
        $item = $notifyStockRequest->toArray('', false, true);

        if ($item['completed_on'] == 0) {
            $item['completed_on'] = $this->adapter->lexicon('commerce_notifystock.not_applicable');
        }

        if ($item['completed'] == 1) {
            $item['completed'] = '<i class="icon icon-check" title="Completed"></i>';
        } else {
            $item['completed'] = '<i class="icon icon-times" title="Not Completed"></i>';
        }

        $item['actions'] = [];

        $editUrl = $this->adapter->makeAdminUrl('notifystock/update', ['id' => $item['id']]);
        $item['actions'][] = (new Action())
            ->setUrl($editUrl)
            ->setTitle($this->adapter->lexicon('commerce_notifystock.update'))
            ->setIcon('icon-edit');

        $deleteUrl = $this->adapter->makeAdminUrl('notifystock/delete', ['id' => $item['id']]);
        $item['actions'][] = (new Action())
            ->setUrl($deleteUrl)
            ->setTitle($this->adapter->lexicon('commerce_notifystock.delete'))
            ->setIcon('icon-trash');

        return $item;
    }

    private function getMessageOptions()
    {
        $notifyStockMessages = $this->adapter->getCollection('NotifyStockMessage', [
            'removed' => false,
        ]);

        $availableMessages = [];
        foreach ($notifyStockMessages as $notifyStockMessage) {
            $availableMessages[] = [
                'label' => $notifyStockMessage->get('name'),
                'value' => $notifyStockMessage->get('id'),
            ];
        }

        return $availableMessages;
    }

    public function render(array $phs)
    {
        return $this->commerce->view()->render('admin/widgets/grid.twig', $phs);
    }
}
