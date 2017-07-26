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
    private $url;
    /**
     * @var string request method.
     */
    private $method = 'get';
    /**
     * @var array request headers.
     */
    private $headers = array();
    /**
     * @var array request options.
     */
    private $options = array();
    /**
     * @var mixed request body
     */
    private $body;

    /**
     * Sets target URL.
     * @param string $url use a string to represent a URL
     * @return $this self reference.
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Returns target URL.
     * @return string target URL
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $method request method
     * @return $this self reference.
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string request method
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * @param string $headers request headers
     * @return $this self reference.
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * @return array request origin headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * @return array request format http header
     */
    public function getHttpheader()
    {
        if (empty($this->headers)) {
            return null;
        }
        
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = "$key: $value";
        }
        return $headers;
    }

    /**
     * @param array $options request options.
     * @return $this self reference.
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array request options.
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * @param mixed $body content body fields.
     * @return $this self reference.
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
    
    /**
     * Returns the body fields.
     * @return mixed content body fields.
     */
    public function getBody()
    {
        return $this->body;
    }
}
