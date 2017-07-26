<?php
namespace Tests\Camera360\Http;

use PHPUnit\Framework\TestCase;
use Camera360\Http\Client;

/**
 * Client test case.
 */
class ClientTest extends TestCase
{
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    /**
     * Tests Client::get()
     */
    public function testGet()
    {
        $url = 'https://sdk.camera360.com';
        $response = Client::get($url);
        $this->assertEquals(200, $response->getHttpcode());
    }
    
    /**
     * Tests Client::get()
     */
    public function testGetForNotFound()
    {
        $url = 'https://sdk.c360dn.com/notfound';
        $response = Client::get($url);
        $this->assertEquals(404, $response->getHttpcode());
    }
    
    /**
     * Tests Client::post()
     */
    public function testPost()
    {
        $url = 'https://baidu.com';
        $response = Client::post($url);
        $this->assertEquals(200, $response->getHttpcode());
    }
    
    /**
     * Tests Client::post()
     */
    public function testPostNotFound()
    {
        $url = 'https://sdk.c360dn.com/notfound';
        $response = Client::post($url);
        $this->assertEquals(404, $response->getHttpcode());
    }
}
