<?php


function navigator_userapi_nested_tree_sort( $args )
{
    $tree = &$args['tree'];

    if (isset($args['direction']) && stristr(strtolower($args['direction']), 'desc')) {
        $dir = 'desc';
    } else {
        $dir = 'asc';
    }

    switch ($dir) {
        case 'desc':
            krsort($tree);
            break;
        default:
            ksort($tree);
            break;
    }

    foreach ($tree as $key => $branch) {
        if (isset($branch['children']) && count($branch['children'])) {
            navigator_userapi_nested_tree_sort(array('tree' => &$tree[$key]['children']));
        }
    }
}

?>