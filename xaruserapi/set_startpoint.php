<?php

function navigator_userapi_set_startpoint( $args )
{
    if (!isset($args['startpoint'])) {
        return;
    } else {
        $startpoint = $args['startpoint'];
    }

    if (!isset($args['tree']) || empty($args['tree'])) {
        return;
    } else {
        $tree = &$args['tree'];
    }

    foreach ($tree as $key => $branch) {
        if ($branch['cid'] == $startpoint) {
            $tree[$key]['startpoint'] = 1;
            return;
        } else {
            if (isset($branch['children']) && count($branch['children'])) {
                navigator_userapi_set_startpoint(array('startpoint' => $startpoint,
                                                       'tree' => &$tree[$key]['children']));
            }
        }
    }
}

?>