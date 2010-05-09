<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * get information about all scheduler jobs
 *
 * @author mikespub
 * @return array of jobs and their info
 */
function scheduler_userapi_getall($args)
{
    $serialjobs = xarModVars::get('scheduler','jobs');
    if (empty($serialjobs)) {
        $jobs = array();
    } else {
        $jobs = unserialize($serialjobs);
    }
    return $jobs;
}

?>
