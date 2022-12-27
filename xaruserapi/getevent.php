<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * get information about a pubsub event
 *
 * @param $args['eventid'] the event id for the event
 * @return array event information on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_getevent($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($eventid) || !is_numeric($eventid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'event id',
            'user',
            'getevent',
            'Pubsub'
        );
        throw new Exception($msg);
    }

    if (!xarMod::apiLoad('categories', 'user')) {
        return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];

    $query = "SELECT $pubsubeventstable.modid,
                     $modulestable.name,
                     itemtype,
                     $pubsubeventstable.cid,
                     extra,
                     groupdescr,
                     $categoriestable.name
                FROM $pubsubeventstable
           LEFT JOIN $modulestable
                  ON $pubsubeventstable.modid = $modulestable.regid
           LEFT JOIN $categoriestable
                  ON $pubsubeventstable.cid = $categoriestable.cid
               WHERE eventid = ?";
    $result = $dbconn->Execute($query, [(int)$eventid]);
    if (!$result) {
        return;
    }

    $info = [];

    if ($result->EOF) {
        return false;
    }

    [$info['modid'],
        $info['modname'],
        $info['itemtype'],
        $info['cid'],
        $info['extra'],
        $info['groupdescr'],
        $info['catname']] = $result->fields;

    return $info;
}
