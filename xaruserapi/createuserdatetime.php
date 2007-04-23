<?php
/**
 * Julian module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 *  calendar_userapi_createUserDateTime
 *  return the date/time for a user based on timezone/locale
 *
 *  @author Roger Raymond
 *  @param string $format valid date/time format using php's date() function
 *  @return string valid date/time
 *  @todo use Xaraya function
 */
function &julian_userapi_createUserDateTime($format='Ymd')
{
    // Checkme: should we be using gmdate?
    // We should probably be using the offset of the server, at the very minimum.
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
