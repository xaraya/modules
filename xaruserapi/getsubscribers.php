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
 * Get the subscriptions for a particular event
 *
 * @param $args['eventid'] the event id we're looking for
 * @return array of events
 */
function pubsub_userapi_getsubscribers($args)
{
    $subscriptions = array();
    /*
     * lets get...
     *  - username (need to get from db)
     *  - subscribe date (need to get from db)
     *  - category name (should have from passed in cid
     *  - ??

     */
    extract($args);
    $events = array();
    if (empty($eventid) || !is_numeric($eventid)) {
        return $events;
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $rolestable           = $xartable['roles'];
    $modulestable         = $xartable['modules'];
    $pubsubeventstable    = $xartable['pubsub_events'];
    $pubsubsubscriptionstable       = $xartable['pubsub_subscriptions'];

    $query = "SELECT $rolestable.uname  AS username
                    ,$modulestable.name AS modname
                    ,$pubsubeventstable.modid AS modid
                    ,$pubsubeventstable.itemtype AS itemtype
                    ,$pubsubeventstable.cid AS cid
                    ,$pubsubsubscriptionstable.subdate AS subdate
                    ,$pubsubsubscriptionstable.pubsubid AS pubsubid
                    ,$pubsubsubscriptionstable.email AS email
                    ,$pubsubsubscriptionstable.userid AS userid
                FROM
                    $modulestable
                    ,$pubsubeventstable
                    ,$pubsubsubscriptionstable LEFT JOIN $rolestable ON ($pubsubsubscriptionstable.userid     = $rolestable.id)
               WHERE $pubsubeventstable.eventid = $pubsubsubscriptionstable.eventid
                 AND $pubsubeventstable.modid   = $modulestable.regid
                 AND $pubsubeventstable.eventid = $eventid";

    $result = $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($username
            ,$modname
            ,$modid
            ,$itemtype
            ,$cid
            ,$subdate
            ,$pubsubid
            ,$email
            ,$userid
           ) = $result->fields;
        if (xarSecurityCheck('AdminPubSub', 0))
        {
            if( $userid == -1 )
            {
                $emailinfo = explode(' ',$email,2);
                $username    = $emailinfo[0];
                if( isset($emailinfo[1]) )
                {
                    $displayname = $emailinfo[1];
                } else {
                    $displayname = '';
                }
            } else {
                $displayname = '';
            }

            $subscriptions[] = array('username'  => $username
                                  ,'displayname' => $displayname
                                  ,'modname'   => $modname
                                  ,'modid'     => $modid
                                  ,'itemtype'  => $itemtype
                                  ,'cid'       => $cid
                                  ,'subdate'   => $subdate
                                  ,'pubsubid'  => $pubsubid
                                  );
        }
    }

    $result->Close();

    return $subscriptions;

}

?>