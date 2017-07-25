<?php
namespace Camera360\Http;

/**
 * HTTP请求类
 * 
 * @author zhanglu <zhanglu@camera360.com>
 *
 */
class Request
{
    /**
     * @var string target URL.
     */
    private $_url;
    /**
     * @var string request method.
     */
    private $_method = 'get';
    /**
     * @var array request headers.
     */
    private $_headers = array();
    /**
     * @var array request options.
     */
    private $_options = array();
    /**
     * @var mixed request body
     */
    private $_body;

    /**
     * Sets target URL.
     * @param string $url use a string to represent a URL
     * @return $this self reference.
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Returns target URL.
     * @return string target URL
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $method request method
     * @return $this self reference.
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * @return string request method
     */
    public function getMethod()
    {
        return $this->_method;
    }
    
    /**
     * @param string $headers request headers
     * @return $this self reference.
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
        return $this;
    }
    
    /**
     * @return array request origin headers
     */
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    /**
     * @return array request format http header
     */
    public function getHttpheader()
    {
        if (empty($this->_headers)) {
            return null;
        }
        
        $headers = array();
        foreach ($this->_headers as $key => $value) {
            $headers[] = "$key: $val";
        }
        return $headers;
    }

    /**
     * @param array $options request options.
     * @return $this self reference.
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * @return array request options.
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /**
     * @param mixed $body content body fields.
     * @return $this self reference.
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }
    
    /**
     * Returns the body fields.
     * @return mixed content body fields.
     */
    public function getBody()
    {
        return $this->_body;
    }
}