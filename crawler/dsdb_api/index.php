<?php


include 'dsdb/Deathstar.inc.php';

$deathstar = new Deathstar();

$campaigns = $deathstar->getCampaigns();

echo "<p>List of Campaigns</p><pre>";
print_r($campaigns);


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 /*   error_reporting(E_ALL);
    ini_set('display_errors', '1');
    require dirname(__FILE__).'/dsdb/Deathstar.inc.php';
    
    $campaign = new Deathstar();  
 
    $where = array('campaign_id' => '111');
    //$where = null;
    //echo '<pre>' . print_r(json_decode($campaign->getCampaigns($where)), true) . '</pre>';
    echo '<pre>' . print_r($campaign->getCampaigns($where), true) . '</pre>';
    //echo '<pre>'. print_r($campaign, true) .'</pre>';
    */
    
    
    //$where = array('task_id' => '108');
    //$where = null;
    //echo '<pre>' . print_r(json_decode($campaign->getTasks($where)), true) . '</pre>';
    //echo '<pre>' . print_r($campaign->getTasks($where), true) . '</pre>';
    //echo '<pre>'. print_r($campaign, true) .'</pre>';
    //$data = array('task_name'=>'test2',
    //              'fk_category_id' => 2
    //            );
    //$where = array('task_id' => 494);
    //echo '<pre>' . print_r($campaign->updateTasks($data, $where), true) . '</pre>';
    //echo '<pre>'. print_r($campaign, true) .'</pre>'; 
    
    
    $data =  array(
                    array(
                        'name'          => 'Ramer Duhino'
                        , 'age'         => '23'
                        , 'location'    => 'Perrelos Carcar Cebu'
                    ),
                    array(
                        'name'          => 'Glenda Lim'
                        , 'age'         => '80'
                        , 'location'    => 'Talamban Cebu'
                    )
             );
      
    //$request->execute();  
    //echo '<pre>' . print_r($request, true) . '</pre>';  
?>
