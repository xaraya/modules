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

function photoshare_user_addfolder()
{
    if (!xarSecurityCheck('AddFolder')) return;
    if(!xarVarFetch('fid', 'int', $folderID,  NULL, XARVAR_DONT_SET)) {return;}

    $data = array();

    if (!isset($folderID)){
        $parentFolderID = 'all';
        $data['trail'] = array();
        $data['parentFolderID'] = -1;
    } else {
        $data['parentFolderID'] = $parentFolderID = $folderID;
        $data['trail'] = xarModAPIFunc('photoshare',
                                'user',
                                'getfoldertrail',
                                array( 'folderID' => $folderID ));
    }

    if (!xarSecurityCheck('AddFolder', 1, 'folder', "all:all:$parentFolderID")) return;

    //create empty folder
    $data['folder'] = array(    'id'    => '',
                                'title' => '',
                                'owner' => '',
                                'parentFolder' => '',
                                'createdDate'   => '',
                                'ownername'     => '',
                                'modifiedDate'   => '',
                                'description'   => '',
                                'template'         => '',
                                'hideframe'    => '',
                                'blockfromlist' => '',
                                'viewkey'         => '',
                                'mainImage'     => '',
                                'imageCount'    => ''
                                );


    // Add top menu
    $data['menuitems'] = xarModAPIFunc('photoshare', 'user', 'makemainmenu',
            array(    'menuSettings' => array( 'gotoCurrentFolder' => true ),
                    'menuHide' => false
                )
        );

    $data['actionUrl'] = xarModURL('photoshare', 'user', 'createfolder', array('fid' => $folderID));
    $data['title'] = xarMl('Add album');

    $templateName = xarModGetVar('photoshare', 'defaultTemplate');
    $data['viewTemplates'] = xarModAPIFunc(    'photoshare',
                                            'user',
                                            'gettemplates',
                                            array('currentTemplate' => $templateName) );

    return xarTplModule('photoshare', 'user', 'editfolder', $data);
}

?>
