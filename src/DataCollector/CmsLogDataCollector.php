<?php

namespace Efrogg\ContentRenderer\DataCollector;

use Efrogg\ContentRenderer\Log\CmsLogger;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CmsLogDataCollector extends AbstractDataCollector
{

    public function __construct(private readonly CmsLogger $logger)
    {
    }


    /**
     * @param Request        $request
     * @param Response       $response
     * @param Throwable|null $exception
     *
     * @return void
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data = [
            'logs' => $this->logger->getLogs()
        ];
    }

    public function getLogs()
    {
        return $this->data['logs'];
    }

}
