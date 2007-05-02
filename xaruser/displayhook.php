<?php
/**
 * Display hook for autodiscovery of trackback
 *
 * @author   John Cox
 * @access   public
 * @returns  array      returns whatever needs to be parsed by the BlockLayout engine
 */
function trackback_user_displayhook($args)
{
    extract($args);
    // Security Check
    if(!xarSecurityCheck('Viewtrackback',0)) return '';

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'user', 'displayhook', 'trackback');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'user', 'displayhook', 'trackback');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (is_array($extrainfo) && !empty($extrainfo['module']) && is_string($extrainfo['module'])) {
        $modname = $extrainfo['module'];
    } else {
        $modname = xarModGetName();
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name ' . $modname, 'user', 'displayhook', 'trackback');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (is_array($extrainfo) && isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (is_array($extrainfo) && isset($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    // implode the arguments
    $implode['mod'] = $modid;
    $implode['it']  = $itemtype;
    $implode['id']  = $itemid;

    $trackbackid    = implode(',', $implode);

    $data['open'] = '<!--';
    $data['close']  = '-->';
    $data['rdflink'] = xarModUrl('trackback', 'trackback', 'receive', array('id' => $trackbackid), false, null, 'ws.php');
    $data['permalink'] = htmlspecialchars($data['rdflink']);
    return $data;
}
?>