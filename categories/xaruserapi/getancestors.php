<?php

/**
 * TODO: point 'getparents()' to this function.
 * Get ancestors (starting with parent, working towards root) of a specific
 * [list of] category. This function used to be 'getparents', the new name
 * being less ambiguous (see XLST AxisNames for examples).
 *
 * @param $args['cid'] id of category to get children for, or
 * @param $args['cids'] array of category ids to get children for
 * @param $args['self'] =Boolean= return the cid itself (default true)
 * @param $args['return_itself'] alias of 'self'
 * @returns array
 * @return array of category info arrays, false on failure
 */
function categories_userapi_getancestors($args) {
    extract($args);

    // Check mandatory arguments.
    if (!isset($cid) && !isset($cids)) {
        // TODO: can the exception handling system support us better here?
        xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
        return false;
    }

    if (isset($return_itself)) {$self = $return_itself;}
    if (!isset($self)) {$self = true;}

    $info = array();

    // Check the arguments.
    if (!empty($cids) && (is_array($cids))) {
        // Filter out non-numeric array values.
        $cids = array_filter($cids, 'is_numeric');
    }

    if ((empty($cid) || !is_numeric($cid)) && (empty($cids) || !is_array($cids))) {
        return $info;
    }

    $xartable = xarDBGetTables();
    $categoriestable = $xartable['categories'];

    // TODO : evaluate alternative with 2 queries
    $SQLquery = "SELECT DISTINCT
                        P1.xar_cid,
                        P1.xar_name,
                        P1.xar_description,
                        P1.xar_image,
                        P1.xar_parent,
                        P1.xar_left,
                        P1.xar_right
                   FROM $categoriestable AS P1,
                        $categoriestable AS P2
                  WHERE P2.xar_left
                     >= P1.xar_left
                    AND P2.xar_left
                     <= P1.xar_right";

    // xarVarPrepForStore() only helps us if the cid is enclosed
    // in single quotes, i.e. is a string. We have already checked
    // it is numeric further up.
    
    if (!empty($cids)) {
        $SQLquery .= ' AND P2.xar_cid in (' . implode(', ', $cids) . ')';
    } else {
        $SQLquery .= ' AND P2.xar_cid = ' . $cid;
    }

    $SQLquery .= ' ORDER BY P1.xar_left';

    // Get database connection info and execute the query.
    list($dbconn) = xarDBGetConn();
    $result = $dbconn->Execute($SQLquery);
    if (!$result) {return;}

    while (!$result->EOF) {
        list($dbcid, $name, $description, $image, $parent, $left, $right) = $result->fields;

        // TODO: deal with 'return_itself' for cids array.
        // Can't just pass over all the cids as some may be direct
        // ancestors of others.
        if ($self || empty($cid) || $cid <> $dbcid)
        {
            $info[$dbcid] = Array(
                "cid"         => $dbcid,
                "name"        => $name,
                "description" => $description,
                "image"       => $image,
                "parent"      => $parent,
                "left"        => $left,
                "right"       => $right
            );
        }
        $result->MoveNext();
    }

    return $info;
}

?>