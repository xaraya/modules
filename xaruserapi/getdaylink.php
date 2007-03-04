<?php
/**
 *  calendar_userapi_getDayLink
 *  Create a valid link to a particluar day
 *  @version $Id: getdaylink.php,v 1.1 2003/06/24 20:01:14 roger Exp $
 *  @author Roger Raymond
 *  @access public
 *  @param string $date YYYYMMDD date to provide link to
 *  @return string a valid link based on xarModURL()
 *  @todo add necessary get vars to the resulting URL
 */
function calendar_userapi_getDayLink($date=null)
{
    if(!isset($date)) $date = date('Ymd');
    $year = substr($date,0,4);
    $month = substr($date,4,2);
    $day = substr($date,6,2);
    
    $link = xarModURL('calendar','user','day',array('cal_date'=>$date));
    return $link;        
}
?>
