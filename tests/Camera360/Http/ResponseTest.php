<?php
namespace Tests\Camera360\Http;

use PHPUnit\Framework\TestCase;
use Camera360\Http\Response;

/**
 * Response test case.
 */
class ResponseTest extends TestCase
{
    
    /**
     *
     * @var Response
     */
    private $response;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $body = '{"key":"test","token":"test"}';
        $headers = array(
            'Content-Type' => 'application/json',
        );
        $this->response = new Response();
        $this->response->setHttpcode(200)
        ->setHeaders($headers)
        ->setBody($body);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->response = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests Response->getData()
     */
    public function testGetData()
    {
        $actual = $this->response->getData();
        $this->assertTrue(is_array($actual));
        $expected = array(
            'key' => 'test',
            'token' => 'test',
        );
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests Response->ok()
     */
    public function testOk()
    {
        $this->assertTrue($this->response->ok());
    }
    
    /**
     * Tests Response->ok()
     */
    public function testOkFalse()
    {
        $this->response->setHttpcode(300);
        $this->assertFalse($this->response->ok());
        $this->response->setHttpcode(400);
        $this->assertFalse($this->response->ok());
        $this->response->setHttpcode(500);
        $this->assertFalse($this->response->ok());
    }
    
    /**
     * Tests Response->getMessage()
     */
    public function testGetMessage()
    {
        $body = '{"message":"error"}';
        $this->response->setBody($body);
        $actual = $this->response->getMessage();
        $this->assertEquals('error', $actual);
    }
}
