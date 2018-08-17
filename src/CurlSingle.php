<?php
namespace Unifun\Curl;

/**
 * @author v.dimoglo <v.dimoglo@unifun.com>
 * PHP version 7.* 
 */

class CurlSingle extends Curl
{   
    //TODO Class Single curl   
    
    /**     
     * @var resource $init Resource single curl
     * 
     * @access private
     */ 
    private $init = 0;
    
    /**
     * @var bool $autoClose Send in one connection default is true  
     * 
     * @access private
     */
    private $autoClose = true;
    
    public function __construct()
    {
        parent::__construct();
        $this->setThreadLimit(1);
    }
        
    /**
     * Execute request.
     *
     * @access private
     *
     * @return void
     */
    public  function exec(): void
    {
        $this->init();
        
        $this->output[$this->packageUID][] = curl_exec($this->init);
        $this->errno[$this->packageUID][]  = curl_errno($this->init);
        $this->error[$this->packageUID][]  = curl_error($this->init);
        
        if ($this->autoClose)
            $this->close();
    }   
    
    /**
     * Initializer for the curl resource.         
     *
     * @example
     * $curl = new CurlSingle();
     * $curl->init();
     * $curl->get('http://dv.ge/request.php');
     * $curl->get('http://dv.ge/request.php');
     * $curl->close();
     * $curl->get('http://dv.ge/request.php');
     * 
     * @access public
     * 
     * @return self
     */    
    public function init(): self
    {
        if (!is_resource($this->init))            
            $this->init = curl_init();
        else 
            $this->autoClose = false;                               
            
        foreach ($this->threadOptions as $threadKey => $threadOptions)
        {
            curl_setopt_array($this->init, $threadOptions);
        }
                              
        return $this;
    }
    
    /**
     * Closing the current open curl resource.
     * 
     * @example
     * $curl = new CurlSingle();
     * $curl->init();
     * $curl->get('http://dv.ge/request.php');
     * $curl->close();
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
        if (is_resource($this->init))
            curl_close($this->init);

        $this->init      = 0;
        $this->autoClose = true;
        
        return $this;
    }            
}

