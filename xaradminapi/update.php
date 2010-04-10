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
 * Update an editor config instance
 *
 * @param  int      $args ['id'] the ID of the item
 * @param  string   $args ['name'] the new name of the item
 * @param  string   $args['desc'] the description of the item
 * @param  string   $args['jsstring'] the javascript options
 * @param  string   $args['gzstring'] the javascript for gzip options
 * @param  int      $args['conftype'] type of config (0 - all, 1 - module , 2-role)
 * @param  string   $args['options'] serialized array of options
 * @param  bool     $args['active'] active or not 
 * @return bool true on success of update
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function tinymce_adminapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'item ID';
    }

    //check exists
    $items = xarModAPIFunc('tinymce','user','getall',array('id'=>$id));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $olditem = current($items);
    if (isset($name) && !empty($name)) {
        //check for duplicate name
        $check = xarModAPIFunc('tinymce','user','getall',array('name'=>$name));

        if (is_array($check) && count($check) >0) {
            $checkitem = current($check);
            if ($checkitem['id'] !=$id) {
                $invalid[] = 'Name already exists';
            }
        }
    }    

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Example');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }


    if (!xarSecurityCheck('EditTinyMCE', 1, 'Item', "All:{$name}")) {
        return;
    }
    if (!isset($name) || empty($name)) $name = $olditem['name'];
    if (!isset($desc) || empty($desc)) $desc= $olditem['desc'];    
    if (!isset($gzstring) || empty($gzstring)) $gzstring = $olditem['gzstring'];
    if (!isset($jsstring) || empty($jsstring)) $jsstring = $olditem['jsstring'];
    if (!isset($conftype) || empty($conftype)) $conftype = $olditem['conftype'];
    if (!isset($options) || empty($options)) $options = $olditem['options'];            
    $active =  (isset($active)) ? $active:FALSE;
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $tinymcetable = $xartable['tinymce'];

    $query = "UPDATE $tinymcetable
            SET xar_name =?, 
                xar_desc = ?,
                xar_jsstring= ?,
                xar_gzstring=?,
                xar_conftype =?,
                xar_options = ?,
                xar_active = ?
            WHERE xar_id = ?";

    $bindvars = array((string)$name, (string)$desc, (string)$jsstring,(string)$gzstring,(int)$conftype,(string)$options, (bool)$active, $id);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $item['module'] = 'tinymce';
    $item['id'] = $id;
    $item['itemtype'] = 0;
    $item['name'] = $name;
    $item['desc'] = $desc;
    xarModCallHooks('item', 'update', $id, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>