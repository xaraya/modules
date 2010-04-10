<?php
/**
 * Delete an editor config instance
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2008-2009 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Delete an editor config instance
 *
 *
 * @param  $args ['id'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function tinymce_adminapi_delete($args)
{
    extract($args);

    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'tinymce');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
   //check for existance
    $item = xarModAPIFunc('tinymce','user','getall',array('id'=>$id));
   
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    
     if (!xarSecurityCheck('DeleteTinyMCE',1)) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $tinymcetable = $xartable['tinymce'];

    $query = "DELETE FROM $tinymcetable WHERE xar_id = ?";

    $result = &$dbconn->Execute($query,array($id));

    if (!$result) return;
    $item['module'] = 'tinymce';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>