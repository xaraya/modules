<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * @TODO: point 'getparents()' to this function.
 * Get ancestors (starting with parent, working towards root) of a specific
 * [list of] category. This function used to be 'getparents', the new name
 * being less ambiguous (see XLST AxisNames for examples).
 *
 * @param $args['cid'] id of category to get children for, or
 * @param $args['cids'] array of category ids to get children for
 * @param $args['self'] =Boolean= return the cid itself (default true)
 * @param $args['return_itself'] alias of 'self'
 * @param $args['order'] 'root' or 'self' first; default 'root' (i.e. oldest ancestor first)
 * @param $args['descendants'] array to determine how descendants will be returned: 'tree', 'lists', 'list', ['none']
 * @return array Array of category info arrays, false on failure
 */
function categories_userapi_getancestors($args)
{
    // Cache each database retrieval.
    // The final result will be an array of aliases (aka pointers) into this cache.
    static $cached = array();

    // Extract the arguments.
    extract($args);

    // The order defaults to 'root' - oldest first.
    if (!isset($order)) {$order = 'root';}

    // 'return_itself' is an alias of 'self'.
    if (isset($return_itself)) {$self = $return_itself;}
    if (!isset($self)) {$self = true;}

    // Put the single cid into the array of cids for convenience.
    if (!isset($cids) || !is_array($cids)) {$cids = array();}
    if (!empty($cid)) {array_push($cids, $cid);}

    if (empty($descendants)) {$descendants = 'none';}

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
        $xartable =& xarDBGetTables();
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
                      WHERE ';
        // Do the most restrictive clause first, this helps mysql's tiny brain
        if (count($dbcids) > 1) {
            //@todo: bind variables!
            $SQLquery .= 'P2.xar_cid in (' . implode(', ', $dbcids) . ')';
        } else {
            //@todo: bind variable!
            $SQLquery .= 'P2.xar_cid = ' . $dbcids[0];
        }
        $SQLquery .= ' AND P2.xar_left >= P1.xar_left
                       AND P2.xar_left <= P1.xar_right';

        // This order retrieved the oldest ancestor first.
        //$SQLquery .= ' ORDER BY P1.xar_left';

        // Get database connection info and execute the query.
        $dbconn =& xarDBGetConn();
        $result = $dbconn->Execute($SQLquery);
        if (!$result) {return;}

        while (!$result->EOF) {
            list($dbcid, $name, $description, $image, $parent, $left, $right) = $result->fields;

            // Add the category into the cache where necessary.
            if (!isset($cached[$dbcid])) {
                $cached[$dbcid] = array(
                    "cid"         => (int)$dbcid,
                    "name"        => $name,
                    "description" => $description,
                    "image"       => $image,
                    "parent"      => (int)$parent,
                    "left"        => (int)$left,
                    "right"       => (int)$right
                );
            }
            $result->MoveNext();
        }
    }

    // Now build up the results array from the cached details.
    $info = array();

    // Loop for each starting cid.
    foreach ($cids as $cid) {
        if (!isset($cached[$cid])) {continue;}
        // Keep a trace of descendants as we walk back up the tree.
        // The descendants are not cached as they will vary
        // depending upon where the ancestor walk starts from.
        $descendantsforcat = array($cid);

        // 'self' added only if required.
        if (!isset($info[$cid]) && $self) {
            $info[$cid] = $cached[$cid];
        }

        // Tranverse remaining ancestors until we either hit a root node.
        // TODO: put a limit on the loop in case of infinite loops.
        $nextcid = $cached[$cid]['parent'];
        while ($nextcid > 0) {

// TODO: what if we have no permission to access one of the ancestors ?
// cfr. getparents() but more difficult because of the caching + descendants stuff :-)

            if (!isset($info[$nextcid])) {
                $info[$nextcid] = $cached[$nextcid];
            }

            if ($descendants == 'lists') {
                // Store the descendant trail against this category.
                $info[$nextcid]['descendants'][] = $descendantsforcat;
            }

            if ($descendants == 'list') {
                // Store the descendant trail against this category.
                if (!isset($info[$nextcid]['descendants'])) {
                    $info[$nextcid]['descendants'] = array();
                }
                $info[$nextcid]['descendants'] = array_unique(
                    array_merge($descendantsforcat, $info[$nextcid]['descendants'])
                );
            }

            // Add this descendant onto the list for the next category up.
            array_unshift($descendantsforcat, $nextcid);

            $nextcid = $cached[$nextcid]['parent'];
        }

    }

    if ($order == 'root') {
        // The ancestors need to be returned in order, oldest first.
        // We build the list starting at self, so we walk the tree
        // in the reverse of that order. The array is reversed,
        // preserving the keys.

        $info = array_reverse($info, true);
    }

    //echo "<pre>"; var_dump($info); echo "</pre><br/>";

    return $info;
}

?>
