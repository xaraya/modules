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

function photoshare_userapi_moveimage($args)
{
    extract($args);

    if (!isset($imageID) && !isset($image)) {
        $msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
        'imageID', 'userapi', 'moveimage', 'Photoshare');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($position)) {
        $msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
        'position', 'userapi', 'moveimage', 'Photoshare');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($image))
        $image = xarModAPIFunc('photoshare', 'user', 'getimages', array('imageID' => $imageID));

    $srcPosition  = $image['position'];

        // If moving from left to right then adjust destition one to the left
    if ($srcPosition < $position)
        --$position;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $imagesTable = $xartable['photoshare_images'];

    // First update moves all images after current image one to the left.
    $sql = "UPDATE $imagesTable SET
            ps_position = ps_position - 1
            WHERE ps_parentfolder = ?
            AND ps_position >= ?";
    $result =& $dbconn->Execute($sql,array($image['parentfolder'], $srcPosition+1));
    if (!$result) return;

    // Next update moves all images after new position one to the right.
    unset($result);
    $sql = "UPDATE $imagesTable SET
            ps_position = ps_position + 1
            WHERE ps_parentfolder = ?
            AND ps_position >= ?";
    $result =& $dbconn->Execute($sql,array($image['parentfolder'], $position));
    if (!$result) return;

    unset($result);
    $sql = "UPDATE $imagesTable SET
            ps_position = ?
            WHERE ps_id = ?";
    $result =& $dbconn->Execute($sql,array($position, $image['id']));
    if (!$result) return;

    return true;
}

?>
