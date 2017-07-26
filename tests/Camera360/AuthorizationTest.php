<?php
namespace Tests\Camera360;

use PHPUnit\Framework\TestCase;
use Camera360\Authorization;

/**
 * Authorization test case.
 */
class AuthorizationTest extends TestCase
{
    
    /**
     *
     * @var Authorization
     */
    private $authorization;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        global $accessKey;
        global $secretKey;
        
        $this->authorization = new Authorization($accessKey, $secretKey);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->authorization = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests Authorization->getAccessKey()
     */
    public function testGetAccessKey()
    {
        global $accessKey;
        
        $actual = $this->authorization->getAccessKey();
        $expected = $accessKey;
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests Authorization->sign()
     */
    public function testSign()
    {
        global $accessKey;
        
        $data = 'test';
        $actual = $this->authorization->sign($data);
        $expected = $accessKey . ':1EDDkG-3vEJlE4VVmFVUSzQ2x5U=';
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests Authorization->signRequest()
     */
    public function testSignRequest()
    {
        global $accessKey;
        
        $urlString = 'https://effectapi.camera360.com/uploadtoken?uploadOnly=1';
        $body = 'test';
        $contentType = 'application/x-www-form-urlencoded';
        
        $actual = $this->authorization->signRequest($urlString, $body);
        $expected = $accessKey . ':4eIyZ_YDwSo9TxvMkkuji6ZAcqk=';
        $this->assertEquals($expected, $actual);
        
        $actual = $this->authorization->signRequest($urlString, $body, $contentType);
        $expected = $accessKey . ':E7GVfhWr6MNN1h7JG-JiZ2SQCYI=';
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests Authorization->verifyCallback()
     */
    public function testVerifyCallback()
    {
        global $accessKey;
        
        $contentType = 'application/x-www-form-urlencoded';
        $originAuthorization = "Camera360 $accessKey:E7GVfhWr6MNN1h7JG-JiZ2SQCYI=";
        $url = 'https://effectapi.camera360.com/uploadtoken?uploadOnly=1';
        $body = 'test';
        $actual = $this->authorization->verifyCallback($contentType, $originAuthorization, $url, $body);
        $expected = true;
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests Authorization->uploadToken()
     */
    public function testUploadToken()
    {
        $actual1 = $this->authorization->uploadToken(true);
        $actual2 = $this->authorization->uploadToken(false);
        $this->assertEquals(0, substr_compare($actual1->token, $actual2->token, 0, 41));
        $this->assertNotEquals($actual1->token, $actual2->token);
    }
    
    /**
     * Tests Authorization->doAuth()
     */
    public function testDoAuth()
    {
        global $accessKey;
        
        $url = 'https://effectapi.camera360.com/uploadtoken?uploadOnly=1';
        $body = 'test';
        $contentType = 'application/x-www-form-urlencoded';
        $actual = $this->authorization->doAuth($url, $body, $contentType);
        $expected = array(
            'Authorization' => "Camera360 $accessKey:E7GVfhWr6MNN1h7JG-JiZ2SQCYI=",
        );
        $this->assertEquals($expected, $actual);
    }
}
