<?php
namespace Camera360;

/**
 * 上传凭证实体
 * 
 * @author zhanglu <zhanglu@camera360.com>
 *
 */
class UploadToken {
    /**
     * @var string
     */
    public $uphost;
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $token;
    
    public function __construct($data = null)
    {
        if (isset($data['uphost'])) {
            $this->uphost = $data['uphost'];
        }
        if (isset($data['key'])) {
            $this->key = $data['key'];
        }
        if (isset($data['token'])) {
            $this->token = $data['token'];
        }
    }
}

