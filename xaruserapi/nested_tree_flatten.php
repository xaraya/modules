<?php

function navigator_userapi_nested_tree_flatten( &$tree )
{
    static $list = array();
    static $beenhere = 0;  // Used to reset list to empty

    if (!is_array($tree) || !count($tree)) {
        return array();
    }
    // if $beenhere equals zero, reset the list to empty
    if (!$beenhere) {
        $list = array();
    }

    $beenhere++;

    foreach ($tree as $key => $branch) {

        $list[$branch['ncid']] = $branch;

        if (isset($branch['children']) && count($branch['children'])) {
            $list[$branch['ncid']]['parent'] = 1;
            navigator_userapi_nested_tree_flatten(&$tree[$key]['children']);
        } else {
            $list[$branch['ncid']]['parent'] = 0;
        }

        unset($list[$branch['ncid']]['children']);
    }
    $tree = $list;

    $beenhere--;
}

?>
