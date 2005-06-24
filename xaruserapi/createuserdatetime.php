<?php
/**
 *  calendar_userapi_createUserDateTime
 *  return the date/time for a user based on timezone/locale
 *  @version $Id: createuserdatetime.php,v 1.2 2005/01/26 08:45:26 michelv01 Exp $
 *  @author Roger Raymond
 *  @param string $format valid date/time format using php's date() function
 *  @return string valid date/time
 *  @todo user timezone modifications
 */
function &julian_userapi_createUserDateTime($format='Ymd')
{
    return gmdate($format);
    
    /*
    if(xarUserLoggedIn()) {
        // $tzoffest = user's timezone offset
    } else {
        // $tzoffset = site's timezone offset
    }
    */
    
}
?>
