<?php
namespace Bytel\SoapBundle\Services\Event;

use Symfony\Component\EventDispatcher\Event;

class SoapEvent extends Event {
    
    protected $request;
    protected $response;
    protected $method;
    
    /**
     * 
     * @param string $request
     * @param string $response
     * @param string $method
     */
    public function __construct($request, $response, $method)
    {
        $this->request = $request;
        $this->response = $response;
        $this->method = $method;
    }
    
    public function getRequest() 
    {
        return $this->request;
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
}