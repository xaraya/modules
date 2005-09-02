<?php
/**
 * File: $Id$
 *
 * Create a valid link to a particluar week
 *
 * @package julian
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link  link to information for the subpackage
 * @author Julian development Team 
 */

/**
 *  
 *  Create a valid link to a particluar week
 *  
 *  @author Roger Raymond
 *  @access public
 *  @param string $date YYYYMMDD date to provide link to
 *  @return string a valid link based on xarModURL()
 *  @todo add necessary get vars to the resulting URL
 */
function julian_userapi_getWeekLink($date=null)
{
    if(!isset($date)) $date = date('Ymd');
    $year = substr($date,0,4);
    $month = substr($date,4,2);
    $day = substr($date,6,2);
    
    $link = xarModURL('julian','user','week',array('cal_date'=>$date));
    return $link;        
}
?>
