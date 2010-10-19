<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get the number of comments for a module based on the author
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $author      the id of the author you want to count comments for
 * @param integer    $status    (optional) the status of the comments to tally up
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_author_count($args)
{
    extract($args);

    $exception = false;

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_count', 'comments');
        throw new BadParameterException($msg);
    }


    if ( !isset($author) || empty($author) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'author', 'userapi', 'get_count', 'comments');
        throw new BadParameterException($msg);
    }

    if (!isset($status) || !is_numeric($status)) {
        $status = _COM_STATUS_ON;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql = "SELECT  COUNT(id) as numitems
              FROM  $xartable[comments]
             WHERE  author=? AND modid=?
               AND  status=?";
    $bindvars = array((int) $author, (int) $modid, (int) $status);

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND itemtype=?";
        $bindvars[] = (int) $itemtype;
    }

// cfr. xarcachemanager - this approach might change later
    $expire = xarModVars::get('comments','cache.userapi.get_author_count');
    if (!empty($expire)){
        $result =& $dbconn->CacheExecute($expire,$sql,$bindvars);
    } else {
        $result =& $dbconn->Execute($sql,$bindvars);
    }
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>
