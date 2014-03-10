<?php

namespace Bytel\SoapBundle\Services\Samples;

use Bytel\SoapBundle\Services\Soap;

class Weather extends Soap {
    
    public function getWeatherInformation($params = array()) {
        
        $response = $this->call('GetWeatherInformation', $params);
        
        return $response->GetWeatherInformationResult->WeatherDescription;
    }

}