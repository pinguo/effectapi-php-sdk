<?php
namespace Tests\Camera360;

use PHPUnit\Framework\TestCase;
use Camera360\EffectManager;
use Camera360\Authorization;
use Camera360\Http\Client;

/**
 * EffectManager test case.
 */
class EffectManagerTest extends TestCase
{
    
    /**
     *
     * @var EffectManager
     */
    private $effectManager;
    
    private static $key;
    
    private static $effectPicUrl;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        global $accessKey;
        global $secretKey;
        
        $authorization = new Authorization($accessKey, $secretKey);
        $this->effectManager = new EffectManager($authorization);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->effectManager = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests EffectManager->upload()
     */
    public function testUploadWithoutFilter()
    {
        global $filepath;
        
        $data = file_get_contents($filepath);
        $actual = $this->effectManager->upload($data);
        $this->assertNotEmpty($actual['key']);
        $this->assertEmpty($actual['effect']);
        self::$key = $actual['key'];
    }
    
    /**
     * Tests EffectManager->addFilter()
     *
     * @dependstestUploadWithoutFilter
     */
    public function testAddFilter()
    {
        global $filter;
        
        $actual = $this->effectManager->addFilter(self::$key, $filter);
        $this->assertNotNull($actual);
        $this->assertNotFalse(parse_url($actual));
        self::$effectPicUrl = $actual;
    }
    
    /**
     * Tests Client::get()
     *
     * @testAddFilter
     */
    public function testEffectPic()
    {
        $response = Client::get(self::$effectPicUrl);
        $this->assertTrue($response->ok());
        $this->assertNotEmpty($response->getBody());
        $headers = $response->getHeaders();
        $this->assertEquals('image/jpeg', $headers['Content-Type']);
    }
    
    /**
     * Tests EffectManager->upload()
     */
    public function testUploadWithFilter()
    {
        global $filepath;
        global $filter;
        
        $data = file_get_contents($filepath);
        $actual = $this->effectManager->upload($data, $filter);
        $this->assertNotEmpty($actual['key']);
        $this->assertNotEmpty($actual['effect']);
    }
    
    /**
     * Tests EffectManager->uploadFile()
     */
    public function testUploadFileWithoutFilter()
    {
        global $filepath;
        
        $actual = $this->effectManager->uploadFile($filepath);
        $this->assertNotEmpty($actual['key']);
        $this->assertEmpty($actual['effect']);
    }
    
    /**
     * Tests EffectManager->uploadFile()
     */
    public function testUploadFileWithFilter()
    {
        global $filepath;
        global $filter;
        
        $actual = $this->effectManager->uploadFile($filepath, $filter);
        $this->assertNotEmpty($actual['key']);
        $this->assertNotEmpty($actual['effect']);
    }
}
