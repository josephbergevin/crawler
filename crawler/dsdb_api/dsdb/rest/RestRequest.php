<?php if(!defined('APP_ACCESS')) die('Access denied.');

// 
define("APP_USER", 'API0001'); 
define("APP_PASS", 'apipass');


class RestRequest
{
	protected $verb                 = 'POST';
        protected $queryData            = null;
	protected $requestData          = null;
	protected $requestLength        = 0;       
	protected $acceptType           = ACCEPT_TYPE;
	protected $responseData         = null;
	protected $responseInfo         = null;
        protected $username             = APP_USER;
        protected $password             = APP_PASS;
        
	
	public function __construct ()
	{
                $this->username                 = APP_USER;
                $this->password                 = APP_PASS;
		$this->verb			= 'POST';
		$this->requestData		= null;
		$this->requestLength            = 0;		
		$this->acceptType		= ACCEPT_TYPE;
		$this->responseData		= null;
		$this->responseInfo		= null;	
                $this->queryData                = null;
	}
        
	protected function execute ($db = null)
	{
		$ch = curl_init();
                
		try
		{
                    if(!$this->setAuth($ch))
                        throw new Exception('Credentials not defined.');
                    
                    $this->requestData['query_data'] = $this->queryData;
                    
                    switch (strtoupper($this->verb))
                    {
                            case 'POST':
                                    $this->executePost($ch);
                                    break;                           
                            default:
                                    throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
                    }
		}
		catch (InvalidArgumentException $e)
		{
			curl_close($ch);
                        print($e->getMessage());
			throw $e;
		}
		catch (Exception $e)
		{
			curl_close($ch);
                        print($e->getMessage());
			throw $e;
		}
		
	}
	
	public function buildPostBody ($data = null)
	{                                     
		$data = ($data !== null) ? $data : $this->requestData;
		
		if (!is_array($data))
		{
			throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
		}
		
		$data = http_build_query($data, '', '&');
		$this->requestData = $data;
	}
	
	protected function executeGet ($ch)
	{		
		$this->doExecute($ch);	
	}
	
	protected function executePost ($ch)
	{
		if (!is_string($this->requestData))
		{
			$this->buildPostBody();
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestData);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		$this->doExecute($ch);	
	}
	
	protected function executePut ($ch)
	{
		if (!is_string($this->requestData))
		{
			$this->buildPostBody();
		}
		
		$this->requestLength = strlen($this->requestData);
		
		$fh = fopen('php://memory', 'rw');
		fwrite($fh, $this->requestData);
		rewind($fh);
		
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, $this->requestLength);
		curl_setopt($ch, CURLOPT_PUT, true);
		
		$this->doExecute($ch);
		
		fclose($fh);
	}
	
	protected function executeDelete ($ch)
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
		$this->doExecute($ch);
	}
	
	protected function doExecute (&$curlHandle)
	{
		$this->setCurlOpts($curlHandle);
		$this->responseData     = curl_exec($curlHandle);
		$this->responseInfo	= curl_getinfo($curlHandle);
		$this->requestData      = null;
		curl_close($curlHandle);
	}
	
	protected function setCurlOpts (&$curlHandle)
	{
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->acceptType));
	}
	
	protected function setAuth (&$curlHandle)
	{
		if ($this->username !== null && $this->password !== null && $this->appid)
		{
                        $request_params = array(
                                                'app_id'    => $this->appid,
                                                'app_token' => $this->apptoken
                                            );
                        
                        $this->requestData['enc_request'] =  base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->apptoken, json_encode($request_params), MCRYPT_MODE_ECB));
                    
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
                        
                        return true;
		}else{
                    
                    return false;
                }
	}
	
        public function setQueryData ($arr)
        {
            if(is_array($arr))
                $this->queryData = array_merge ($this->queryData,$arr);
        }
        
        public function getQueryData ()
        {
            return $this->queryData;
        }
}
