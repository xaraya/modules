<?php
/**
 * Get status options
 *
 */
function tasks_userapi_getstatusoptions()
{
    $options = array();
    $options[] = array('id'=>0,'name'=>xarML('Open'));
    $options[] = array('id'=>1,'name'=>xarML('Closed'));
    return $options;
}

?>