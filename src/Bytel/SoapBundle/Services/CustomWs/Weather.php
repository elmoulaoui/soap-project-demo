<?php

namespace Bytel\SoapBundle\Services\CustomWs;

use Bytel\SoapBundle\Services\Soap;
use Bytel\SoapBundle\Services\Event\SoapEvent;

class Weather extends Soap {
    
    public function getWeatherInformation($params = array()) {
        
        try {
            $response = $this->getSoapClient()->GetWeatherInformation($params);
            
            $event = new SoapEvent($this->getSoapClient()->getLastRequest(), $this->getSoapClient()->getLastResponse());
            
            //die(var_dump($this->getEventDispatcher()));
            
            $this->getEventDispatcher()->dispatch('soap.call', $event);
            
            //die(var_dump($this->getSoapClient()->getLast));
            
        } catch (\Exception $e) {
            throw $e;
        }
        return $response->GetWeatherInformationResult->WeatherDescription;
    }

}