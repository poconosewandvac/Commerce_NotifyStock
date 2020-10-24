<?php

declare(strict_types=1);

namespace PoconoSewVac\NotifyStock\Cron;

use PoconoSewVac\NotifyStock\Repositories\RequestRepository;

/**
 * Class ScheduledRunner
 * @package PoconoSewVac\NotifyStock\Cron
 */
class ScheduledRunner implements Runnable
{
    /**
     * @var \Commerce
     */
    protected $commerce;

    /**
     * @var \modmore\Commerce\Adapter\AdapterInterface|\modmore\Commerce\Adapter\Revolution
     */
    protected $adapter;

    /**
     * Runner constructor.
     * @param \Commerce $commerce
     */
    public function __construct(\Commerce $commerce)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
    }

    public function run()
    {
        $requestRepository = new RequestRepository($this->commerce);
        $pendingRequests = $requestRepository->getPending();

        foreach ($pendingRequests as $request) {
            if ($request->hasConditions() && $request->conditionsMet()) {
                if ($request->send()) {
                    $this->adapter->log(4, "[NotifyStock] Email sent successfully for notify request {$request->get('id')}");

                    $request->markCompleted();
                    $request->save();
                } else {
                    $this->adapter->log(1, "[NotifyStock] Could not send email for notify request {$request->get('id')}, will retry.");
                }
            }
        }
    }
}