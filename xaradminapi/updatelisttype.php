<?php
/**
 * Update an autolink
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
 * update a list type
 * @param $args['x'] TODO
 */
function lists_adminapi_updatelisttype($args)
{
    // Get arguments from argument array
    extract($args);

    // Array of column set statements.
    $set = array();
    $bind = array();

    $accepted = array(
        'name' => 'type_name',
        'desc' => 'type_desc',
        'order_columns' => 'type_order_columns'
        //'list_type_id' => 'type_parenttype'
    );

    // String parameters.
    foreach($accepted as $colname => $parameter)
    {
        if (isset($args[$parameter]))
        {
            $set[] = "xar_$colname = ?";
            $bind[] = $args[$parameter];
        }
    }

    // Argument check
    if (!isset($tid) || empty($set)) {
        $msg = xarML(
            'Invalid Parameter Count',
            join(', ', $args)
        );
        xarExceptionSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    // Create the 'set' statement.
    $set = implode(', ', $set);

    // The user API function is called
    $listtype = xarModAPIFunc(
        'lists', 'user', 'getlisttypes',
        array('tid' => $tid)
    );

    if (empty($listtype)) {
        $msg = xarML('No such list type ID #(1) present', $tid);
        xarExceptionSet(
            XAR_USER_EXCEPTION, 'MISSING_DATA',
            new DefaultUserException($msg)
        );
        return;
    }

    // TODO: make sure the name is unique.

    //if (!xarSecurityCheck('EditLists')) {return;}

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_name = $xartable['lists_types'];

    // Update the link
    $query = 'UPDATE ' . $table_name . ' SET ' . $set . ' WHERE xar_tid = ?';
    $bind[] = $tid;
    $result =& $dbconn->Execute($query, $bind);
    if (!$result) {return;}

    // Config hooks for the type as an itemtype.
    xarModCallHooks(
        'module', 'updateconfig', 'lists',
        array('module' => 'lists', 'itemtype' => $tid)
    );

    // Let the calling process know that we have finished successfully
    return true;
}

?>