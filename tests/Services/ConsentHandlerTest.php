<?php
namespace Tests\ConsentBundle\Services;

use Aws\DynamoDb\DynamoDbClient;
use PHPUnit\Framework\TestCase;
use VouchedFor\ConsentBundle\Services\ConsentHandler;

class ConsentHandlerTest extends TestCase
{
    /* @var \VouchedFor\ConsentBundle\Services\ConsentHandler $consentHandler */
    private $consentHandler;

    public function setUp()
    {
        $dynamoDbClient = $this->getMockBuilder(DynamoDbClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consentHandler = new ConsentHandler($dynamoDbClient, 'abc', 'secret password');

        parent::setUp();
    }

    public function testGet() {
        $this->assertFalse($this->consentHandler->get('FlEmMB7ZTgpxfDJYKYQEDw=='));
    }

    public function testUpdate() {
        $services = [
            'Elephant' => true,
            'Kangaroo' => false,
        ];

        $this->assertNull($this->consentHandler->update('test@test.com', '2016-01-01 12:13:31', $services));
        $this->assertNull($this->consentHandler->update('test@test.com', '2016-01-01 12:13:31', $services, 12345));
    }

    public function testEncrypt() {
        $this->assertEquals('FlEmMB7ZTgpxfDJYKYQEDw==', $this->consentHandler->encrypt('info@test.com'));
    }

    public function testDecrypt() {
        $this->assertEquals('info@test.com', $this->consentHandler->decrypt('FlEmMB7ZTgpxfDJYKYQEDw=='));
    }


    public function testGetDateStringArray()
    {
        $this->assertEquals(['S' => '2017-05-01 12:13:33'], $this->invokeMethod($this->consentHandler, 'getDateStringArray', ['2017-05-01 12:13:33']));
    }

    public function testGetKey()
    {
        $this->assertEquals(['email' => ['S' => 'info@test.com']], $this->invokeMethod($this->consentHandler, 'getKey', ['FlEmMB7ZTgpxfDJYKYQEDw==']));
    }


    public function testGetId()
    {
        $this->assertEquals(['id' => ['N' => 12345]], $this->invokeMethod($this->consentHandler, 'getId', ['12345']));
    }

    public function testGetKeyConsentString()
    {
        $this->assertEquals(['BOOL' => true], $this->invokeMethod($this->consentHandler, 'getConsentString', [true]));
        $this->assertEquals(['BOOL' => false], $this->invokeMethod($this->consentHandler, 'getConsentString', [false]));
    }

    public function testGetConsentData()
    {
        $this->assertEquals([
            'M' => [
                'consent' => ['BOOL' => true],
                'date' => ['S' => '2017-05-01 12:13:33'],
            ],
        ], $this->invokeMethod($this->consentHandler, 'getConsentData', [true, '2017-05-01 12:13:33']));

        $this->assertEquals([
            'M' => [
                'consent' => ['BOOL' => false],
                'date' => ['S' => '2017-05-01 12:13:33'],
            ],
        ], $this->invokeMethod($this->consentHandler, 'getConsentData', [false, '2017-05-01 12:13:33']));

        $this->assertEquals([
            'M' => [
                'consent' => ['BOOL' => false],
                'date' => ['S' => '2017-05-01 12:13:33'],
            ],
        ], $this->invokeMethod($this->consentHandler, 'getConsentData', [false, '2017-05-01 12:13:33']));
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}