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
    // Cache each database retrieval.
    // The final result will be an array of aliases (aka pointers) into this cache.
    static $cached = array();

    // Extract the arguments.
    extract($args);

    // 'return_itself' is an alias of 'self'.
    if (isset($return_itself)) {$self = $return_itself;}
    if (!isset($self)) {$self = true;}

    // Put the single cid into the array of cids for convenience.
    if (!isset($cids) || !is_array($cids)) {$cids = array();}
    if (!empty($cid)) {array_push($cids, $cid);}

    // Filter out non-numeric array values.
    $cids = array_filter($cids, 'is_numeric');

    // Check mandatory arguments.
    if (empty($cids)) {
        // TODO: can the exception handling system support us better here?
        // We shouldn't have to set our own error stacks.
        xarSessionSetVar('errormsg', xarML('Bad arguments for API function'));
        return false;
    }

    // Only retrieve from the database for categories we have not
    // already cached. Create an array of cids we actually want to
    // scan in the database.
    $dbcids = array();

    // Remove cids we have already cached.
    foreach ($cids as $loopcid) {
        if (!isset($cached[$loopcid])) {array_push($dbcids, $loopcid);}
    }

    // Only do the database stuff if there are uncached cids to fetch.
    if (!empty($dbcids)) {
        $xartable = xarDBGetTables();
        $categoriestable = $xartable['categories'];

        // TODO : evaluate alternative with 2 queries
        $SQLquery = 'SELECT DISTINCT
                            P1.xar_cid,
                            P1.xar_name,
                            P1.xar_description,
                            P1.xar_image,
                            P1.xar_parent,
                            P1.xar_left,
                            P1.xar_right
                       FROM '.$categoriestable.' AS P1,
                            '.$categoriestable.' AS P2
                      WHERE P2.xar_left
                         >= P1.xar_left
                        AND P2.xar_left
                         <= P1.xar_right';

        // xarVarPrepForStore() only helps us if the cid is enclosed
        // in single quotes, i.e. is a string. We have already checked
        // it is numeric further up, so we don't need a further check.
        
        if (count($dbcids) > 1) {
            $SQLquery .= ' AND P2.xar_cid in (' . implode(', ', $dbcids) . ')';
        } else {
            $SQLquery .= ' AND P2.xar_cid = ' . $dbcids[0];
        }

        $SQLquery .= ' ORDER BY P1.xar_left';

        // Get database connection info and execute the query.
        list($dbconn) = xarDBGetConn();
        $result = $dbconn->Execute($SQLquery);
        if (!$result) {return;}

        while (!$result->EOF) {
            list($dbcid, $name, $description, $image, $parent, $left, $right) = $result->fields;

            // Add the category into the cache where necessary.
            if (!isset($cached[$dbcid])) {
                $cached[$dbcid] = array(
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
    }

    // Now build up the results array from the cached details.
    $info = array();

    // Loop for each starting cid.
    foreach ($cids as $cid) {
        // 'self' added only if required.
        if (!isset($info[$cid]) && $self) {
            $info[$cid] = $cached[$cid];
        }
        // Tranverse remaining ancestors until we either hit the end
        // or an ancestor that has already been set.
        $nextcid = $cached[$cid]['parent'];
        while ($nextcid > 0 && !isset($info[$nextcid])) {
            $info[$nextcid] = $cached[$nextcid];
            $nextcid = $cached[$nextcid]['parent'];
        }
    }

    return $info;
}

?>