<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
include_once("./modules/commonutil.php");
function messages_user_display( )
{
    // Security check
    if (!xarSecurityCheck('ReadMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to read messages.');
    }

    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox')) return;

    xarVarFetch('startnum', 'int', $startnum , 1 , XARVAR_NOT_REQUIRED);

    $data['startnum'] = $startnum ;
    
    //Psspl:Added the code for paging
    $link_data = xarModAPIFunc('messages', 
           					   'user', 
    						   'get_prev_next_link',
    						    array('folder'   => $folder,
    							      'startnum' => $startnum));
    
    $data = array_merge($data,$link_data);
       
    $read_messages = xarModUserVars::get('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

	//Psspl:Added the code for configuring the user-menu
	$data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
    
	//psspl:Added the code for resolving issue of modifing draft messages.
	if ($folder != 'drafts') {
   		$messages = xarModAPIFunc('messages', 'user', 'getall', array('folder' => $folder,
   																		'startnum' => $startnum));
    } else {
    	$messages = xarModAPIFunc('messages', 'user', 'checkdraft' , array('startnum' => $startnum));	
    }
    
    if (is_array($messages)) {

        //Psspl:Comment the code for sorting messages.
    	//krsort($messages);

        $data['messages']                = $messages;
        //Psspl:Added the code for attaching foder type to the link.
       foreach($messages as  $key=>$message){
        	
        	if(isset($data['messages'][$key]['view_link']))
        		$data['messages'][$key]['view_link']       .= "&folder=$folder"; 
        	
        	if(isset($data['messages'][$key]['modify_link']))
        		$data['messages'][$key]['modify_link']     .= "&folder=$folder"; 
        	
        	if(isset($data['messages'][$key]['delete_link']))
        		$data['messages'][$key]['delete_link'] 	   .= "&folder=$folder"; 
        }
        
        //Psspl:Added the code for read unread messages.
        /*$messages_inbox = xarModAPIFunc('messages', 'user', 'getall', array('folder' => 'Inbox'));
        $unread = 0;
        foreach($messages_inbox as $k => $message) {
            if($message['status_alt'] == 'unread') {
               $unread++;
            }
        } 
		*/ 
        $data['header_attachment_image'] = xarTplGetImage('attachment.png');
        $data['header_status_image']     = xarTplGetImage('check_read.gif');
        $data['unread']                  = xarModAPIFunc('messages','user','count_unread');
        $data['sent']                    = xarModAPIFunc('messages','user','count_sent');
        $data['total']                   = xarModAPIFunc('messages','user','count_total');
        $data['drafts']                  = xarModAPIFunc('messages','user','count_drafts');
		//$data['unread']  				 = $unread;
    } else {
        $list = array();
    }
    if (xarUserIsLoggedIn()) {
        if (!xarVarFetch('away','str',$away,null,XARVAR_NOT_REQUIRED)) return;
        if (isset($away)) {
            xarModUserVars::set('messages','away_message',$away);
        }
        $data['away_message'] = xarModUserVars::get('messages','away_message');
    } else {
        $data['away_message'] = '';
    }

    $data['folder'] = xarML(ucfirst($folder));

    return $data;
}

?>
