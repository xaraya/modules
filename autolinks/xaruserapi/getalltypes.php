<?php

/**
 * get all link types
 * @param $args['startnum'] start number (optional)
 * @param $args['numitems'] number of items (optional)
 * @param $args['template_name'] for a given template name (optional)
 * @param $args['type_name'] for a given type name (optional)
 * @returns array
 * @return link type array, or false on failure
 */
function autolinks_userapi_getalltypes($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $where = array();
    $bind = array();

    if (isset($template_name)) {
        $where[] = 'xar_template_name like ?';
        $bind[] = $template_name;
    }

    if (isset($type_name)) {
        $where[] = 'xar_type_name like ?';
        $bind[] = $type_name;
    }

    if (!empty($where)) {
        $where = ' WHERE ' . implode(' and ', $where);
    }

    // Security Check
    if(!xarSecurityCheck('ReadAutolinks')) {return;}

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstypestable = $xartable['autolinks_types'];

    // Initialise
    $linktypes = array();

    // Get link
    $query = 'SELECT xar_tid,
                    xar_dynamic_replace,
                    xar_template_name,
                    xar_type_name,
                    xar_link_itemtype,
                    xar_type_desc
            FROM    ' . $autolinkstypestable . ' ' . $where;

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
    if (!$result) {return;}

    for (; !$result->EOF; $result->MoveNext()) {
        list(
            $tid, $dynamic_replace, $template_name, $type_name, $itemtype, $type_desc
        ) = $result->fields;

        // TODO: security on link types?
        $linktypes[$tid] = array(
            'tid' => $tid,
            'dynamic_replace' => $dynamic_replace,
            'template_name' => $template_name,
            'type_name' => $type_name,
            'itemtype' => $itemtype,
            'type_desc' => $type_desc
        );
    }

    $result->Close();

    return $linktypes;
}

?>