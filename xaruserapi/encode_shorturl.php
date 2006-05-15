<?php
/**
 * Encode the short URLs for Julian
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */
/**
 * Encode the short URLs in Julian
 *
 * The parameters are taken from the URL and coupled to functions
 *
 * @author  MichelV <michelv@xaraya.com>
 * @access  public
 * @param   array $args including func
 * @return  string URL
 * @todo    MichelV. <#> Check this function and it functioning. Include Categories
 */
function julian_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args); unset($args);
    // check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // default path is empty -> no short URL
    $path = '';
    $extra = '';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'julian';
    $alias = xarModGetAlias($module);
    /* Check if we have module alias set or not */
    $aliasisset = xarModGetVar('julian', 'useModuleAlias');
    $aliasname = xarModGetVar('julian','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else {
        $usealias = false;
    }
    if (($module == $alias) && ($usealias)){
        $path = '/' . $aliasname . '/';
    } else {
        $path = '/' . $module . '/';
    }
    // specify some short URLs relevant to your module
    switch($func) {
        case 'main':
            // replace this with the default view when available
            // right now we'll just default to the month view
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= xarVarPrepForDisplay($cal_user).'/';
            break;

        case 'day':
            $path .= 'day/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            break;

        case 'week':
            $path .= 'week/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            break;

        case 'month':
            $path .= 'month/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            break;

        case 'year':
            $path .= 'year/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            break;

        case 'addevent':
            $path .= 'addevent/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date;
            break;
        case 'viewevents':
            $path .= 'viewevents/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date;
            break;
        case 'alerts':
            $path .= 'alerts/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date;
            break;
        case 'edit':
            $path .= 'edit/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id;
            break;
        case 'updateevent':
            $path .= 'updateevent/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id;
            break;
        case 'viewevent':
            $path .= 'display/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id;
            break;
        case 'export':
            $path .= 'export/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id;
            break;
        case 'jump':
            $path .= 'jump/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date;
            break;
    }

    /* add some other module arguments as standard URL parameters */
    if (isset($startnum) || isset($catid) || isset($cids) || isset($numitems) || isset($sortby) || isset($orderby)) {
        $join = '&';
        $path = $path.'?';
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
        }
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            }
            $path .= $join . 'catid=' . $catid;
        }

        if (isset($numitems)) {
            $path .= $join . 'numitems=' . $numitems;
        }
        if (isset($sortby)) {
            $path .= $join . 'sortby=' . $sortby;
        }
        if (isset($orderby)) {
            $path .= $join . 'orderby=' . $orderby;
        }
    }

    return $path.$extra;

}

?>
