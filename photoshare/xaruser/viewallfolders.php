<?php
/**
 * Photoshare by Jorn Lind-Nielsen (C) 2002.
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Jorn Lind-Nielsen / Chris van de Steeg
 */
 
function photoshare_user_viewallfolders()
{
	if (!xarSecurityCheck('ViewFolder')) return;

	// Create list of folders
	if(!xarVarFetch('order', 'str', $order,  'title', XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('fid', 'int', $topFolderID,  NULL, XARVAR_NOT_REQUIRED | XARVAR_DONT_SET)) {return;}
	$user = xarUserGetVar('uid');
	
	xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View all folders'))); 
	
	$listName = xarModGetVar('photoshare', 'mainlist');
	
	if (substr($listName, 0, 6) == 'nested') {
		$folders =  xarModAPIFunc('photoshare', 'user', 'getfolders',
		                         array('user'            => $user,
		                               'getForList'      => true,
		                               'order'           => $order,
		                               'parentFolderID'  => $topFolderID,
		                               'countSubFolders' => true,
									   'prepareForDisplay' => true) );
	} else {
		$folders =  xarModAPIFunc('photoshare', 'user', 'getfolders',
		                         array('user'       => $user,
		                               'getForList' => true,
		                               'order'      => $order,
		                               'countSubFolders' => false,
									   'prepareForDisplay' => true) );
	}

	if (isset($topFolderID) && $topFolderID != -1)
	{
		$topFolder =   xarModAPIFunc('photoshare', 'user', 'getfolders',
		                         array('getForList' => false,
		                               'folderID' 	=> $topFolderID,
									   'prepareForDisplay' => true)
		                       	);
	}

	if (!isset($folders)) return;

	$data = array();
	$data['folders'] = array();

	foreach ($folders as $folder) {
		if (xarSecurityCheck('ViewFolder', 0, 'Folder', "$folder[id]:$folder[owner]:$folder[parentFolder]")) {
			$folder['folderURL'] =	xarModUrl('photoshare', 'user', 'showimages', array('fid' => $folder['id']));
		} else {
			$folder['folderURL'] = 'javascript:void(0)';
		}

		// Add this item to the list of items to be displayed
		$data['folders'][] = $folder;
	}

	$menuHide = array( '_PSMENUTHISFOLDER'     => (!isset($topFolder) || !xarSecurityCheck('EditFolder', 0, 'item', $topFolder['id'].":".$topFolder['owner'].":".$topFolder['parentFolder'])),
                       '_PSMENUADDFOLDER'      => true,
                       '_PSMENUADDIMAGES'      => true,
                       '_PSMENUFOLDEREDIT'     => true,
                       '_PSMENUVIEWFOLDER'     => (!isset($topFolder) || !xarSecurityCheck('ViewFolder', 0, 'item', $topFolder['id'].":".$topFolder['owner'].":".$topFolder['parentFolder'])),
                       '_PSMENUFOLDERDELETE'   => true,
                       '_PSMENUPUBLISHALBUM'   => true,
                       '_PSMENUVIEWALLFOLDERS' => true,
                       '_PSMENUMYFOLDERS'      => false);

   	$data['menuitems'] = xarModAPIFunc('photoshare', 'user', 'makemainmenu',
   				array(	'menuHide' => $menuHide
   					)
   			);

	$data['order'] = $order;
	if (isset($topFolder))
		$data['title'] = $topFolder['title'];
	else
		$data['title'] = '('.xarMl('No album selected').')';

	$data['top'] = xarModUrl('photoshare', 'user', 'viewallfolders');
	
	return xarTplModule('photoshare', 'user', 'list', $data, $listName);
}
?>
