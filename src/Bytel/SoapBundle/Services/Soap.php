<?php

namespace Bytel\SoapBundle\Services;

use Bytel\SoapBundle\Services\SoapClient as SoapClient;
use Bytel\SoapBundle\Services\Event\SoapEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * 
 * @author noureddineelmoulaoui
 *
 */

class Soap {
    
    /**
     * SOAP client used to connect to service
     * @var   SoapClient
     */
    protected $soapClient;
    
    protected $dispatcher;
    
    
    /**
     * Constructor
     *
     * @param  array $options
     * @return void
     */
    public function __construct($options = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
        Options::setOptions($this, $options);
    }
    
    /**
     * Get SOAP client
     *
     * @return SoapClient
     */
    public function getSoapClient()
    {
        if (!$this->soapClient instanceof SoapClient) {
            throw new SoapException('Cannot get soap client service', 0, $e);
        }
        return $this->soapClient;
    }
    
    /**
     * Set SOAP client
     *
     * @param  array $soapClient
     */
    public function setSoapClient($options)
    {
        
        if (is_array($options)) {
            $wsdl = isset($options['wsdl']) ? $options['wsdl'] : null;
            $options = isset($options['options']) ? $options['options'] : array();
            
            $soapClient = new SoapClient($wsdl, $options);
            
            $soapClient->setOptions($options);
        }
    
        if (!$soapClient instanceof SoapClient) {
            throw new SoapException('Cannot set soap client service', 0, $e);
        }
    
        $this->soapClient = $soapClient;
    }
    
    public function getEventDispatcher() 
    {
        return $this->dispatcher;
    }
    
    public function call($method, $params)
    {
        try {
            $response = $this->getSoapClient()->$method($params);
        
            $event = new SoapEvent($this->getSoapClient()->getLastRequest(), $this->getSoapClient()->getLastResponse(), $this->getSoapClient()->getLastMethod());
            $this->getEventDispatcher()->dispatch('soap.call', $event);
        
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $response;
    }
}