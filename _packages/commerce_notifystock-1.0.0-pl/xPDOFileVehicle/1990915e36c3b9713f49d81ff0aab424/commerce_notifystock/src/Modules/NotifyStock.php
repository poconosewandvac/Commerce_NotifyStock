<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Modules;

use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu as TopNavMenuEvent;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class NotifyStock extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_notifystock:default');
        return $this->adapter->lexicon('commerce_notifystock');
    }

    public function getAuthor()
    {
        return 'Tony Klapatch';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_notifystock.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_notifystock:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $root = dirname(__DIR__, 2);
        $path = $root . '/model/';
        $this->adapter->loadPackage('commerce_notifystock', $path);

        // Add template path to twig
        $root = dirname(__DIR__, 2);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'loadPages']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'loadMenuItem']);

        // Add composer libraries to the about section (v0.12+)
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_LOAD_ABOUT, [$this, 'addLibrariesToAbout']);
    }

    public function loadPages(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();

        $generator->addPage('notifystock', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Overview::class);
        $generator->addPage('notifystock/delete', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Delete::class);
        $generator->addPage('notifystock/update', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Update::class);
        $generator->addPage('notifystock/create', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Create::class);

        $generator->addPage('notifystock/messages', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages\Overview::class);
        $generator->addPage('notifystock/messages/delete', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages\Delete::class);
        $generator->addPage('notifystock/messages/update', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages\Update::class);
        $generator->addPage('notifystock/messages/create', \PoconoSewVac\NotifyStock\Admin\Modules\NotifyStock\Messages\Create::class);
    }

    public function loadMenuItem(TopNavMenuEvent $event)
    {
        $items = $event->getItems();

        $items = $this->insertInArray($items, ['notifystock' => [
            'name' => $this->adapter->lexicon('commerce_notifystock'),
            'key' => 'notifystock',
            'icon' => 'icon icon-bell',
            'link' => $this->adapter->makeAdminUrl('notifystock'),
            'submenu' => [
                [
                    'name' => $this->adapter->lexicon('commerce_notifystock.requests'),
                    'key' => 'notifystock',
                    'icon' => 'icon icon-bell',
                    'link' => $this->adapter->makeAdminUrl('notifystock'),
                ],
                [
                    'name' => $this->adapter->lexicon('commerce_notifystock.messages'),
                    'key' => 'notifystock/messages',
                    'icon' => 'icon envelope',
                    'link' => $this->adapter->makeAdminUrl('notifystock/messages'),
                ],
            ]
        ]], 3);

        $event->setItems($items);
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        // A more detailed description to be shown in the module configuration. Note that the module description
        // ({@see self:getDescription}) is automatically shown as well.
//        $fields[] = new DescriptionField($this->commerce, [
//            'description' => $this->adapter->lexicon('commerce_notifystock.module_description'),
//        ]);

        return $fields;
    }

    public function addLibrariesToAbout(PageEvent $event)
    {
        $lockFile = dirname(__DIR__, 2) . '/composer.lock';
        if (file_exists($lockFile)) {
            $section = new SimpleSection($this->commerce);
            $section->addWidget(new ComposerPackages($this->commerce, [
                'lockFile' => $lockFile,
                'heading' => $this->adapter->lexicon('commerce.about.open_source_libraries') . ' - ' . $this->adapter->lexicon('commerce_notifystock'),
                'introduction' => '', // Could add information about how libraries are used, if you'd like
            ]));

            $about = $event->getPage();
            $about->addSection($section);
        }
    }

    /**
     * @param $array
     * @param $values
     * @param $offset
     * @return array
     */
    private function insertInArray($array, $values, $offset) {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }
}
