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

    public function updateConsent($email, $consent, $service){
        if (!$this->tableName) return;

        $date = new \DateTime('now');
        $newData = [
            'M' => [
                'consent' => ['BOOL' => $consent],
                'date' => ['S' => $date->format('Y-m-d H:i:s')],
            ],
        ];

        $key = [
            'email' => ['S' => $email]
        ];

        $getResponse = $this->dynamoDbClient->getItem([
            'TableName' => $this->tableName,
            'Key' => $key
        ]);
        if (!isset($getResponse['Item'])) {
            $data['email'] = ['S' => $email];
        } else {
            $data = $getResponse['Item'];
        }
        $data[$service] = $newData;

        $this->dynamoDbClient->putItem([
            'TableName' => 'consent',
            'Item' => $data
        ]);
    }
}
