<?php
/**
 *  Wrapper for the dayIs method of the calendar class
 *  @author Roger Raymond <roger@asphyxia.com>
 *  @version $Id: dayis.php,v 1.2 2003/06/24 21:23:06 roger Exp $
 *  @param int $day 0 - 6 [Sun - Sat]
 *  @param int $date valid date YYYYMMDD
 *  @return bool true/false depending on day looking for and the date
 */
function calendar_userapi_dayIs($args)
{
    extract($args); unset($args);
    // make sure we have a valid day value
    if(!xarVarValidate('int:0:7',$day)) {
        return;
    }
    // TODO: Revisit this later and make a new validator for it
    // make sure we have a valid date
    if(!xarVarValidate('int::',$date)) {
        return;
    }
    $c = xarModAPIFunc('calendar','user','factory','calendar');
    return $c->dayIs($day,$date);
}

?>
