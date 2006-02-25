<?php
/**
 * Julian module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 *  Wrapper for the dayIs method of the calendar class
 *  @author Roger Raymond <roger@asphyxia.com>
 *  @version $Id: dayis.php,v 1.2 2005/01/26 08:45:26 michelv01 Exp $
 *  @param int $day 0 - 6 [Sun - Sat]
 *  @param int $date valid date YYYYMMDD
 *  @return bool true/false depending on day looking for and the date
 *  @Deprecated Nov 2005
 */
function julian_userapi_dayIs($args)
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
    $c = xarModAPIFunc('julian','user','factory','calendar');
    return $c->dayIs($day,$date);
}

?>