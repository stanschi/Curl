<?php
namespace Unifun\Curl;

/**
 *
 * @author v.dimoglo <v.dimoglo@unifun.com>
 *         PHP version 7.*
 */

abstract class Curl
{
    //TODO abstract Class Curl   
    
    /**
     * @var array $errno Contains the error code of the curren request, -1 means no error happend
     * 
     * @access public
     */
    public $errno = array();

    /**
     * @var array $error If the curl request failed, the error message is contained
     * 
     * @access public
     */
    public $error = array();
    
    /**
     * @var string $packageUID UID request
     *
     * @access protected
     */  
    protected $packageUID = "";

    /**
     * @var array $output Request response
     * 
     * @access protected
     */
    protected $output = array();
 
    /**
     * @var array $threadOptions Options
     *
     * @access protected
     */
    protected $threadOptions  = array();   
    
    /**
     * @var int $threadLastKey Lastkey thread is necessary for multi curl
     *
     * @access protected
     */
    protected $threadLastKey = 0;
    
    /**
     * @var int $threadLimit Limit thread is necessary for multi curl. Default is 5
     *
     * @access private
     */
    private $threadLimit = 1;

    public function __construct()
    {
        $threadOptions = array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL            => '',
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_RETURNTRANSFER => true,
        );              
    }

    //TODO Getters  
    
    /**
     * Get header information.
     *
     * @example
     * $curl = new CurlSingle();
     * $header = $curl->getHeader();
     *
     * @access public
     *        
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }
    
    /**
     * Get a response to a request.
     *
     * @example
     * $curl = new CurlSingle();
     * $output = $curl->getOutput();
     *
     * @access public
     *
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }
    
    /**
     * Get options.
     *
     * Default options is:
     * CURLOPT_FOLLOWLOCATION => true
     * CURLOPT_SSL_VERIFYHOST => false
     * CURLOPT_SSL_VERIFYPEER => false
     * CURLOPT_URL            => ''
     * CURLOPT_CONNECTTIMEOUT => 3
     * CURLOPT_TIMEOUT        => 5
     * CURLOPT_RETURNTRANSFER => true
     * 
     * @example
     * $curl = new CurlSingle();
     * $options = $curl->getOptions();
     *
     * @access public
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->threadOptions;
    }
    
    /**
     * Get last thread is necessary for multi curl.
     *
     * @example
     * $curl = new CurlMulti();
     * $threadLastKey = $curl->getThreadLastKey();
     *
     * @access public
     *
     * @return int
     */
    public function getThreadLastKey(): int
    {
        return $this->threadLastKey;
    }
    
    /**
     * Get thread limit is necessary for multi curl.
     *
     * @example
     * $curl = new CurlMulti();
     * $threadLimit = $curl->getThreadLimit();
     *
     * @access public
     *
     * @return int
     */
    public function getThreadLimit():int
    {
        return $this->threadLimit;
    }
    
    //TODO Setters 
    
    /**
     * Set options.
     *
     * @example
     * $curl = new CurlMulti();
     * $options = array(
     *      CURLOPT_CONNECTTIMEOUT => 13,
     *      CURLOPT_TIMEOUT => 15,
     * );   
     * $threadLimit = $curl->setOptions($options);
     *
     * @access public
     * 
     * @param array $options [required]    
     *
     * @return void     
     */
    public function setOptions($options): void
    {
        $this->threadOptions = $options;                      
    }
     
    /**
     * Set thread(package) limit is necessary for multi curl.
     *
     * @example
     * $curl = new CurlMulti();
     * $threadLimit = $curl->setThreadLimit(15);
     *
     * @access public
     * 
     * @param int $threadLimit [required]    
     *
     * @return void
     */
    public function setThreadLimit($threadLimit): void
    {
        $this->threadLimit = $threadLimit;
    }
    
    /**
     * Make a get request with optional data.
     *
     * @example
     * $curl = new CurlSingle();
     * $curl->get('http://dv.ge/request.php');
     *
     * @access public
     *
     * @param string $url [required]
     * @param array  $params [optional]
     *
     * @return string
     */
    public function get($url, $params = array()): string
    {
        if (!empty($params))
        {
            $url .= "?";
            foreach ($params as $key => $value)
                $url .= $key . "=" . $value . "&";
                $url = trim($url, "&");
        }
        
        $this->threadLastKey++;
        $this->threadOptions[$this->threadLastKey][CURLOPT_URL] = $url;
        $this->threadOptions[$this->threadLastKey][CURLOPT_HTTPGET] = true;
        $this->threadOptions[$this->threadLastKey][CURLOPT_RETURNTRANSFER] = true;
        $this->threadOptions[$this->threadLastKey][CURLOPT_SSL_VERIFYHOST] = false;
        $this->threadOptions[$this->threadLastKey][CURLOPT_SSL_VERIFYPEER] = false;

        if ($this->threadLastKey >= $this->threadLimit)
        {
            $this->threadLastKey = 0;
            $this->output = array();
            $this->packageUID = uniqid();
            $this->exec();
        }
        
        return $this->packageUID;
    }
    
    /**
     * Make a post request with optional data.
     *
     * @example
     * $curl = new CurlSingle();
     * $curl->post('http://dv.ge/request.php');
     *
     * @access public
     *
     * @param string $url [required]
     * @param array  $params [required]
     *
     * @return string
     */
    public function post($url, $params): string
    {
        $this->threadLastKey++;
        $this->threadOptions[$this->threadLastKey][CURLOPT_URL] = $url;
        $this->threadOptions[$this->threadLastKey][CURLOPT_POST] = true;
        $this->threadOptions[$this->threadLastKey][CURLOPT_POSTFIELDS] = $params;
        $this->threadOptions[$this->threadLastKey][CURLOPT_RETURNTRANSFER] = true;               
        
        if ($this->threadLastKey >= $this->threadLimit)
        {
            $this->threadLastKey = 0;
            $this->output = array();
            $this->packageUID = uniqid();
            $this->exec();
        }
        
        return $this->packageUID;
    }
    
    
    /**
     * Initializer for the curl resource.
     * Is called by the __construct() of the class or when the curl request is reseted.
     *
     * @example
     * $curl = new CurlSingle();
     * $curl->init();
     * $curl->get('http://dv.ge/request.php');
     * $curl->get('http://dv.ge/request.php');
     * $curl->close();
     * $curl->get('http://dv.ge/request.php');
     *
     * @access abstract public
     *        
     * @return self
     */
    abstract public function init();
    

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
    abstract public function close();
           
    /**
     * Execute request.
     *
     * @access public
     *        
     * @return void
     */
    abstract public function exec();
}

