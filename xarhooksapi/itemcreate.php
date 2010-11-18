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
 * send status update on new item - hook for ('item','create','GUI')
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
            $modname = xarModGetName();
        }
    }
    $modid = xarModGetIDFromName($modname);
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
    
    $settings = xarModAPIFunc('twitter', 'hooks', 'getsettings',
        array(
            'module' => $modname,
            'itemtype' => $itemtype,
        ));

    // try and get a link for this item using the modules own getitemlinks api function    
    $itemlink = xarModAPIFunc($modname, 'user', 'getitemlinks',
        array(
            'itemtype' => $itemtype,
            'itemids' => array($objectid)
        ),false); // don't throw an exception if this function doesn't exist
    if (!empty($itemlink[$objectid]['url']))
        $linkurl = $itemlink[$objectid]['url'];
    
    // If no link, build one based on settings
    if (empty($linkurl)) {
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
    
    // build the status text
    $text = "";
    // Add pre text if specified
    if (!empty($settings['pre']))
        $text .= $settings['pre'];
    // Add excerpt from field if specified and exists
    if (!empty($settings['field']) && isset($extrainfo[$settings['field']]))
        $text .= " " . $extrainfo[$settings['field']];
    // Add link to item
    $text .= " " . $linkurl;
    
    // Prep the status text
    require_once("modules/twitter/class/twitterapi");    
    $text = TwitterUtil::prepstatus($text);
    
    if (!empty($text) && strlen($text) <= 140) {
        $extrainfo['twitter_update'] = xarModAPIFunc('twitter', 'rest', 'status',
            array(
                'method' => 'update',
                'status' => $text,
                'access_token' => xarModGetVar('twitter', 'access_token'),
                'access_token_secret' => xarModGetVar('twitter', 'access_token_secret'),
            ));                
    }
    
    return $extrainfo;
}
?>