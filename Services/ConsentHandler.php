<?php

namespace VouchedFor\ConsentBundle\Services;

use Aws\DynamoDb\DynamoDbClient;

class ConsentHandler
{
    private $dynamoDbClient;
    private $url;

    public function __construct(DynamoDbClient $dynamoDbClient, $url)
    {
        $this->dynamoDbClient = $dynamoDbClient;
        $this->url = $url;
    }

    public function decode($token)
    {
        return $token . '-' . $this->url;
    }
}
