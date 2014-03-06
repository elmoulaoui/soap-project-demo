<?php

namespace Bytel\SoapBundle\Services;

use Zend\Soap\Client as ZendSoapClient;

/**
 * 
 * @author noureddineelmoulaoui
 *
 */

class Soap {
    
    /**
     * SOAP client used to connect to service
     * @var   ZendSoapClient
     */
    protected $soapClient;
    
    
    /**
     * Constructor
     *
     * @param  array $options
     * @return void
     */
    public function __construct($options = null)
    {
        Options::setOptions($this, $options);
    }
    
    /**
     * Get SOAP client
     *
     * @return ZendSoapClient
     */
    public function getSoapClient()
    {
        if (!$this->soapClient instanceof ZendSoapClient) {
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
            
            $soapClient = new ZendSoapClient($wsdl, $options);
            
            $soapClient->setOptions($options);
        }
    
        if (!$soapClient instanceof ZendSoapClient) {
            throw new SoapException('Cannot set soap client service', 0, $e);
        }
    
        $this->soapClient = $soapClient;
    }
}