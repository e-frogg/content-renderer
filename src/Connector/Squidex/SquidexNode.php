<?php


namespace Efrogg\ContentRenderer\Connector\Squidex;

use DateTime;

/**
 * Class SquidexNode
 * @package Efrogg\ContentRenderer\Connector\Squidex
 *
 */
class SquidexNode
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        /*
          "id" => "3453d6c7-0b79-4ee6-a02e-8cb294bd1baf"
          "createdBy" => "subject:5f180ab631488e0001cf2748"
          "lastModifiedBy" => "subject:5f180ab631488e0001cf2748"
          "data" => array:11 [▶]
          "created" => "2020-07-22T14:50:44Z"
          "lastModified" => "2020-07-23T04:35:04Z"
          "status" => "Published"
          "statusColor" => "#4bb958"
          "schemaName" => "page"
          "schemaDisplayName" => "page"
          "version" => 6
         */
//        dd($this->data);
    }


    public function getId():string
    {
        // ex : 3453d6c7-0b79-4ee6-a02e-8cb294bd1baf
        return $this->data['id'];
    }

    public function getCreatedBy():string
    {
        // ex : subject:5f180ab631488e0001cf2748
        return $this->data['createdBy'];
    }

    public function getLastModifiedBy():string
    {
        // ex : subject:5f180ab631488e0001cf2748
        return $this->data['lastModifiedBy'];
    }

    public function getData():array
    {
        return $this->data['data'];
    }
    public function getContext():array
    {
        $context = $this->data;
        unset($context['data'], $context['_links']);
        return $context;
    }

    public function getCreated(): DateTime
    {
        // ex : 2020-07-22T14:50:44Z
        return new DateTime($this->data['created']);
    }

    public function getLastModified(): DateTime
    {
        // ex : 2020-07-23T04:35:04Z
        return new DateTime($this->data['lastModified']);
    }

    public function getStatus():string
    {
        // ex : Published
        return $this->data['status'];
    }

    public function getStatusColor():string
    {
        // ex : #4bb958
        return $this->data['statusColor'];
    }

    public function getSchemaName():string
    {
        // ex : page
        return $this->data['schemaName'];
    }

    public function getSchemaDisplayName():string
    {
        // ex : page
        return $this->data['schemaDisplayName'];
    }

    public function getVersion():int
    {
        // ex : 6
        return (int)$this->data['version'];
    }


}