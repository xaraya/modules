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

sys::import('modules.messages.xarincludes.defines');

function messages_user_view( )
{
    if (!xarSecurityCheck('ReadMessages')) return;

	if(!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', XARVAR_NOT_REQUIRED)) return;
    xarSession::setVar('messages_currentfolder', $folder); 

    $data['startnum'] = $startnum;

	if (empty($numitems)) {
		$numitems = xarModVars::get('messages', 'items_per_page');
	}
		
    
    //Psspl:Added the code for paging
   /* $link_data = xarModAPIFunc('messages', 
                               'user', 
                               'get_prev_next_link',
                                array('folder'   => $folder,
                                      'startnum' => $startnum));*/
    
    //$data = array_merge($data,$link_data);

    //Psspl:Added the code for configuring the user-menu
//    $data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
    
    //$messages = xarModAPIFunc('messages', 'user', 'getall', array('folder' => $folder, 'startnum' => $startnum));    

	switch($folder){
        case 'inbox':
			$where = 'to eq ' . xarUserGetVar('id');
			$where .= ' and recipient_delete eq ' . MESSAGES_NOTDELETED;
			break;
		case 'sent':
			$where = 'from eq ' . xarUserGetVar('id');
			$where .= ' and author_delete eq ' . MESSAGES_NOTDELETED;
			break;
		case 'drafts':
			$where = 'author_status eq 0';
			$where .= ' and from eq ' . xarUserGetVar('id');
			$where .= ' and author_delete eq ' . MESSAGES_NOTDELETED;
			break;
	}

	$data['sort'] = 'id DESC'; // for now

	$total = DataObjectMaster::getObjectList(array(
							'name' => 'messages_messages',
							'status'    =>DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
							'numitems' => NULL,
							'where' => $where
							));
	$data['total'] = count($total->getItems());

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'messages_messages',
							'status'    =>DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
							'startnum'  => $startnum,
							'numitems' => $numitems,
							'where' => $where
							));
	
	$list->getItems();
    $data['list'] = $list; 

    /*if (is_array($messages)) {

        //Psspl:Comment the code for sorting messages.
        //krsort($messages);

        $data['messages']                = $messages;
        
        //Psspl:Added the code for read unread messages.
          
    } else {
        $list = array();
    }*/
    if (xarUserIsLoggedIn()) {
        if (!xarVarFetch('away','str',$away,null,XARVAR_NOT_REQUIRED)) return;
        if (isset($away)) {
            xarModUserVars::set('messages','away_message',$away);
        }
        $data['away_message'] = xarModUserVars::get('messages','away_message');
    } else {
        $data['away_message'] = '';
    }

    $data['folder'] = $folder;

    return $data;
}

?>
