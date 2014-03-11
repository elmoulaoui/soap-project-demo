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
    public function __construct($options = null)
    {
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
    
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function call($method, $params)
    {
        try {
            $response = $this->getSoapClient()->$method($params);
            
            $lastRequest = $this->xmlToArray(simplexml_load_string($this->getSoapClient()->getLastRequest()));
            $lastResponse = $this->xmlToArray(simplexml_load_string($this->getSoapClient()->getLastResponse()));
            
            $event = new SoapEvent($lastRequest, $lastResponse, $this->getSoapClient()->getLastMethod());
            $this->getEventDispatcher()->dispatch('soap.call', $event);
        
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $response;
    }
    
    /**
     * 
     * @param \SimpleXMLElement $xml
     * @param array $options
     * @return array
     */
    public function xmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => ':',//you may want this to be something other than a colon
            'attributePrefix' => '@', //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(), //array of xml tag names which should always become arrays
            'autoArray' => true, //only create arrays for tags which appear more than once
            'textContent' => '$', //key used for the text content of elements
            'autoText' => true, //skip textContent key if node has no attributes or child nodes
            'keySearch' => false, //optional search and replace on tag and attribute names
            'keyReplace' => false //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace
         
        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }
         
        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = $this->xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);
                 
                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
                 
                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                    in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                    ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }
         
        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
         
        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
        ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
         
        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }
}