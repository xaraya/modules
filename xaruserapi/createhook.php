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
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['itemtype'] optional item type for the item (not used in hook calls)
 * @param $args['item'] optional item info (not used in hook calls)
 * @return array extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function twitter_userapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'user', 'createhook', 'twitter');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, modname wil be empty, but we get it from the
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
                    'module name', 'user', 'createhook', 'twitter');
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

    // try settings for module / itemtype we're hooked to
    if (!empty($modname) && !empty($itemtype)) {
        $string = xarModGetVar('twitter', $modname . '.' . $itemtype);
    }
    // fall back to default settings for module (itemtype 0) we're hooked to
    if (!empty($modname) && empty($string)) {
        $string = xarModGetVar('twitter', $modname);
    }
    // fall back to twitter module defaults
    if (empty($string)) {
        $string = xarModGetVar('twitter', 'twitter');
    }
    // this should never be empty
    if (!empty($string)) {
        $settings = unserialize($string);
    }

    // make sure we have an account to twitter from...
    if ($settings['senduser']) {
        // TODO: support twitter this here
        if ($settings['senduser'] == 2) {

        }
        $tfieldname = xarModGetVar('twitter', 'tfieldname');
        // no dd prop, bail
        if (empty($tfieldname)) return $extrainfo;
        // get the current users twitterscreenname value
        $userdd = xarUserGetVar($tfieldname);
        // empty, bail
        if (empty($userdd)) return $extrainfo;
        list($screen_name, $screen_pass) = explode(';', $userdd);
        // no username or password, bail
        if (empty($screen_name) || empty($screen_pass)) return $extrainfo;
    } elseif ($settings['sendsite']) {
        // check tweeting for site account owner only
        if ($settings['sendsite'] == 2 && xarUserGetVar('uid') != xarModGetVar('twitter', 'site_screen_role')) return $extrainfo;
        // check for site account
        $screen_name = xarModGetVar('twitter', 'site_screen_name');
        // no screen name, bail
        if (empty($screen_name)) return $extrainfo;
        $screen_pass = xarModGetVar('twitter', 'site_screen_pass');
        // no screen pass, bail
        if (empty($screen_pass)) return $extrainfo;
    } else {
        // no account specified, bail
        return $extrainfo;
    }

    // maximum update length
    $maxlen = 160;

    // try and get a link for this item using the modules own getitemlinks api function
    $itemlink = xarModAPIFunc($modname, 'user', 'getitemlinks',
        array(
            'itemtype' => $itemtype,
            'itemids' => array($objectid)
        ),
        0 // don't throw an error if this function doesn't exist
    );

    // if we didn't get a link from api, we build a generic one
    if (!isset($itemlink[$objectid]['url'])) {
        $urlargs = array();
        // skip itemtype if the url param is empty (not all modules need an itemtype param)
        if (!empty($settings['urlitemtype']) && !empty($itemtype)) {
            $urlargs[$settings['urlitemtype']] = $itemtype;
        }
        $urlargs[$settings['urlitemid']] = $objectid;
        // link to the new item
        $linkurl = xarModURL($modname, $settings['urltype'], $settings['urlfunc'], $urlargs);
    } else {
        $linkurl = $itemlink[$objectid]['url'];
    }
    $linkurl = str_replace('&amp;', '&', $linkurl);
    $tinyurl = xarModAPIFunc('twitter', 'util', 'tinyurl', array('url' => $linkurl));
    if (!empty($tinyurl)) $linkurl = $tinyurl;
    // length of link
    $linklen = strlen($linkurl);
    // remaining characters
    $remaining = ($maxlen - $linklen);
    // if we got enough space we get the excerpt to prepend (if any)
    if ($remaining > 10 && !empty($settings['fieldname']) && isset($extrainfo[$settings['fieldname']]) && !empty($extrainfo[$settings['fieldname']])) {
        // clean out the html
        $excerpt = strip_tags($extrainfo[$settings['fieldname']]);
        $excerpt = substr($excerpt, 0, $remaining - 4) .'... ';
    }

    $status = !empty($excerpt) ? $excerpt.$linkurl : $linkurl;

    // sanity check in case the link is longer than the max length
    if (strlen($status) <= $maxlen) {
        // finally send the status update, adding the response to the extrainfo
        $extrainfo['twitter_update'] = xarModAPIFunc('twitter','user', 'rest_methods',
            array(
                'area' => 'statuses',
                'method' => 'update',
                'status' => $status,
                'username' => $screen_name,
                'password' => $screen_pass,
                'cached' => false,
                'superrors' => true
            ));
    }
    return $extrainfo;
}
?>