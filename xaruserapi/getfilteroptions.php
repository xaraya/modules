<?php
/**
 * Get status options
 *
 */
function tasks_userapi_getfilteroptions()
{
    $options = array();
    $options[] = array('id'=>0,'name'=>xarML('Default'));
    $options[] = array('id'=>1,'name'=>xarML('My tasks'));
    $options[] = array('id'=>1,'name'=>xarML('Available tasks'));
    $options[] = array('id'=>1,'name'=>xarML('Priority list'));
    $options[] = array('id'=>1,'name'=>xarML('Recent activity'));
    return $options;
}

?>
