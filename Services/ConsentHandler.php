<?php

namespace VouchedFor\ConsentBundle\Services;

use Aws\DynamoDb\DynamoDbClient;

class ConsentHandler
{
    private $dynamoDbClient;
    private $tableName;

    public function __construct(DynamoDbClient $dynamoDbClient, $tableName)
    {
        $this->dynamoDbClient = $dynamoDbClient;
        $this->tableName = $tableName;
    }

    public function decode($token)
    {
        return $token . '-' . $this->tableName;
    }
}
