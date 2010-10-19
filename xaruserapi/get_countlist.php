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
 * Get the number of comments for a list of module items
 *
 * @author  mikespub
 * @access  public
 * @param   integer   $modid        the id of the module that these nodes belong to
 * @param   integer   $itemtype     the item type that these nodes belong to
 * @param   array     $objectids    (optional) the list of ids of the items that these nodes belong to
 * @param   integer   $startdate    (optional) comments posted at startdate or later
 * @returns array     the number of comments for the particular modid/objectids pairs,
 *                    or raise an exception and return false.
 */
function comments_userapi_get_countlist($args)
{
    extract($args);
    // $modid, $objectids

    $exception = false;

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_countlist', 'comments');
        throw new BadParameterException($msg);
        $exception |= true;
    }


    if ( !isset($objectids) || !is_array($objectids) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'objectids', 'userapi', 'get_countlist', 'comments');
        throw new BadParameterException($msg);
        $exception |= true;
    }

    if ($exception) {
        return false;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql = "SELECT  objectid, COUNT(id) as numitems
              FROM  $xartable[comments]
             WHERE  modid=$modid
               AND  status="._COM_STATUS_ON;

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND itemtype=$itemtype";
    }

    if ( isset($objectids) && is_array($objectids) ) {
        $sql .= " AND  objectid IN ('" . join("', '",$objectids) . "')";
    }

    if (!empty($startdate) && is_numeric($startdate)) {
        $sql .= " AND date>=$startdate";
    }
 
    $sql .= " GROUP BY  objectid";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    $count = array();
    while (!$result->EOF) {
        list($id,$numitems) = $result->fields;
        $count[$id] = $numitems;
        $result->MoveNext();
    }
    $result->Close();

    return $count;
}

?>
