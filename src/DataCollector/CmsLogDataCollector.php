<?php

namespace Efrogg\ContentRenderer\DataCollector;

use Efrogg\ContentRenderer\Log\CmsLogger;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CmsLogDataCollector extends AbstractDataCollector
{

    /** @var CmsLogger */
    private $logger;

    /**
     * @param CmsLogger $storybloklogger
     */
    public function __construct(CmsLogger $storybloklogger)
    {
        $this->logger = $storybloklogger;
    }


    public function collect(Request $request, Response $response, \Throwable $exception = null)
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
