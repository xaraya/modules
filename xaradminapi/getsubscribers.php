<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Get the subscribers for a particular event
 *
 * @param $args['eventid'] the event id we're looking for
 * @returns array
 * @return array of events
*/
function pubsub_adminapi_getsubscribers($args)
{
    $subscribers = array();
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $rolestable           = $xartable['roles'];
    $modulestable         = $xartable['modules'];
    $pubsubeventstable    = $xartable['pubsub_events'];
    $pubsubregtable       = $xartable['pubsub_reg'];

    $query = "SELECT $rolestable.xar_uname  AS username
                    ,$modulestable.xar_name AS modname
                    ,$pubsubeventstable.xar_modid AS modid
                    ,$pubsubeventstable.xar_itemtype AS itemtype
                    ,$pubsubeventstable.xar_cid AS cid
                    ,$pubsubregtable.xar_subdate AS subdate
                    ,$pubsubregtable.xar_pubsubid AS pubsubid
                    ,$pubsubregtable.xar_email AS email
                    ,$pubsubregtable.xar_userid AS userid
                FROM
                    $modulestable
                    ,$pubsubeventstable
                    ,$pubsubregtable LEFT JOIN $rolestable ON ($pubsubregtable.xar_userid     = $rolestable.xar_uid)
               WHERE $pubsubeventstable.xar_eventid = $pubsubregtable.xar_eventid
                 AND $pubsubeventstable.xar_modid   = $modulestable.xar_regid
                 AND $pubsubeventstable.xar_eventid = $eventid";

    $result =& $dbconn->Execute($query);
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

            $subscribers[] = array('username'  => $username
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

    return $subscribers;

} // END getsubscribers

?>