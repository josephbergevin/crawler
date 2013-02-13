<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    define('APP_ACCESS', true);
            
//Includes
    require dirname(__FILE__).'/config.php';
    require dirname(__FILE__).'/rest/DeathstarRestRequest.php';
    
    class Deathstar 
    {
        protected $function;
        protected $table;
        protected $where;
        protected $option;
        protected $data;
        
        public function __construct() 
        {
            $this->func         = SELECT;
            $this->table        = CAMPAIGN_TABLE;
            $this->where        = null;
            $this->option       = null;
            $this->data         = null;
        }
        
        public function flush ()
        {
            $this->func     = SELECT;
            $this->table    = CAMPAIGN_TABLE;
            $this->where    = null;
            $this->option   = null;
            $this->data     = null;
        }
        
        /*
         *  Fetch Campaign Data.
         *      @param: var $con        : get campaign with condition WHERE
         *                                  eg. $con = array('field' => 'value') or $con = 'field = value'
         *              var $opt        : get campaign whith option in array or string
         *                                  eg. $opt = array('orderby' = 'ASC')  or $opt = 'orderby acs'      
         */
        public function getCampaigns($where = null, $opt = null)
        {
            $this->flush();
            
            if($where)
                $this->where    = $where;
            
            if($opt)
                $this->option   = $option;
            
            return $this->execute();
        }
        
        /*
         *  Fetch Task Data.
         *      @param: var $con        : get task with condition WHERE
         *                                  eg. $con = array('field' => 'value') or $con = 'field = value'
         *              var $opt        : get task whith option in array or string
         *                                  eg. $opt = array('orderby' = 'ASC')  or $opt = 'orderby acs'      
         */
        public function getTasks ($where = null, $opt = null)
        {
            $this->flush();
            $this->table = TASK_TABLE;
                       
            if($where)
                $this->where    = $where;
            
            if($opt)
                $this->option   = $option;
            
            return $this->execute();
        }
        
        public function insertCampaign ($data){} //Disabled working how to work with this due to table problem
                
        /*
         *  Insert Task Data.
         *      @param: var $data        : datas to be inserted in task table
         *                                  eg. $data = array(
         *                                                  'field1' => 'value'
         *                                                  'field2' => 'value
         *                                                  );               
         */
        public function insertTasks ($data)
        {
            $this->flush();
            $this->func  = INSERT;
            $this->table = TASK_TABLE;
            
            if(is_array($data))
                $this->data     = $data;
            else 
                die("Data is invalid. Array expected.");
                
            return $this->execute();
        }
        
        
        public function updateCampaign ($data, $where){} //Disabled working how to work with this due to table problem
        
        /*
         *  Update Task Data.
         *      @param: var $data        : datas to be updated in task table
         *                                  eg. $data = array(
         *                                                  'field1' => 'value'
         *                                                  'field2' => 'value
         *                                                  ); 
         *              var $where        : condition for data to be updated WHERE
         *                                  eg. $where = array(
         *                                                  'field1' => 'value'
         *                                                    )                    
         */
        public function updateTasks ($data, $where)
        {
            $this->flush();
            $this->func  = UPDATE;
            $this->table = TASK_TABLE;
            
            if(is_array($data))
                $this->data     = $data;
            else 
                die("Data is invalid. Array expected.");
            
            if($where)
                $this->where    = $where;
                
            return $this->execute();
        }
        
        public function deleteCampaigns($where){}  //disable see issues
        public function deleteTasks($where){}       //disable see issues
        
        protected function execute()
        {           
            $deathstar    = new DeathstarRestRequest();
            $func = $this->func;
            switch($func)
            {
                case SELECT     :                         
                case DELETE     :   $response = $deathstar->$func($this->table,$this->where,$this->option);                      
                                    break;
                case INSERT     :
                case UPDATE     :   $response = $deathstar->$func($this->table,$this->data,$this->where);
                                    break;
            }
            return $response;
        }
    }
    
    
?>
