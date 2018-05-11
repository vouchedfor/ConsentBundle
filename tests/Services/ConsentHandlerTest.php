<?php
namespace Tests\ConsentBundle\Services;

use Aws\DynamoDb\DynamoDbClient;
use PHPUnit\Framework\TestCase;
use VouchedFor\ConsentBundle\Services\ConsentHandler;

class ConsentHandlerTest extends TestCase
{
    public function testEncode() {
        $this->markTestSkipped();
        $dynamoDbClient = $this->getMockBuilder(DynamoDbClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $consentHandler = new ConsentHandler($dynamoDbClient, 'abc');

        $this->assertEquals('banana-abc', $consentHandler->decode('banana'));
    }
}