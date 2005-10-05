<?php
/**
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
function ping_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object id', 'admin', 'updatehook', 'ping');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'updatehook', 'ping');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    $itemlinks = xarModAPIFunc($modname,'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);

    if (isset($itemlinks[$itemid]) && !empty($itemlinks[$itemid]['url'])) {
        // normally we should have url, title and label here
        foreach ($itemlinks[$itemid] as $field => $value) {
            $item[$field] = $value;
        }
    } else {
        $item['url'] = xarModURL($modname,'user','display',
                                 array('itemtype'   => $itemtype,
                                       'itemid'     => $itemid));
    }

    $rsslink = xarModURL($modname,'user','display',
                                 array('itemtype'   => $itemtype,
                                       'itemid'     => $itemid,
                                       'theme'      => 'rss'));

    $links = xarModAPIFunc('ping',
                           'user',
                           'getall');

    $sitename = xarModGetVar('themes', 'SiteName');
    // Init the RPC Server
    include_once 'modules/xmlrpcserver/xarincludes/xmlrpc.inc';
    include_once 'modules/xmlrpcserver/xarincludes/xmlrpcs.inc';

    foreach ($links as $link) {
        $url = parse_url($link['url']);
        if (!empty($link['method'])){
            $client         = new xmlrpc_client($url['path'], $url['host'], 80);
            $message        = new xmlrpcmsg("weblogUpdates.ping", array(new xmlrpcval($sitename), new xmlrpcval($item['url'])));
            $result         = $client->send($message);
            if (!$result || $result->faultCode()) {
                return $extrainfo;
            }
            unset($client);
        } else {
            if (xarThemeIsAvailable('rss')){
                $client         = new xmlrpc_client($url['path'], $url['host'], 80);
                $message        = new xmlrpcmsg("rssUpdate", array(new xmlrpcval($sitename), new xmlrpcval($rsslink)));
                $result         = $client->send($message);
                if (!$result || $result->faultCode()) {
                    return $extrainfo;
                }
            }
            unset($client);
        }
    }

    // Return the extra info
    return $extrainfo;
}
?>