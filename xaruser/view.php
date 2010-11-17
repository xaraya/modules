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
 * @author Ryan Walker
 */

sys::import('modules.messages.xarincludes.defines');

function messages_user_view() {

    if (!xarSecurityCheck('ViewMessages')) return;

	if(!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', XARVAR_NOT_REQUIRED)) return;
    xarSession::setVar('messages_currentfolder', $folder); 

    $data['startnum'] = $startnum;

	if (empty($numitems)) {
		$numitems = xarModVars::get('messages', 'items_per_page');
	}

    //Psspl:Added the code for configuring the user-menu
	//$data['allow_newpm'] = xarMod::apiFunc('messages' , 'user' , 'isset_grouplist');

	switch($folder){
        case 'inbox':
			$where = 'to eq ' . xarUserGetVar('id');
			$where .= ' and recipient_delete eq ' . MESSAGES_NOTDELETED;
			$where .= ' and author_status ne ' . MESSAGES_STATUS_DRAFT;
			$data['fieldlist'] = 'from,subject,time,recipient_status';
			xarTplSetPageTitle(xarML('Inbox'));
			$data['input_title']    = xarML('Inbox');
			break;
		case 'sent':
			$where = 'from eq ' . xarUserGetVar('id');
			$where .= ' and author_delete eq ' . MESSAGES_NOTDELETED;
			$where .= ' and author_status ne ' . MESSAGES_STATUS_DRAFT;
			$data['fieldlist'] = 'to,subject,time,author_status,recipient_status';
			xarTplSetPageTitle(xarML('Sent Messages'));
			$data['input_title']    = xarML('Sent Messages');
			break;
		case 'drafts':
			$where = 'author_status eq 0';
			$where .= ' and from eq ' . xarUserGetVar('id');
			$where .= ' and author_delete eq ' . MESSAGES_NOTDELETED;
			$data['fieldlist'] = 'to,subject,time,author_status';
			xarTplSetPageTitle(xarML('Drafts'));
			$data['input_title']    = xarML('Drafts');
			break;
	}

	$sort = xarMod::apiFunc('messages','admin','sort', array(
		//how to sort if the URL or config say otherwise...
		//'object' => $object,
		'sortfield_fallback' => 'time', 
		'ascdesc_fallback' => 'DESC'
	));
	$data['sort'] = $sort;

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
							'sort' => $data['sort'],
							'where' => $where
							));
	
	$list->getItems();
    $data['list'] = $list; 
 
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
