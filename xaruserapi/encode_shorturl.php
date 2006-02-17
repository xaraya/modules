<?php
/**
 * Encode the short URLs for Julian
 *
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
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
 * @author  Julian Development Team, <michelv@xarayahosting.nl>
 * @access  public
 * @param   func
 * @return  URL
 * @todo    MichelV. <#> Check this function and it functioning. Include Categories
 */

function julian_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args); unset($args);
    // check if we have something to work with
    if (!isset($func)) { return; }

    // default path is empty -> no short URL
    $path = '';
    $extra = '';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'julian';

    // specify some short URLs relevant to your module
    switch($func) {
        case 'main':
            // replace this with the default view when available
            // right now we'll just default to the month view
            $path = "/$module/month/";
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= xarVarPrepForDisplay($cal_user).'/';
            $path .= 'index.html';
            break;

        case 'day':
            $path = "/$module/day/";
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= xarVarPrepForDisplay($cal_user).'/';
            $path .= 'index.html';
            break;

        case 'week':
            $path = "/$module/week/";
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= xarVarPrepForDisplay($cal_user).'/';
            $path .= 'index.html';
            break;

        case 'month':
            $path = "/$module/month/";
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= xarVarPrepForDisplay($cal_user).'/';
            $path .= 'index.html';
            break;

        case 'year':
            $path = "/$module/year/";
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            if(isset($cal_user) && !empty($cal_user)) $path .= xarVarPrepForDisplay($cal_user).'/';
            $path .= 'index.html';
            break;

        case 'add':
            $path = "/$module/add/";
            if(isset($cal_date) && !empty($cal_date)) $path .= xarVarPrepForDisplay($cal_date).'/';
            $path .= 'index.html';
            break;

        case 'edit':
            $path = "/$module/edit/";
            if(isset($cal_eid) && !empty($cal_eid)) $path .= xarVarPrepForDisplay($cal_eid).'.html/';
            break;

        case 'view':
            $path = "/$module/view/";
            if(isset($cal_vid) && !empty($cal_vid)) $path .= xarVarPrepForDisplay($cal_vid).'/';
            break;
    }

    if(!empty($path) && isset($cal_sdow)) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'cal_sdow=' . $cal_sdow;
    }

    if(!empty($path) && isset($cal_category)) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'cal_category=' . $cal_category;
    }

    if(!empty($path) && isset($cal_topic)) {
        $join = empty($extra) ? '?' : '&amp;';
        $extra .= $join . 'cal_topic=' . $cal_topic;
    }

    return $path.$extra;

}

?>
