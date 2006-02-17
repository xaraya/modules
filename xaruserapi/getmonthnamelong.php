<?php
/**
 * Get the long names of the month in an array
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
 * Get the long names of the month
 *
 * @author
 * @param  string month
 * @return string longname
 * @todo MichelV deprec?
 */
function julian_userapi_getMonthNameLong($args)
{
    extract($args); unset($args);
    if(!isset($month)) $month = date('m');

    // make sure we have a valid month value
    if(!xarVarValidate('int:1:12',$month)) {
        return;
    }
    $c = xarModAPIFunc('julian','user','factory','calendar');
    return $c->MonthLong($month);
}

?>