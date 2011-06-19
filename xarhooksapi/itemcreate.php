<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * send status update on new item - hook for ('item','itemcreate','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfo array
 * @throws BAD_PARAM exception
 */
function twitter_hooksapi_itemcreate($args)
{
    extract($args);

    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'hooksapi', 'itemcreate', 'twitter');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $extrainfo;
    }

    // When called via hooks, modname will be empty, but we get it from the
    // extrainfo or the current module
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarMod::getName();
        }
    }
    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'hooksapi', 'itemcreate', 'twitter');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $extrainfo;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    $settings = xarMod::apiFunc('twitter', 'hooks', 'getsettings',
        array(
            'module' => $modname,
            'itemtype' => $itemtype,
        ));

    // Check if we're tweeting new items...
    if (empty($settings['tweetcreated']))
        return $extrainfo;

    // Status check if module supplied states...
    if (!empty($settings['itemstates']) && !empty($settings['stateparam'])) {
        $stateparam = $settings['stateparam'];
        if (empty($settings['states']) ||
            !isset($extrainfo[$stateparam]) ||
            !in_array($extrainfo[$stateparam], $settings['states']))
            return $extrainfo;
    }

    $text = !empty($settings['textcreated']) ? $settings['textcreated'] : '';

    $field = $settings['field'];
    if (!empty($field) &&
        isset($extrainfo[$field]) &&
        is_string($extrainfo[$field]) &&
        !empty($extrainfo[$field]))
        $text .= ' ' . $extrainfo[$field];

    if (!empty($settings['includelink'])) {
        try {
            $itemlink = xarMod::apiFunc($modname, 'user', 'getitelinks',
                array(
                    'itemtype' => $itemtype,
                    'itemids' => array($objectid),
                ));
            if (!empty($itemlink[$objectid]['url']))
                $linkurl = $itemlink[$objectid]['url'];
        } catch (Exception $e) {
            $typeparam = !empty($settings['typeparam']) ? $settings['typeparam'] : 'user';
            $funcparam = !empty($settings['funcparam']) ? $settings['funcparam'] : 'display';
            $itypeparam = !empty($settings['itypeparam']) ? $settings['itypeparam'] : 'itemtype';
            $itemparam = !empty($settings['itemparam']) ? $settings['itemparam'] : 'itemid';
            $linkurl = xarModURL($modname, $typeparam, $funcparam,
                array(
                    $itypeparam => empty($itemtype) ? null : $itemtype,
                    $itemparam => $objectid,
                ));

        }
        if (!empty($linkurl))
            $text .= ' ' . $linkurl;
    }

    // Prep the status text
    sys::import("modules.twitter.class.twitterapi");
    $text = TwitterUtil::prepstatus($text);

    if (!empty($text) && strlen($text) <= 140) {
        $extrainfo['twitter_create'] = xarMod::apiFunc('twitter', 'rest', 'status',
            array(
                'method' => 'update',
                'status' => $text,
                'access_token' => xarModVars::get('twitter', 'access_token'),
                'access_token_secret' => xarModVars::get('twitter', 'access_token_secret'),
            ));
    }

    return $extrainfo;
}
?>