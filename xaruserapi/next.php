<?php
/**
 * Calculates the new date for the next view.
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */
/**
 * Calculates the new date for the next view.
 *
 * The view or "cal_type" is incremented by an interval of "cal_interval" starting from "cal_date".
 *
 * @param INT $args['cal_sdow']
 * @param str $args['func'] the function of this moment
 * @return array URL
 */
function julian_userapi_next($args=array())
{
    xarVarFetch('cal_sdow', 'int:0:7', $cal_sdow, 0);
    // what function are we in
    xarVarFetch('func','str::',$func);

    extract($args); unset($args);

    if(!isset($cal_interval)) $cal_interval = 1;

    xarVarValidate('int::', $cal_date);
    xarVarValidate('int:1:', $cal_interval);
    xarVarValidate('str::', $cal_type);

    $y = substr($cal_date,0,4);
    $m = substr($cal_date,4,2);
    $d = substr($cal_date,6,2);

    switch(strtolower($cal_type)) {

        case 'day' :
            $d += $cal_interval;
            break;

        case 'week' :
            $d += (7 * $cal_interval);
            break;

        case 'month' :
            // Bug 5733 Subtract superfluous day
            if($d == 31) {
                switch($m) {
                    case 8:
                    case 10:
                    case 3:
                    case 5:
                        $d -= $cal_interval;
                        break;
                    case 1:
                        $d -= 3;
                        break;
                }
            }
            $m += $cal_interval;
            break;

        case 'year' :
            $y += $cal_interval;
            break;
    }

    $new_date = gmdate('Ymd',gmmktime(0,0,0,$m,$d,$y));
    return xarModURL('julian','user',strtolower($func),array('cal_date'=>$new_date,'cal_sdow'=>$cal_sdow));
}

?>
