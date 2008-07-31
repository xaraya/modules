<?
/**Psspl:Added the code for allow the members of a
 * given group to only send messages to another group.
 * 
 * 
 */
 
include_once("./modules/commonutil.php");
function messages_userapi_get_users( $args )
{

    extract($args);
	
	   	$users = xarModAPIFunc('roles',	'user',
								    	'getall',
								    	array('state'   => 3,
								    	'include_anonymous' => false,
								    	'include_myself' => false));
    	$userid = xarUserGetVar('id');

    	sys::import('modules.roles.class.xarQuery');

    	$xartable = xarDB::getTables();
    	$q = new xarQuery('SELECT');
    	$q->addtable($xartable['roles'],'r');

    	$q->eq('id',$userid);

    	$q->addtable($xartable['rolemembers'],'rm');
    	$q->join('r.id','rm.role_id');

    	if(!$q->run()) return;
    	$CurrentUser =  $q->output();
		
    	$id=$CurrentUser[0]['parent_id'];
    	$groupID=$CurrentUser[0]['parent_id'];
    	
    	$allowedSendMessages = unserialize(xarModVars::get('messages',"allowedSendMessages[$groupID]"));
    	TracePrint($allowedSendMessages,"allowed Send Messages");
    	
    	$state = 3;//set state for active users


    	$xartable = xarDB::getTables();
    	$q = new xarQuery('SELECT');
    	$q->addtable($xartable['roles'],'r');
    	
    	/*Psspl:get the selected user only
    	if ($allowedSendMessages['childusersimploded']!=0) {
	    	//get the selected group/user information with current userid.
	    	$allowedUsers = explode(",",$allowedSendMessages['childusersimploded']);
			  	
			foreach ($allowedUsers as $key => $value)
			{
				$user_c[]=$q->peq('id' , $value);
			}
			
			$q->qor($user_c); //use OR
    	}
		*/
    	
		$q->eq('state',$state);
    	$q->ne('email','');
    	$q->ne('name' , 'Myself');
    	$q->ne('itemtype' , 3);//check for user
		 	
    	$q->addtable($xartable['rolemembers'],'rm');
    	$q->join('r.id','rm.role_id');
    	/*Psspl:get the selected group only 
    	*/
    	//Check condition for selecting all group.
    	if ($allowedSendMessages[0] != 0 or $allowedSendMessages != null ) {
	    	//$allowedGroups = explode(",",$allowedSendMessages);
	    	foreach ($allowedSendMessages as $key => $value)
	    	{
	    		$group_c[]=$q->peq('rm.parent_id' , $value);//select the users only from selected group. 
	    	}
	    	
	    	$q->qor($group_c); //set or condition
    	}
    	
    	//function for echo the query.
    	//$q->qecho();
    	  	
    	if(!$q->run()) return;
	
   		$users = $q->output();

    return $users;
}
?>    