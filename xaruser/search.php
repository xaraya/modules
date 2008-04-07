<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Roger Raymond
 */
function weather_user_search()
{
    // grab the location we're searching for
    if (!xarVarFetch('city_default_location', 'str', $default_location,  null, XARVAR_NOT_REQUIRED)) return;
    if (isset($default_location)){
        $loc = $default_location;   
    } else {
        $loc = null;
    }

    $w = xarModAPIFunc('weather','user','factory');

    // perform the search
    $matches=null;

    if(isset($loc) && !empty($loc)) {;
        $matches = $w->locData($loc);
        $error = false;
    }
    if(!is_array($matches)) {
        // we don't have real results but an error message
        $matches = array();
        if(isset($loc)) { 
            // if we performed a search and we're here then we found nothing
            $error = true; 
        } else {
            // otherwise we probably just got here from a link and have yet to search
            $error = false;
        }
    } 
    
// Ignore the match search because we used dropdowns, so the choice is valid

//    if(count($matches) == 1) {
//        xarResponseRedirect(xarModURL('weather','user','main',array('xwloc'=>$matches[0]['zip'])));
    if(isset($loc) && !empty($loc)) {;
        xarResponseRedirect(xarModURL('weather','user','main',array('xwloc'=>$loc)));
    } else {
        $data['default_location'] = null;
        $data['loc'] = $loc;
        $data['error'] = $error;
        $data['matches'] = $matches;
        return $data;
    }
}
?>