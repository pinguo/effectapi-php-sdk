<?php
namespace Camera360\Http;

use Camera360\Conf;

/**
 * 实现HTTP客户端
 * 
 * @author zhanglu <zhanglu@camera360.com>
 *
 */
class Client {
    
    private static $_ua;
    
    /**
     * Creates 'GET' request.
     * @param string $url target URL.
     * @param array|string $body if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Response response instance.
     */
    public static function get($url, $body = null, $headers = array(), $options = array())
    {
        $request = self::createRequest('get', $url, $body, $headers, $options);
        $response = self::send($request);
        return $response;
    }
    
    /**
     * Creates 'POST' request.
     * @param string $url target URL.
     * @param array|string $body if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return Response response instance.
     */
    public static function post($url, $body = null, $headers = array(), $options = array())
    {
        $request = self::createRequest('post', $url, $body, $headers, $options);
        $response = self::send($request);
        return $response;
    }
    
    /**
     * @param string $method
     * @param string $url
     * @param array|string $body
     * @param array $headers
     * @param array $options
     * @return Request request instance.
     */
    public static function createRequest($method, $url, $body = null, $headers = array(), $options = array())
    {
        $request = new Request();
        $request->setMethod($method)
        ->setUrl($url)
        ->setHeaders($headers)
        ->setOptions($options)
        ->setBody($body);
        
        return $request;
    }
    
    /**
     * @param int $httpcode
     * @param float $duration
     * @param array $headers
     * @param array|string $body
     * @return Response response instance.
     */
    public static function createResponse($httpcode, $duration, $headers, $body)
    {
        $response = new Response();
        $response->setHttpcode($httpcode)
        ->setDuration($duration)
        ->setHeaders($headers)
        ->setBody($body);
        
        return $response;
    }
    
    /**
     * @param Request $request
     * @throws \Exception
     * @return Response response instance.
     */
    public static function send($request)
    {
        $method = strtoupper($request->getMethod());
        switch ($method) {
            case 'POST':
                $options[CURLOPT_POST] = true;
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        
        $body = $request->getBody();
        if ($body === null) {
            if ($method === 'HEAD') {
                $options[CURLOPT_NOBODY] = true;
            }
        } else {
            $options[CURLOPT_POSTFIELDS] = $body;
        }
        
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_USERAGENT] = self::userAgent();
        $options[CURLOPT_URL] = $request->getUrl();
        $options[CURLOPT_HTTPHEADER] = $request->getHttpheader();
        $responseHeaders = array();
        $options[CURLOPT_HEADERFUNCTION] = function($resource, $headerString) use (&$responseHeaders) {
            $header = trim($headerString, "\n\r");
            if (strlen($header) > 0) {
                $kv = explode(':', $header, 2);
                if (count($kv) > 1) {
                    $responseHeaders[$kv[0]] = trim($kv[1]);
                }
            }
            return mb_strlen($headerString, '8bit');
        };
        
        $reqOptions = $request->getOptions();
        if (is_array($reqOptions)) {
            $options = $reqOptions + $options;
        }
        
        $t1 = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $responseBody = curl_exec($ch);
        $t2 = microtime(true);
        $duration = round($t2 - $t1, 3);
        
        // check cURL error
        $errno = curl_errno($ch);
        if ($errno !== 0) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Curl error: '. $error . ', errno: ' . $errno . ', duration: ' . $duration . 's');
        }
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $response = $this->createResponse($httpcode, $duration, $responseHeaders, $responseBody);
        return $response;
    }
    
    private static function userAgent()
    {
        if (!self::$_ua) {
            $sdkInfo = "Camera360PHP/" . Conf::SDK_VER;
            
            $systemInfo = php_uname("s");
            $machineInfo = php_uname("m");
            
            $envInfo = "($systemInfo/$machineInfo)";
            
            $phpVer = phpversion();
            
            self::$_ua = "$sdkInfo $envInfo PHP/$phpVer";
        }
        return self::$_ua;
    }
}

