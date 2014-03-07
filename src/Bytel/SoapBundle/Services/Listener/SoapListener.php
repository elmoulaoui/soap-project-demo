<?php
namespace Bytel\SoapBundle\Services\Listener;

use Bytel\SoapBundle\Services\Event\SoapEvent;

/**
 * Maintains a list of requests and responses sent using a request or client
 */
class SoapListener
{
    /**
     * @var array Requests and responses that have passed through the plugin
     */
    protected $calls = array();

    public function onRequestSent(SoapEvent $event)
    {
        $this->add($event->getRequest(), $event->getResponse(), $event->getMethod());
    }

    /**
     * Add a request to the history
     *
     * @param string $request
     * @param string $response
     * @param string $method
     */
    public function add($request, $response, $method)
    {
        $this->calls[] = array(
            'request'  => $request,
            'response' => $response,
            'method'   => $method
        );
    }
    
    
    /**
     * Convert to a string that contains all request and response headers
     *
     * @return string
     */
    public function __toString()
    {
        $lines = array();
        foreach ($this->calls as $entry) {
            $response = isset($entry['response']) ? $entry['response'] : '';
            $lines[] = '> ' . trim($entry['request']) . "\n\n< " . trim($response) . "\n";
        }
    
        return implode("\n", $lines);
    }

    /**
     * Set the max number of requests to store
     *
     * @param int $limit
     *            Limit
     *            
     * @return HistoryPlugin
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        
        return $this;
    }

    /**
     * Get the request limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get all of the raw transactions in the form of an array of associative arrays containing
     * 'request' and 'response' keys.
     *
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * Get the requests in the history
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        // Return an iterator just like the old iteration of the HistoryPlugin for BC compatibility (use getAll())
        return new \ArrayIterator(array_map(function ($entry)
        {
            $entry['request']->getParams()->set('actual_response', $entry['response']);
            return $entry['request'];
        }, $this->calls));
    }

    /**
     * Get the number of requests in the history
     *
     * @return int
     */
    public function count()
    {
        return count($this->calls);
    }

    /**
     * Get the last request sent
     *
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        $last = end($this->calls);
        
        return $last['request'];
    }

    /**
     * Get the last response in the history
     *
     * @return Response null
     */
    public function getLastResponse()
    {
        $last = end($this->calls);
        
        return isset($last['response']) ? $last['response'] : null;
    }

    /**
     * Clears the history
     *
     * @return HistoryPlugin
     */
    public function clear()
    {
        $this->calls = array();
        
        return $this;
    }

}
