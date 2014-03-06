<?php

namespace Bytel\SoapBundle\Services\CustomWs;

use Bytel\SoapBundle\Services\Soap;

class Weather extends Soap {
    
    public function getWeatherInformation($params = array()) {
        
        try {
            $response = $this->getSoapClient()->GetWeatherInformation($params);
        } catch (\Exception $e) {
            throw $e;
        }
        return $response->GetWeatherInformationResult->WeatherDescription;
    }

}