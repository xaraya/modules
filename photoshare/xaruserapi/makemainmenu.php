<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */
 
function photoshare_userapi_makemainmenu($args)
{
	extract($args);
	$menu  = array();
	$first = true;

	if (isset($menuhide) && $menuhide === false)
		return array();
	
	$editFolder = array();
	if (isset($gotoCurrentFolder) && $gotoCurrentFolder && isset($folderID))
		$editFolder['fid'] = $folderID;
	
	$modurlArgs = array();
	if (isset($folderID))
		$modurlArgs['fid'] = $folderID;
	$hasEditAccess =xarSecurityCheck('EditFolder', 0);
	
	if (!$hasEditAccess)
		$menuHide[xarMl('My albums')] = true;
	
	photoshareAddToMenu($menu, $first, $menuHide,xarMl('My albums'), 
	                  xarModURL('photoshare', 'user', 'view'), ' _PSMENUMYFOLDERS');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Edit'), 
	                  xarModURL('photoshare', 'user', 'view', $editFolder), '_PSMENUTHISFOLDER');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Add album'),
	                  xarModURL('photoshare',
	                            'user',
	                            'addfolder',
	                            $modurlArgs), '_PSMENUADDFOLDER');
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Add images'),
	                  xarModURL('photoshare',
	                                        'user',
	                                        'addimages',
	                                        $modurlArgs), '_PSMENUADDIMAGES');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Edit album'),
	                  xarModURL('photoshare',
	                                        'user',
	                                        'editfolder',
	                                        $modurlArgs), '_PSMENUFOLDEREDIT');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Show album'),
	                  xarModURL('photoshare',
	                                        'user',
	                                        'showimages',
	                                        $modurlArgs), '_PSMENUVIEWFOLDER');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Delete album'),
	                  xarModURL('photoshare',
	                                        'user',
	                                        'deletefolder',
	                                        $modurlArgs), '_PSMENUFOLDERDELETE');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('Publish album'),
	                  xarModURL('photoshare',
	                                        'user',
	                                        'editaccess',
	                                        $modurlArgs), '_PSMENUPUBLISHALBUM');
	
	photoshareAddToMenu($menu, $first, $menuHide, xarMl('All albums'), 
	                  xarModURL('photoshare',
	                                        'user',
	                                        'viewallfolders'), '_PSMENUVIEWALLFOLDERS');
	return $menu;
}


function photoshareAddToMenu(&$menu, &$first, $menuHide, $name, $item, $xarKey)
{
  if ($menuHide === false)
    return;

  if (!array_key_exists($xarKey,$menuHide) ||  $menuHide[$xarKey] == false)
  {
    $menu[] = array('link' => $item, 'name' => $name);
    $first = false;
  }
}

function photoshareGetImageMenu()
{
  return array( array( 'title' => xarMl('Show image'),					'image' => 'magnify.gif' ), 
                 array( 'title' => xarMl('Edit image'),					'image' => 'setting.gif' ), 
                 array( 'title' => xarMl('Delete image'),				'image' => 'delete.gif' ), 
	             array( 'title' => xarMl('Rotate clockwise'),			'image' => 'rotatec.gif' ), 
                 array( 'title' => xarMl('Rotate counter clockwise'),	'image' => 'rotatecc.gif' ), 
                 array( 'title' => xarMl('Set image as main image'),	'image' => 'greendot.gif' ) );
}

 
?>
