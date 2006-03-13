<?php
/**
 * Get all list types
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Get the list types.
 *
 * List types are configured in the module itself
 *
 * @returns array
 * @param $args['tid'] type id (optional)
 * @param $args['type_name'] type name (optional)
 * @param $args['typekey'] key for the type list [id|name|index] (optional)
 * @return array of links, or false on failure
 */
function lists_userapi_getlisttypes($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = (-1);
    }

    // Security Check
    //if(!xarSecurityCheck('Readlists')) {return;}

    if (!isset($typekey)) {
        $typekey = 'id';
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_lists = $xartable['lists_types'];
    //$table_items = $xartable['lists_items'];

    // Extra where-clause conditions.
    $where = array();
    $bind = array();

    if (isset($type_name))
    {
        $where[] = '(xar_name = ?)';
        $bind[] = $type_name;
    }

    if (isset($tid))
    {
        $where[] = '(xar_tid = ?)';
        $bind[] = (int)$tid;
    }

    $where = implode(' AND ', $where);

    // Initialise.
    $types = array();

    // Get list types.
    $query = '
        SELECT  xar_tid,
                xar_name, xar_desc,
                xar_order_columns,
                xar_list_type_id
        FROM    ' . $table_lists . '
        WHERE   xar_type = \'T\' '
        . (!empty($where) ? ' AND ' . $where : '');

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
    if (!$result) {
        return $types;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list(
            $tid, $type_name, $type_desc, $type_order_columns, $type_group_id
        ) = $result->fields;

        //if(xarSecurityCheck('ReadLists', 0, 'All', $name.':'.$tid)) {
            $types[($typekey == 'name') ? $type_name : ($typekey == 'index' ? count($types) : (int)$tid)] = array(
                'tid' => (int)$tid,
                'type_name' => $type_name,
                'type_desc' => $type_desc,
                'type_order_columns' => $type_order_columns,
                'type_group_id' => $type_group_id
            );
        //}
    }

    $result->Close();

    return $types;
}

?>