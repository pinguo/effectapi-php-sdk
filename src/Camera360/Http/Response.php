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
    private $_httpcode;
    /**
     * @var float response cost time.
     */
    private $_duration;
    /**
     * @var array response headers.
     */
    private $_headers = array();
    /**
     * @var string response origin body
     */
    private $_body;
    
    /**
     * @var array response parsed body
     */
    private $_data;
    
    /**
     * @param int $httpcode
     * @return $this self reference.
     */
    public function setHttpcode($httpcode)
    {
        $this->_httpcode = $httpcode;
        return $this;
    }
    
    /**
     * Returns httpcode.
     * @return int
     */
    public function getHttpcode()
    {
        return $this->_httpcode;
    }
    
    /**
     * @param float $duration response duration
     * @return $this self reference.
     */
    public function setDuration($duration)
    {
        $this->_duration = $duration;
        return $this;
    }
    
    /**
     * @return float response duration
     */
    public function getDuration()
    {
        return $this->_duration;
    }
    
    /**
     * @param array $headers response headers
     * @return $this self reference.
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
        return $this;
    }
    
    /**
     * @return array response headers
     */
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    /**
     * @param string $body content body.
     * @return $this self reference.
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }
    
    /**
     * Returns the origin body.
     * @return string content body fields.
     */
    public function getBody()
    {
        return $this->_body;
    }
    
    /**
     * Returns the parsed body. 
     * @return array
     */
    public function getData()
    {
        if ($this->_data === null) {
            if (!empty($this->_body) && $this->isJson()) {
                $this->_data = @json_decode($this->_body, true);
                $error = json_last_error();
                if ($error !== JSON_ERROR_NONE) {
                    throw new \Exception(json_last_error_msg(), $error);
                }
            }
        }
        return $this->_data;
    }
    
    /**
     * @return boolean
     */
    public function ok()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
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
        return is_array($this->_headers)
        && array_key_exists('Content-Type', $this->_headers)
        && strpos($headers['Content-Type'], 'application/json') === 0;
    }
}