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
            $path .= 'index.html';
            break;

        case 'day':
            $path .= 'day/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            $path .= 'index.html';
            break;

        case 'week':
            $path .= 'week/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            $path .= 'index.html';
            break;

        case 'month':
            $path .= 'month/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            $path .= 'index.html';
            break;

        case 'year':
            $path .= 'year/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= $cal_user.'/';
            $path .= 'index.html';
            break;

        case 'addevent':
            $path .= 'addevent/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            $path .= 'index.html';
            break;
        case 'viewevents':
            $path .= 'viewevents/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            $path .= 'index.html';
            break;
        case 'alerts':
            $path .= 'alerts/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            $path .= 'index.html';
            break;
        case 'edit':
            $path .= 'edit/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id.'.html';
            break;

        case 'viewevent':
            $path .= 'display/';
      //      if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id.'.html';
            break;
        case 'export':
            $path .= 'export/';
      //      if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date.'/';
            if(isset($event_id) && !empty($event_id)) $path .= $event_id.'.html';
            break;
        case 'jump':
            $path .= 'jump/';
            if(isset($cal_date) && !empty($cal_date)) $path .= $cal_date;
      //      if(isset($event_id) && !empty($event_id)) $path .= $event_id.'.html';
            break;
    }

    /* add some other module arguments as standard URL parameters */
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            }
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        }
    }

    return $path.$extra;

}

?>
