<?php

namespace Bytel\SoapBundle\Services\Collector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

use Bytel\SoapBundle\Services\Listener\SoapListener;

class SoapCollector extends DataCollector implements DataCollectorInterface {
    
    /**
     * @var SoapListener
     */
    protected $listener;
    
    /**
     * Constructor
     *
     * @param SoapListener $listener
     */
    public function __construct(SoapListener $listener)
    {
        
        $this->listener = $listener;
    }
    
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->listener->getCalls();
    }
    
    /**
     * Returns profiled data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    public function getName()
    {
        return 'soap';
    }
}