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
 
function photoshare_user_view()
{
    if (!xarSecurityCheck('EditFolder')) return;
    // Create list of folders
    if(!xarVarFetch('fid', 'int', $folderID,  NULL, XARVAR_DONT_SET)) {return;}

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View personal album')));

    $user   = xarUserGetVar('uid');
    $data = array();

    if (isset($folderID)) {
        if ($folderID == -1)
            unset($folderID);
        else {
            $data['folderID'] = $folderID;
            $data['folder'] = $folder = xarModAPIFunc('photoshare',
                                'user',
                                'getfolders',
                                array( 'folderID' => $folderID, 'getForList' => false, 'prepareForDisplay' => true));
            if (!isset($folder))
                return;

            if (!xarSecurityCheck('EditFolder', 1, 'folder', "$folder[id]:$folder[owner]:$folder[parentFolder]")) return;

            // Get folder trail information
            $data['trail'] = xarModAPIFunc('photoshare',
                                'user',
                                'getfoldertrail',
                                array( 'folderID' => $folderID ));
        }
    } else {
        $data['trail'] = array();
    }

    $data['title'] = (isset($folder) ? $folder['title'] : xarMl('My albums'));

    $menuHide = array( '_PSMENUADDIMAGES'    => (!isset($folderID) || !xarSecurityCheck('AddPhoto', 0, 'item', "All:All:$folderID")),
                     '_PSMENUPUBLISHALBUM'    => (!isset($folderID)  || !xarSecurityCheck('AdminFolder', 0, 'folder', "$folder[id]:$folder[owner]:$folder[parentFolder]")),
                     '_PSMENUFOLDEREDIT'       => (!isset($folderID)  || !xarSecurityCheck('EditFolder', 0, 'folder', "$folder[id]:$folder[owner]:$folder[parentFolder]")),
                     '_PSMENUFOLDERDELETE'     => (!isset($folderID)  || !xarSecurityCheck('DeleteFolder', 0, 'folder', "$folder[id]:$folder[owner]:$folder[parentFolder]")),
                     '_PSMENUVIEWFOLDER'       => (!isset($folderID)  || !xarSecurityCheck('ReadFolder', 0, 'folder', "$folder[id]:$folder[owner]:$folder[parentFolder]")),
                     '_PSMENUTHISFOLDER'            => true);

       $data['menuitems'] = xarModAPIFunc('photoshare', 'user', 'makemainmenu',
                   array(    'menuHide' => $menuHide,
                        'gotoCurrentFolder' => true,
                        'folderID' => isset($folderID) ? $folderID : NULL
                       )
               );

    $data['addEditIcons'] = true;

       // Create list of folders
    $folders =  xarModAPIFunc('photoshare',
                           'user',
                           'getfolders',
                           array(
                                       'owner' => xarUserGetVar('uid'),
                                    'getForList' => false,
                                    'parentFolderID' => isset($folderID) ? $folderID : -1,
                                    'prepareForDisplay' => true
                                )
                );
    if (!isset($folders)) return;

    $data['folders'] = array();
    foreach ($folders as $subfolder) {
        if (xarSecurityCheck('EditFolder', 0, 'folder', "$subfolder[id]:$subfolder[owner]:$subfolder[parentFolder]")) {
            // Add this item to the list of items to be displayed
            $data['folders'][] = $subfolder;
        }
    }

    $data['images'] = array();
    if (isset($folder)) {
        $images =  xarModAPIFunc('photoshare',
                               'user',
                               'getimages',
                               array('folderID' => $folder['id'], 'prepareForDisplay' => true));
        if (!isset($images))
            return;

        foreach ($images as $image) {
            if (xarSecurityCheck('EditPhoto', 0, 'item', "$image[id]:$image[owner]:$folder[id]")) {
                $data['images'][] = $image;
            }
        }
    }

    $data['$photoshareClipboard'] = xarSessionGetVar('photoshareClipboard');
    if (!isset($data['$photoshareClipboard']))
        $data['$photoshareClipboard'] = false;
    
    $userInfo = xarModAPIFunc('photoshare', 'user', 'getuserinfo');
      if (!isset($userInfo))
          return;

      $maxSize = $userInfo['imageSizeLimitTotal'];
      $size = $userInfo['totalCapacityUsed'];
    $scale = 1000000.0;
    $data['progress_leftSize']  = $leftSize = intval($maxSize > 0 ? ($size*100)/$maxSize : 100);
    $data['progress_rightSize'] = $rightSize = intval($maxSize > 0 ? 100-$leftSize : 0);
    $data['progress_text'] = xarMl('Storage').': '.$leftSize.'%'.sprintf("(%.2f %s %.2f Mb)", $size/$scale, xarMl('of'), $maxSize/$scale);

    return $data;
}

?>
