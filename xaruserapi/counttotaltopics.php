<?php
/**
 * Count the number of topics for a given forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @deprec 2006-05-01 This does the same as getalltopics() in 'count' mode. It is no longer used in this module.
*/
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of topics that match the criteria
 */

function xarbb_userapi_counttotaltopics($args)
{
    $args['getcount'] = true;

    $count = xarModAPIfunc('xarbb', 'user', 'getalltopics', $args);

    return $count;
}

?>