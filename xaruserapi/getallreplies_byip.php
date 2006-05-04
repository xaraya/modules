<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * Get a single comment or a list of comments. Depending on the parameters passed
 * you can retrieve either a single comment, a complete list of comments, a complete
 * list of comments down to a certain depth or, lastly, a specific branch of comments
 * starting from a specified root node and traversing the complete branch
 *
 * if you leave out the objectid, you -must- at least specify the author id
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @todo This is function actually returns all posters (reply authors) *not* the replies
 * @deprec Replaced by get_reply_authors()
 */

function xarbb_userapi_getallreplies_byip($args) 
{
    if (isset($args['hostname'])) $args['ip'] = $args['hostname'];

    return xarModAPIFunc('xarbb', 'user', 'get_reply_authors', $args);
}

?>