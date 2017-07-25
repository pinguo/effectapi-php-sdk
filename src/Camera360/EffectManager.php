<?php
namespace Camera360;

use Qiniu\Storage\UploadManager;
use Camera360\Http\Client;

/**
 * 特效处理类
 * 
 * 特效处理步骤：
 * 1. 构造授权类 Authorization
 * 2. 构造特效处理类 EffectManager
 * 3. 调用特效处理类的上传图片接口 upload | uploadFile
 * 4. 调用特效处理类的增加特效滤镜接口 addFilter
 * 
 * 注意：在第3步调用特效处理类的上传图片接口时，传入滤镜相关参数，会自动触发特效处理，并返回特效图.
 * 
 * @example 
 * use Camera360\Authorization;
 * use Camera360\EffectManager;
 * 
 * $authorization = new Authorization($accessKey, $secretKey);
 * $effectManager = new EffectManager($authorization);
 * $uploadRet = $effectManager->upload($image);
 * // $uploadRet = $effectManager->uploadFile($filePath);
 * $effectPicUrl = $effectManager->addFilter($uploadRet['key'], $filter);
 * 
 * @author zhanglu <zhanglu@camera360.com>
 *
 */
final class EffectManager
{
    /**
     * @var Authorization
     */
    private $_authorization;
    
    public $key;
    
    /**
     * @param Authorization $authorization 授权验证实例
     */
    public function __construct(Authorization $authorization)
    {
        $this->_authorization = $authorization;
    }
    
    /**
     * 上传图片的二进制流，上传成功后返回图片的唯一 Key，后续进行特效处理时作为传入参数。
     * 如果设置有效的 $filter 参数，那么上传图片后会自动触发特效处理流程。
     * 注意：上传的原图和特效图在服务器保存 1 天后随即删除
     * 
     * @param string $data          上传二进制流
     * @param string $filter        滤镜名称
     * @param integer $strength     滤镜强度，取值范围 0 - 100
     * @param integer $rotateAngle  旋转角度，取值范围 0 - 360
     * @param boolean $mirrorX      水平翻转
     * @param boolean $mirrorY      垂直翻转
     * @throws \Exception
     * @return array                "key" 是标识原图的Key；"effect"是特效图的私有下载地址，如果没有设置 $filter 参数，"effect"为空。
     *                              array(
     *                                  "key" => "<Origin picture key string>",
     *                                  "effect" => "<Effect picture download url>",
     *                              )
     */
    public function upload($data, $filter = null, $strength = 100, $rotateAngle = 0, $mirrorX = false, $mirrorY = false)
    {
        $params = null;
        $uploadOnly = true;
        if (!empty($filter)) {
            $params = $this->makeFilterParams($filter, $strength, $rotateAngle, $mirrorX, $mirrorY);
            $uploadOnly = false;
        }
        $uploadToken = $this->_authorization->uploadToken($uploadOnly);
        $this->key = $uploadToken->key;
        
        $uploadMgr = new UploadManager();
        /**
         * @var \Qiniu\Http\Error $err
         */
        list($result, $err) = $uploadMgr->put($uploadToken->token, $uploadToken->key, $data, $params);
        if ($err !== null) {
            throw new \Exception('Upload error. error: ' . $err->message() . ', errno: ' . $err->code());
        }
        $ret = array(
            'key'       => $uploadToken->key,
            'effect'    => $result['url'],
        );
        
        return $ret;
    }
    
    /**
     * 上传图片文件，上传成功后返回图片的唯一 Key，后续进行特效处理时作为传入参数。
     * 如果设置有效的 $filter 参数，那么上传图片后会自动触发特效处理流程。
     * 注意：上传的原图和特效图在服务器保存 1 天后随即删除
     *
     * @param string $data          上传文件的路径
     * @param string $filter        滤镜名称
     * @param integer $strength     滤镜强度，取值范围 0 - 100
     * @param integer $rotateAngle  旋转角度，取值范围 0 - 360
     * @param boolean $mirrorX      水平翻转
     * @param boolean $mirrorY      垂直翻转
     * @throws \Exception
     * @return array                "key" 是标识原图的Key；"effect"是特效图的私有下载地址，如果没有设置 $filter 参数，"effect"为空。
     *                              array(
     *                                  "key" => "<Origin picture key string>",
     *                                  "effect" => "<Effect picture download url>",
     *                              )
     */
    public function uploadFile($filePath, $filter = null, $strength = 100, $rotateAngle = 0, $mirrorX = false, $mirrorY = false)
    {
        $params = null;
        $uploadOnly = true;
        if (!empty($filter)) {
            $params = $this->makeFilterParams($filter, $strength, $rotateAngle, $mirrorX, $mirrorY);
            $uploadOnly = false;
        }
        $uploadToken = $this->_authorization->uploadToken($uploadOnly);
        $this->key = $uploadToken->key;
        
        $uploadMgr = new UploadManager();
        /**
         * @var \Qiniu\Http\Error $err
         */
        list($result, $err) = $uploadMgr->putFile($uploadToken->token, $uploadToken->key, $filePath, $params);
        if ($err !== null) {
            throw new \Exception('Upload error. error: ' . $err->message() . ', errno: ' . $err->code());
        }
        $ret = array(
            'key'       => $uploadToken->key,
            'effect'    => $result['url'],
        );
        
        return $ret;
    }
    
    /**
     * 给图片增加特效滤镜
     * 注意：特效图在服务器保存 1 天后随即删除
     *
     * @param string $key           上传图片接口返回的标识原图的 Key
     * @param string $filter        滤镜名称
     * @param integer $strength     滤镜强度，取值范围 0 - 100
     * @param integer $rotateAngle  旋转角度，取值范围 0 - 360
     * @param boolean $mirrorX      水平翻转
     * @param boolean $mirrorY      垂直翻转
     * @throws \Exception
     * @return string               特效图的私有下载地址
     */
    public function addFilter($key, $filter, $strength = 100, $rotateAngle = 0, $mirrorX = false, $mirrorY = false)
    {
        $query = $this->makeFilterParams($filter, $strength, $rotateAngle, $mirrorX, $mirrorY);
        $body = http_build_query($query);
        $url = Conf::HOST . '/pics/' . $key . '/effects';
        $contentType = 'application/x-www-form-urlencoded';
        
        $authHeaders = $this->_authorization->doAuth($url, $body, $contentType);
        $response = Client::post($url, $body, $authHeaders);
        if (!$response->ok()) {
            throw new \Exception($response->getMessage(), $response->getHttpcode());
        }
        $result = $response->getData();
        
        return $result['url'];
    }
    
    private function makeFilterParams($filter, $strength, $rotateAngle, $mirrorX, $mirrorY)
    {
        if (!is_string($filter)) {
            throw new \Exception('argument filter must be string type.');
        }
        if (!is_int($strength)) {
            throw new \Exception('argument strength must be int type.');
        }
        if ($strength < 0 || $strength > 100) {
            throw new \Exception('argument strength must be greater than 0.');
        }
        if (!is_int($rotateAngle)) {
            throw new \Exception('argument rotateAngle must be integer type.');
        }
        if (!is_bool($mirrorX)) {
            throw new \Exception('argument mirrorX must be boolean type.');
        }
        if (!is_bool($mirrorY)) {
            throw new \Exception('argument mirrorY must be boolean type.');
        }
        $rotateAngle = $rotateAngle % 360;
        $params = array(
            'x:filter' => $filter,
            'x:strength' => $strength,
            'x:rotateAngle' => $rotateAngle,
            'x:mirrorX' => $mirrorX ? 1 : 0,
            'x:mirrorY' => $mirrorY ? 1 : 0,
        );
        return $params;
    }
}
