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

    public function get($encryptedEmail)
    {
        if (!$this->tableName) return false;

        $key = $this->getKey($encryptedEmail);
        if (!$key['email']['S']) return false;

        $data = $this->getData($key);

        if (!$data['Item']) return false;

        $response = [];

        foreach ($data['Item'] as $key => $value) {
            if ($key == 'email') {
                $response[$key] = $value['S'];
            } else {
                $response[$key] = ['date' => $value['M']['date']['S'], 'consent' => $value['M']['consent']['BOOL']];
            }
        }

        return $response;
    }

    public function update($encryptedEmail, $date, array $consentService, $id = null)
    {
        if (!$this->tableName) return;

        $key = $this->getKey($encryptedEmail);
        if (!$key['email']['S']) return;

        $getResponse = $this->getData($key);

        $data = isset($getResponse['Item']) ? $getResponse['Item'] : $key;

        foreach ($consentService as $service => $consentChoice) {
            $data[$service] = $this->getConsentData($consentChoice, $date);
        }

        if ($id) {
            $data['id'] = $this->getId($id);
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

    private function getKey($encryptedEmail)
    {
        return [
            'email' => ['S' => $this->decrypt($encryptedEmail)],
        ];
    }

    private function getId($id)
    {
        return ['N' => $id];
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

    private function getData($key)
    {
        return $this->dynamoDbClient->getItem(
            [
                'TableName' => $this->tableName,
                'Key' => $key,
            ]
        );
    }
}
