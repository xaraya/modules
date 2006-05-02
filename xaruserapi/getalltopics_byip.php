<?php
/**
 * Get all topics in a forum
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * get all topics
 *
 * @param $args['fid'] forum id, or
 * @param $args['tids'] array of topic ids
 * @returns array
 * @return array of links, or false on failure
 * @deprec 2006-05-01 - functionality now built into getalltopics()
 */
function xarbb_userapi_getalltopics_byip($args)
{
    return xarModAPIfunc('xarbb', 'user', 'getalltopics', $args);
}

?>