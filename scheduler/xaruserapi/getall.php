<?php

/**
 * get information about all scheduler jobs
 * 
 * @author mikespub
 * @returns array
 * @return array of jobs and their info
 */
function scheduler_userapi_getall($args)
{
    $serialjobs = xarModGetVar('scheduler','jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }
    return $jobs;
}

?>
