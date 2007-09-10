<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * display rating for a specific item, and request rating
 * @param $args['objectid'] ID of the item this rating is for
 * @param $args['extrainfo'] URL to return to if user chooses to rate
 * @param $args['style'] style to display this rating in (optional)
 * @param $args['itemtype'] item type
 * @return output with rating information
 */
function ratings_user_display($args)
{
    extract($args);

    $data = array();
    $data['objectid'] = $objectid;

    $itemtype = 0;
    if (isset($extrainfo) && is_array($extrainfo)) {
        if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
        if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
        if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
            $data['returnurl'] = $extrainfo['returnurl'];
        }
    } else {
        $data['returnurl'] = $extrainfo;
    }

    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $args['modname'] = $modname;
    $args['itemtype'] = $itemtype;

    if (!isset($style)) {
        if (!empty($itemtype)) {
            $style = xarModGetVar('ratings', "style.$modname.$itemtype");
        }
        if (!isset($style)) {
            $style = xarModGetVar('ratings', 'style.'.$modname);
        }
        if (!isset($style)) {
            $style = xarModGetVar('ratings', 'defaultstyle');
        }
    }
    $data['style'] = $style;
    $data['modname'] = $modname;
    $data['itemtype'] = $itemtype;

    // Run API function
    $data['rating'] = xarModAPIFunc('ratings',
                           'user',
                           'get',
                           $args);

    if (isset($data['rating'])) {
        // Set the cached variable if requested
        if (xarVarIsCached('Hooks.ratings','save') &&
            xarVarGetCached('Hooks.ratings','save') == true) {
            xarVarSetCached('Hooks.ratings','value',$data['rating']);
        }

        // Display current rating
        switch($data['style']) {
            case 'percentage':
                $data['rating'] = sprintf("%.1f",$data['rating']);
                break;
            case 'outoffive':
                $data['rating'] = (int)(($data['rating']+10)/20);
                break;
            case 'outoffivestars':
                $data['rating'] = (int)($data['rating']/2);
                $data['intrating'] = (int)($data['rating']/10);
                $data['fracrating'] = $data['rating'] - (10*$data['intrating']);
                break;
            case 'outoften':
                $data['rating'] = (int)(($data['rating']+5)/10);
                break;
            case 'outoftenstars':
                $data['intrating'] = (int)($data['rating']/10);
                $data['fracrating'] = $data['rating'] - (10*$data['intrating']);
                $data['rating'] = sprintf("%.1f",$data['rating']);
                break;
            case 'customised':
            default:
                $data['rating'] = sprintf("%.1f",$data['rating']);
                break;
        }
    } else {
        $data['rating'] = 0;
        $data['intrating'] = 0;
        $data['fracrating'] = 0;
    }

    // Multipe rate check
    if (!empty($itemtype)) {
        $seclevel = xarModGetVar('ratings', "seclevel.$modname.$itemtype");
        if (!isset($seclevel)) {
            $seclevel = xarModGetVar('ratings', 'seclevel.'.$modname);
        }
    } else {
        $seclevel = xarModGetVar('ratings', 'seclevel.'.$modname);
    }
    if (!isset($seclevel)) {
        $seclevel = xarModGetVar('ratings', 'seclevel');
    }
    if ($seclevel == 'high') {
        // Check to see if user has already voted
        if (xarUserIsLoggedIn()) {
            if (!xarModGetVar('ratings',$modname.':'.$itemtype.':'.$objectid)) {
                xarModSetVar('ratings',$modname.':'.$itemtype.':'.$objectid,1);
            }
            $rated = xarModGetUserVar('ratings',$modname.':'.$itemtype.':'.$objectid);
            if (!empty($rated) && $rated > 1) {
                $data['rated'] = true;
            }
        } else {
            // no rating for anonymous users here
            $data['rated'] = true;
            // bug 5482 Always set the authid, but only a true one if security is met
            $data['authid'] = 0;
        }
    } elseif ($seclevel == 'medium') {
        // Check to see if user has already voted
        if (xarUserIsLoggedIn()) {
            if (!xarModGetVar('ratings',$modname.':'.$itemtype.':'.$objectid)) {
                xarModSetVar('ratings',$modname.':'.$itemtype.':'.$objectid,1);
            }
            $rated = xarModGetUserVar('ratings',$modname.':'.$itemtype.':'.$objectid);
            if (!empty($rated) && $rated > time() - 24*60*60) {
                $data['rated'] = true;
            }
        } else {
            $rated = xarSessionGetVar('ratings:'.$modname.':'.$itemtype.':'.$objectid);
            if (!empty($rated) && $rated > time() - 24*60*60) {
                $data['rated'] = true;
            }
        }
    } // No check for low

    // module name is mandatory here, because this is displayed via hooks (= from within another module)
    // set an authid, but only if the current user can rate the item
    if (xarSecurityCheck('CommentRatings', 0, 'Item', "$modname:$itemtype:$objectid")) {
        $data['authid'] = xarSecGenAuthKey('ratings');
    }
    return $data;
}

?>
