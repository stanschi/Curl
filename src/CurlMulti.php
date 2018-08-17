<?php
namespace Unifun\Curl;

/**
 * @author v.dimoglo <v.dimoglo@unifun.com>
 * PHP version 7.* 
 */

class CurlMulti extends Curl
{   
    //TODO Class Multi curl   
    
    /**
     * @var resource $initMulti Resource multi curl
     * 
     * @access private
     */    
    private $initMulti  = 0;   
    
    /**    
     * @var array $threadResource Resources of each thread
     * 
     * @access private
     */    
    private $threadResource = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->setThreadLimit(5);
    } 
    
    /**
     * Execute request.
     *
     * @access private
     *
     * @return void
     */
    private function exec(): void
    {
        $threadProccesed = 0;
        
        $this->init();
        
        do
        {
            curl_multi_exec($this->initMulti, $execStatus);
            
            $initInfo = curl_multi_info_read($this->initMulti);
            
            if (is_array($initInfo))
            {
                $threadResource = $initInfo['handle'];
                $this->output[$this->packageUID ][] = curl_multi_getcontent($threadResource);
                $threadProccesed++;
            }
        } while ($threadProccesed != $this->getThreadLimit());
        
        $this->close();        
    }     
    
    /**
     * Initializer for the multi curl resource.
     *
     * @example
     * $curl = new CurlMulti();     
     * $curl->get('http://dv.ge/request.php');
     * $curl->get('http://dv.ge/request.php');
     * $curl->get('http://dv.ge/request.php');
     *
     * @access public
     *
     * @return self
     */
    public function init(): self
    {
        if (!is_resource($this->initMulti))
            $this->initMulti = curl_multi_init();
                
        foreach ($this->threadOptions as $threadKey => $threadOptions)
        {
            $this->threadResource[$threadKey] = curl_init();
            curl_setopt_array($this->threadResource[$threadKey], $threadOptions);
            curl_multi_add_handle($this->initMulti, $this->threadResource[$threadKey]);
        }
        
        return $this;              
    }
    
    
    /**
     * Closing the current open multi curl resource.
     * 
     * @example
     * $curl = new CurlMulti();
     * $curl->get('http://dv.ge/request.php');
     * $curl->get('http://dv.ge/request.php');
     * $curl->get('http://dv.ge/request.php');
     * $curl->close();
     * 
     * @access public
     * 
     * @return self
     */    
    public function close(): self
    {
        if (is_resource($this->initMulti))
            curl_multi_close($this->initMulti);

        $this->initMulti = 0;        
        
        return $this;
    }      
}

