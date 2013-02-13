<?php if(!defined('APP_ACCESS')) die('Access denied.');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    require dirname(__FILE__).'/RestRequest.php';

    class DeathstarRestRequest extends RestRequest
    {
        protected $url              = null;
        protected $appid            = null;
        protected $apptoken         = null;
        

        public function __construct ()
        {
            $this->url                      = APP_URL;
            $this->appid                    = APP_ID;
            $this->apptoken                 = APP_TOKEN;
            $this->queryData['app_token']   = APP_TOKEN;
        }
        
        //clear all data
        public function flush ()
        {
            $this->requestData		= null;
            $this->requestLength        = 0;
            $this->responseData		= null;
            $this->responseInfo		= null;
            $this->queryData            = null;
        }
        
        /*
         *  Fetch Campaign/Task Data.
         *      @param: var $con        : get campaign with condition WHERE
         *                                  eg. $con = array('field' => 'value') or $con = 'field = value'
         *              var $opt        : get campaign whith option in array or string
         *                                  eg. $opt = array('orderby' = 'ASC')  or $opt = 'orderby acs'      
         */
        public function select ($table ,$con = null , $opt = null)
        {
            $query = array();
            
                $query['table'] = $table;
                $query['fc']    = SELECT;
                
            if($con)
                $query['con']   = $con;
            
            if($opt)
                $query['opt']   = $opt;
                      
            $this->setQueryData($query);
            $this->execute();
            

            return $this->getResponse();
        }
        
        
        /*
         *  Insert Campaign/Task Data.
         *      @param: var $data        : data to be inserted in the table
         *                                  eg. $con = array(
         *                                                  'field1' => 'value'
         *                                                  'field2' => 'value
         *                                                  );               
         */
        public function insert($table, $data )
        {
            $query = array();
            
                $query['table'] = $table;
                $query['fc']    = INSERT;
                
            if(is_array($data))
                $query['data']   = $data;
            else 
                die("Data is invalid. Array expected.");
            
            $this->setQueryData($query);
            $this->execute();
            

            return $this->getResponse();
        }
        
        /*
         *  Update Task/Campaign Data.
         *      @param: var $data        : datas to be updated in task/campaign table
         *                                  eg. $data = array(
         *                                                  'field1' => 'value'
         *                                                  'field2' => 'value
         *                                                  ); 
         *              var $where        : condition for data to be updated WHERE
         *                                  eg. $where = array(
         *                                                  'field1' => 'value'
         *                                                    )                    
         */
        public function update($table, $data, $con)
        {
            $query = array();
            
                $query['table'] = $table;
                $query['fc']    = UPDATE;
                
            if(is_array($data))
                $query['data']   = $data;
            else 
                die("Data is invalid. Array expected.");
            
            if($con)
                $query['con']   = $con;
            
            $this->setQueryData($query);
            $this->execute();
            

            return $this->getResponse();
        }
        
        public function delete($table, $con){}  //disable see issues
        
        public function getResponse ()
	{
		return $this->responseData;
	} 

    }
?>
