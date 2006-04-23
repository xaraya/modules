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
 * @deprecated 23-APR-2006
*/
/**
 * get all topics
 *
 * @param $args['fid'] forum id, or
 * @param $args['tids'] array of topic ids
 * @returns array
 * @return array of links, or false on failure
 */

function xarbb_userapi_getalltopics_byuid($args)
{
    return xarModAPIfunc('xarbb', 'user', 'getalltopics', $args);
}

?>