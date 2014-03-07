<?php
namespace Bytel\SoapBundle\Services\Event;

use Symfony\Component\EventDispatcher\Event;

class SoapEvent extends Event {
    
    protected $request;
    protected $response;
    
    /**
     * 
     * @param string $request
     * @param string $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    
    public function getRequest() 
    {
        return $this->request;
    }
    
    public function getResponse()
    {
        return $this->response;
    }
}