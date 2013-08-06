<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */ 
/**
 * Get the list of users this user can send to
 * Pretty sure this function takes no args
 * @return array		$users the IDs of users this user can send to
 */
 
sys::import('modules.messages.xarincludes.defines');

    function messages_userapi_get_sendtousers($args)
    { 
        $sendtogroups = xarMod::apiFunc('messages','user','get_sendtogroups',$args); 
         
		if (empty($sendtogroups)) return array();

        // Get the users these allowed groups contain
        sys::import('xaraya.structures.query');
        $xartable = xarDB::getTables();
        $q = new Query('SELECT');
        $q->addtable($xartable['roles'], 'r');
        $q->addtable($xartable['rolemembers'],'rm');
        $q->join('r.id', 'rm.role_id');
        
        $q->addfield('r.id');
        $q->addfield('r.name');
        $q->addfield('r.uname');
        $q->eq('r.state', xarRoles::ROLES_STATE_ACTIVE);
        $q->ne('r.email', '');
        $q->ne('r.name' , 'Myself');
        $q->eq('r.itemtype' , xarRoles::ROLES_USERTYPE);//check for user
            
        /*Psspl:get the selected groups only*/
        $user_c = array();
        foreach ($sendtogroups as $key => $value){
            $user_c[]=$q->peq('rm.parent_id' , $value);
        }
        $q->qor($user_c); //use OR
                
        //function for echo the query.
        //$q->qecho();
            
        if(!$q->run()) return;
    
        $users = $q->output();
        
        // Need to transform the display name values we got
        $nameproperty = DataPropertyMaster::getProperty(array('name' => 'name'));
        foreach ($users as $key => $value) {
            $nameproperty->value = $users[$key]['name'];
            $users[$key]['name'] = $nameproperty->getValue();
        }

        return $users;
    }
?>