<?php
/**
 *
 * @param  names   array    array of id => name pairs
 * @param  tree    array    the nested array we want to search and modify
 *
 */

function navigator_userapi_set_names( $args )
{
    if (!isset($args['names'])) {
        return;
    } else {
        if (!is_array($args['names'])) {
            return;
        } else {
            $names = $args['names'];
        }
    }

    // If the tree is missing - give up
    if (!isset($args['tree']) || empty($args['tree'])) {
        return;
    } else {
        $tree = &$args['tree'];
    }

    foreach ($tree as $key => $branch) {
        $cid = $branch['cid'];
        // If there is a matching id in our list of
        // id => name pairs, then switch the name value
        // of the current node in the tree to the new one
        // specified in the names array
        if (isset($names[$cid])) {
            $tree[$key]['name'] = $names[$cid];
        }

        // Make sure to check for any children and change their names as needed
        if (isset($branch['children']) && count($branch['children'])) {
            navigator_userapi_set_names(array('names' => $names,
                                              'tree' => &$tree[$key]['children']));
        }
    }
}

?>