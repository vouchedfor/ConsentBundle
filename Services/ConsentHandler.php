<?php

namespace VouchedFor\ConsentBundle\Services;

use Aws\DynamoDb\DynamoDbClient;

class ConsentHandler
{
    const ENCRYPTION_METHOD = 'AES-128-ECB';
    private $dynamoDbClient;
    private $tableName;
    private $password;

    public function __construct(DynamoDbClient $dynamoDbClient, $tableName, $password)
    {
        $this->dynamoDbClient = $dynamoDbClient;
        $this->tableName = $tableName;
        $this->password = $password;
    }

    public function encrypt($string)
    {
        return openssl_encrypt($string, self::ENCRYPTION_METHOD, $this->password);
    }

    public function decrypt($string)
    {
        return openssl_decrypt($string, self::ENCRYPTION_METHOD, $this->password, 0);
    }

    public function update($encryptedEmail, array $consentService)
    {
        if (!$this->tableName) return;

        $email = $this->decrypt($encryptedEmail);

        $key = $this->getKey($email);

        $getResponse = $this->dynamoDbClient->getItem(
            [
                'TableName' => $this->tableName,
                'Key' => $key,
            ]
        );

        $data = isset($getResponse['Item']) ? $getResponse['Item'] : $key;

        foreach ($consentService as $service => $consentChoice) {
            $data[$service] = $this->getConsentData($consentChoice);
        }

        $this->dynamoDbClient->putItem(
            [
                'TableName' => $this->tableName,
                'Item' => $data,
            ]
        );
    }

    private function getDateStringArray($date = 'now')
    {
        $dateObject = new \DateTime($date);

        return ['S' => $dateObject->format('Y-m-d H:i:s')];
    }

    private function getKey($email)
    {
        return [
            'email' => ['S' => $email],
        ];
    }

    private function getConsentString(bool $consentChoice)
    {
        return ['BOOL' => $consentChoice];
    }

    private function getConsentData($consentChoice, $date = 'now')
    {
        return [
            'M' => [
                'consent' => $this->getConsentString($consentChoice),
                'date' => $this->getDateStringArray($date),
            ],
        ];
    }
}
