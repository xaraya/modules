<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

function gallery_adminapi_delete_file($args)
{
    extract($args);

    if( empty($file_id) ){ return false; }

    $file = xarModAPIFunc('gallery', 'user', 'get_file',
        array(
            'file_id' => $file_id
        )
    );

    $file_path = xarModGetVar('gallery', 'file_path');
    $r =@ unlink($file_path.$file['file']);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $files_table = $xartable['gallery_files'];
    $linkage_table = $xartable['gallery_files_linkage'];

    $sql = " DELETE FROM $files_table WHERE file_id = ? ";
    $bindvars = array($file_id);
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    $sql = " DELETE FROM $linkage_table WHERE file_id = ? ";
    $bindvars = array($file_id);
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    return true;
}
?>