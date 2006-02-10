<?php
/**
 * Get status options
 *
 */
function tasks_userapi_getdepthoptions($args)
{
    extract($args);
    $maxdepth = isset($maxdepth) ? $maxdepth : 1;
    $options = array();
    for($x=1; $x<=$maxdepth; $x++) {
        $options[] = array('id'=>$x, 'name'=>$x);
    }
    return $options;
}

?>