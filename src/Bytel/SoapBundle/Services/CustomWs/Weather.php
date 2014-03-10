<?php

namespace Bytel\SoapBundle\Services\CustomWs;

use Bytel\SoapBundle\Services\Soap;

class Weather extends Soap {
    
    public function getWeatherInformation($params = array()) {
        
        $response = $this->call('GetWeatherInformation', $params);
        
        return $response->GetWeatherInformationResult->WeatherDescription;
    }

}