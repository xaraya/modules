<?php
/**
 * File: $Id$
 * 
 * Ping initialization functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage ping
 * @author John Cox
 */
/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['ping_update'] from arguments
 * Optional $extrainfo['ping_url'] from arguments for url from module other than articles
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ping_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object id', 'admin', 'updatehook', 'ping');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'updatehook', 'ping');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'updatehook', 'ping');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }
/*
    if (!isset($extrainfo['ping_update']) || (!is_numeric($extrainfo['status']) && $extrainfo['status'] < 2 || $extrainfo['status'] > 3)) {

        // Don't want to send a ping if our status from the articles don't show that it is published, or if the ping_update is not there.
        return $extrainfo;
    } else {
*/
        // Need to get the correct itemtype so we send the correct URL to update
        //if ($modname == 'articles') {
            $data['viewlink'] = xarModURL('articles','user','view', array('ptid' => $extrainfo['itemtype']));
            $data['rsslink']  = xarModURL('articles','user','view', array('ptid' => $extrainfo['itemtype'], 'theme' => 'rss'));
        //} else {
            // need a better implementation
        //    $data['viewlink'] = $extrainfo['ping_url'];
        //}
        $data['sitename'] = xarModGetVar('themes', 'SiteName');
        // Init the RPC Server
        include_once 'modules/xmlrpcserver/xarincludes/xmlrpc.inc';
        include_once 'modules/xmlrpcserver/xarincludes/xmlrpcs.inc';
        // Send out the pings
        // Credit to Drupal for some inspiration here.
        xarLogMessage("ping: weblogUpdates.ping");
        $client         = new xmlrpc_client("/RPC2", "rpc.weblogs.com", 80);
        $message        = new xmlrpcmsg("weblogUpdates.ping", array(new xmlrpcval($data['sitename']), new xmlrpcval($data['viewlink'])));
        $result         = $client->send($message);
        if (!$result || $result->faultCode()) {
            $msg = xarML('Ping failed to send for #(1) function #(2)() in module #(3)', 'admin', 'updatehook', 'ping');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            // we *must* return $extrainfo for now, or the next hook will fail
            //return false;
            return $extrainfo;
        }
        unset($client);
        $client         = new xmlrpc_client("/RPC2", "api.my.yahoo.com", 80);
        $message        = new xmlrpcmsg("weblogUpdates.ping", array(new xmlrpcval($data['sitename']), new xmlrpcval($data['viewlink'])));
        $result         = $client->send($message);
        if (!$result || $result->faultCode()) {
            $msg = xarML('Ping failed to send for #(1) function #(2)() in module #(3)', 'admin', 'updatehook', 'ping');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            // we *must* return $extrainfo for now, or the next hook will fail
            //return false;
            return $extrainfo;
        }
        unset($client);
        $client         = new xmlrpc_client("/rpc/ping", "rpc.technorati.com", 80);
        $message        = new xmlrpcmsg("weblogUpdates.ping", array(new xmlrpcval($data['sitename']), new xmlrpcval($data['viewlink'])));
        $result         = $client->send($message);
        if (!$result || $result->faultCode()) {
            $msg = xarML('Ping failed to send for #(1) function #(2)() in module #(3)', 'admin', 'updatehook', 'ping');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            // we *must* return $extrainfo for now, or the next hook will fail
            //return false;
            return $extrainfo;
        }
        unset($client);
        if (xarThemeIsAvailable('rss')){
            $client         = new xmlrpc_client("/RPC2", "rssrpc.weblogs.com", 80);
            $message        = new xmlrpcmsg("rssUpdate", array(new xmlrpcval($data['sitename']), new xmlrpcval($data['rsslink'])));
            $result         = $client->send($message);
            if (!$result || $result->faultCode()) {
                $msg = xarML('Ping failed to send for #(1) function #(2)() in module #(3)', 'admin', 'updatehook', 'ping');
                xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                // we *must* return $extrainfo for now, or the next hook will fail
                //return false;
                return $extrainfo;
            }
            unset($client);
        }
        if (xarThemeIsAvailable('rss')){
            $client             = new xmlrpc_client("/", "ping.blo.gs", 80);
            $message            = new xmlrpcmsg("weblogUpdates.extendedPing", array(new xmlrpcval($data['sitename']), new xmlrpcval($data['viewlink']), new xmlrpcval($data['viewlink']), new xmlrpcval($data['rsslink'])));
            $result         = $client->send($message);
            if (!$result || $result->faultCode()) {
                $msg = xarML('Ping failed to send for #(1) function #(2)() in module #(3)', 'admin', 'updatehook', 'ping');
                xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                // we *must* return $extrainfo for now, or the next hook will fail
                //return false;
                return $extrainfo;
            }
            unset($client);
        }
    //}

    // Return the extra info
    return $extrainfo;
}
?>