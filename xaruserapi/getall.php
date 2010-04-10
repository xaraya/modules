<?php
/**
 * Getall responses
 *
 * @package modules
 * @copyright (C) 2004-2009 2skies.com 
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xarigami.com/projects/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Get all instances
 *
 * @param int  $args['numitems']    the number of items to retrieve (default -1 = all)
 * @param int  $args[' startnum']   start with this item number (default 1)
 * @param int  $args['id']          the config instance id to retrieve 
 * @param str  $args['name']       the config instance name - either id or name must be supplied
 * @param bool $args['gz get']      the gz string not the js
 * @param int  $args['itype']       type of config to get 0 - all, 1- module, 2 - role
 * @param bool $args['options']     get the options 
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function tinymce_userapi_getall($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (isset($name) && $name == 'mceEditor') {
        //just return
        return;
    }
    // Argument check
    if (isset($id) && (!is_numeric($id) || $id < 1)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'xarTinyMCE IID', 'user', 'get',
                    'xarTinyMCE');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    if (isset($name) && (!is_string($name))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'xarTinyMCE name', 'user', 'get',
                    'xarTinyMCE');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }    


    $items = array();
   // jojo - remove for now, not required here and can cause probs for anon users. Can be added back in the permissions scenario later
   // if (!xarSecurityCheck('EditTinyMCE',0)) return; //this is higher than normal as normal visitors don't get to see these

    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();

    $tinymcetable = $xartables['tinymce'];
    
    $bindvars=array();
    
   if (!empty($id)) {
        $where = "WHERE xar_id = ?";
        $bindvars[] = $id;
    } else {
        $wherelist = array();
        $fieldlist = array('id','name','desc','jsstring','gzstring','conftype','options');
        foreach ($fieldlist as $field) {
            if (isset($$field)) {
                $wherelist[] = "xar_$field = ?";
                $bindvars[] = $$field;
            }
        }
        if (count($wherelist) > 0) {
            $where = "WHERE " . join(' AND ',$wherelist);
        } else {
            $where = '';
        }
    }
    
    $query = "SELECT xar_id,
                     xar_name,
                     xar_desc,
                     xar_jsstring,
                     xar_gzstring,
                     xar_conftype,
                     xar_options,
                     xar_active
                     FROM $tinymcetable
                    $where
                    ORDER BY xar_id";
    if (!empty($id)) {
        $result = $dbconn->Execute($query,$bindvars);
    } else {
        if ($numitems > 0) {
            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars );
        } else {
            $result = $dbconn->Execute($query, $bindvars );        
        }
    }

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars );

    if (!$result) return ;
    $items = array(); //initialize our items array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,$name, $desc,$jsstring,$gzstring,$conftype,$options,$active) = $result->fields;
        if (xarSecurityCheck('ReadTinyMCE', 0, 'instance', "$id:$name")) {
            $items[$name] =array('id'        => (int)$id,
                            'name'      => $name,
                            'desc'      => $desc,
                            'jsstring'  => $jsstring,
                            'gzstring'  => $gzstring,
                            'conftype'  => $conftype,
                            'options'   => $options,
                            'active'    => $active
                            );
        }
    }
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();

    /* Return the items */
    return $items;
}
?>