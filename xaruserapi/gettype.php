<?php

/**
 * get a specific link type
 * @param $args['tid'] id of link type to get
 * @returns array
 * @return link type array, or false on failure
 */
function autolinks_userapi_gettype($args)
{
    extract($args);

    if (!isset($tid) && !isset($type_name)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2)', 
            'userapi', 'gettype', 'autolinks'
        );
        xarErrorSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];
    $autolinkstypestable = $xartable['autolinks_types'];

    if (isset($tid)) {
        $where = 'xar_tid = ?';
        $bind = array($tid);
    } elseif (isset($type_name)) {
        $where = 'xar_type_name = ?';
        $bind = array($type_name);
    }

    // Get link type
    $query = '
        SELECT xar_tid,
               xar_dynamic_replace,
               xar_template_name,
               xar_type_name,
               xar_link_itemtype,
               xar_type_desc
        FROM   ' . $autolinkstypestable . '
        WHERE  ' . $where;
    $result =& $dbconn->Execute($query, $bind);

    if (!$result || $result->EOF) {return;}

    list(
        $tid, $dynamic_replace, $template_name, $type_name, $itemtype, $type_desc
    ) = $result->fields;
    $result->Close();

    // Security Check
    if(!xarSecurityCheck('ReadAutolinks')) {return;}

    return array(
        'tid' => $tid,
        'dynamic_replace' => $dynamic_replace,
        'template_name' => $template_name,
        'type_name' => $type_name,
        'itemtype' => $itemtype,
        'type_desc' => $type_desc
    );
}

?>