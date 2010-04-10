<?php
/**
 * Admin Configuration function
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
 * Create a new configuration
 *
 * @param  array $item
 * @return int ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function tinymce_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($name) || empty($name) || !is_string($name)) {
        $invalid[] = 'Name type';
    }

  
    //check for duplicates
    $check = xarModAPIFunc('tinymce','user','getall',array('name'=>$name));
    
    if (is_array($check) && count($check)>=1) {
        $invalid[] = 'Name already exists';
    }
  

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'xarTinyMCE');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddTinyMCE', 1, 'Item', "All:{$name}")) {
        return;
    }  
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $tinymcetable = $xartable['tinymce'];

    $nextId = $dbconn->GenId($tinymcetable);
    $query = "INSERT INTO $tinymcetable (
              xar_id,
              xar_name,
              xar_desc,
              xar_jsstring,
              xar_gzstring,
              xar_conftype,
              xar_options,
              xar_active)
            VALUES (?,?,?,?,?,?,?,?)";
   
    $bindvars = array($nextId, (string) $name, (string)$desc,(string)$jsstring, (string)$gzstring, (int)$conftype,(string)$options,(bool)$active);
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
     if (empty($id) || !is_numeric($id) || $id == 0) {
        $id = $dbconn->PO_Insert_ID($tinymcetable, 'xar_id');
    }
    $item = $args;
    $item['module'] = 'tinymce';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'create', $id, $item);
    /* Return the id of the newly created item to the calling process */

    return $id;
}
?>