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

function photoshare_userapi_deletefolder($args)
{
    extract($args);

    if (!isset($folderID)) {
        $msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
        'folderID', 'userapi', 'deletefolder', 'Photoshare');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $folder = xarModAPIFunc('photoshare', 'user', 'getfolders', array('folderID' => $folderID ));
    if (!isset($folder)) return;

    if (!isset($deleteSubFolders))
        $deleteSubFolders = true;

 if ($deleteSubFolders) {
        $folders = xarModAPIFunc('photoshare', 'user', 'getfolders', array('parentFolderID' => $folderID) );
        if (!isset($folders)) return;

        // Recurse
        foreach ($folders as $folder) {
            $ok = xarModAPIFunc('photoshare',
                                'user',
                                'deletefolder',
                                array('folderID' => $folder['id'],
                                    'deleteSubFolders' => true) );
            if (!$ok) return;
        }
    } else {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $imagesTable = $xartable['photoshare_images'];
        $foldersTable = $xartable['photoshare_folders'];

        $parentFolder = $folder['parentFolder'];

        // Set sub-folder's parent to current parent
        $sql = "UPDATE $foldersTable SET
                ps_parentfolder = ?
                WHERE ps_parentfolder =?";
        $bindvars = array($parentFolder, $folderID);
        $result =& $dbconn->execute($sql,$bindvars);
        if (!isset($result)) return;
    }

    $images = xarModAPIFunc('photoshare', 'user', 'getimages', array('folderID' => $folderID ));
    foreach ($images as $image) {
        $deleted = xarModAPIFunc('photoshare', 'user', 'deleteimage', array('imageID' => $image['id']));
        if (!isset($deleted)) return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $imagesTable = $xartable['photoshare_images'];
    $foldersTable = $xartable['photoshare_folders'];

    // Delete the current folder
    $sql = "DELETE FROM $foldersTable
            WHERE ps_id = ?";

    $result =& $dbconn->Execute($sql,array($folderID));
    if (!isset($result)) return;

    return true;
}

?>
