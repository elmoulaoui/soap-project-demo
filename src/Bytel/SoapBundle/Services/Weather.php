<?php

namespace Bytel\SoapBundle\Services;

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