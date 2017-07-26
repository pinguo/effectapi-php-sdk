<?php
namespace Camera360\Http;

/**
 * HTTP响应类
 *
 * @author zhanglu <zhanglu@camera360.com>
 *
 */
class Response
{
    /**
     * @var int httpcode.
     */
    private $httpcode;
    /**
     * @var float response cost time.
     */
    private $duration;
    /**
     * @var array response headers.
     */
    private $headers = array();
    /**
     * @var string response origin body
     */
    private $body;
    
    /**
     * @var array response parsed body
     */
    private $data;
    
    /**
     * @param int $httpcode
     * @return $this self reference.
     */
    public function setHttpcode($httpcode)
    {
        $this->httpcode = $httpcode;
        return $this;
    }
    
    /**
     * Returns httpcode.
     * @return int
     */
    public function getHttpcode()
    {
        return $this->httpcode;
    }
    
    /**
     * @param float $duration response duration
     * @return $this self reference.
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }
    
    /**
     * @return float response duration
     */
    public function getDuration()
    {
        return $this->duration;
    }
    
    /**
     * @param array $headers response headers
     * @return $this self reference.
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * @return array response headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * @param string $body content body.
     * @return $this self reference.
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
    
    /**
     * Returns the origin body.
     * @return string content body fields.
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Returns the parsed body.
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            if (!empty($this->body) && $this->isJson()) {
                $this->data = @json_decode($this->body, true);
                $error = json_last_error();
                if ($error !== JSON_ERROR_NONE) {
                    throw new \Exception(json_last_error_msg(), $error);
                }
            }
        }
        return $this->data;
    }
    
    /**
     * @return boolean
     */
    public function ok()
    {
        return is_int($this->httpcode) && $this->httpcode >= 200 && $this->httpcode < 300;
    }
    
    /**
     * Return error message
     * @return string|NULL
     */
    public function getMessage()
    {
        $data = $this->getData();
        if (isset($data['message'])) {
            return $data['message'];
        }
        return null;
    }
    
    private function isJson()
    {
        return is_array($this->headers)
        && array_key_exists('Content-Type', $this->headers)
        && strpos($this->headers['Content-Type'], 'application/json') === 0;
    }
}
