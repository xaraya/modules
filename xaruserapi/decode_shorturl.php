<?php
/**
 * Decode the short URLs in Julian
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Decode the short URLs in Julian
 *
 * The parameters are taken from the URL and coupled to functions
 *
 * @author  Julian Development Team, MichelV <michelv@xarayahosting.nl>
 * @access  public
 * @param   array $params the URL
 * @return  array
 * @todo    MichelV. <#> Check this function and it functioning. Include Categories
 */
function julian_userapi_decode_shorturl($params)
{
    $args = array();
    $module = 'julian';
    /* Check and see if we have a module alias */
    $aliasisset = xarModGetVar('julian', 'useModuleAlias');
    $aliasname = xarModGetVar('julian','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }
    /* Analyse the different parts of the virtual path
     * $params[1] contains the first part after index.php/julian
     * In general, you should be strict in encoding URLs, but as liberal
     * as possible in trying to decode them...
     */
    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar('julian','aliasname');
    }
    if(empty($params[1])) {
        return array('main', $args);
    } elseif($params[1] == 'day') {
        // if we have a 2nd parameter see if it's a date or username
        if(!empty($params[2])) {
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                // this is a date of some sort (YYYYMMDD)
                $args['cal_date'] = $matches[1];
            } elseif(preg_match('/([0-9a-z])/i',$params[2],$matches)) {
                // this should be a username
                $args['cal_user'] = $matches[1];
            }
        }
        // if we have a 3rd parameter it should be a username
        if(!empty($params[3])) {
            $args['cal_user'] = $params[3];
        }
        return array('day', $args);
    } elseif($params[1] == 'week') {
        // if we have a 2nd parameter see if it's a date or username
        if(!empty($params[2])) {
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                // this is a date of some sort (YYYYMMDD)
                $args['cal_date'] = $matches[1];
            } elseif(preg_match('/([0-9a-z])/i',$params[2],$matches)) {
                // this should be a username
                $args['cal_user'] = $matches[1];
            }
        }
        // if we have a 3rd parameter it should be a username
        if(!empty($params[3])) {
            $args['cal_user'] = $params[3];
        }
        return array('week', $args);
    } elseif($params[1] == 'month') {
        // if we have a 2nd parameter see if it's a date or username
        if(!empty($params[2])) {
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                // this is a date of some sort (YYYYMMDD)
                $args['cal_date'] = $matches[1];
            } elseif(preg_match('/([0-9a-z])/i',$params[2],$matches)) {
                // this should be a username
                $args['cal_user'] = $matches[1];
            }
        }
        // if we have a 3rd parameter it should be a username
        if(!empty($params[3])) {
            $args['cal_user'] = $params[3];
        }
        return array('month', $args);
    } elseif($params[1] == 'year') {
        // if we have a 2nd parameter see if it's a date or username
        if(!empty($params[2])) {
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                // this is a date of some sort (YYYYMMDD)
                $args['cal_date'] = $matches[1];
            } elseif(preg_match('/([0-9a-z])/i',$params[2],$matches)) {
                // this should be a username
                $args['cal_user'] = $matches[1];
            }
        }
        // if we have a 3rd parameter it should be a username
        if(!empty($params[3])) {
            $args['cal_user'] = $params[3];
        }
        return array('year', $args);
    } elseif($params[1] == 'addevent') {
        // if we have a 2nd parameter it should be a date
        if(!empty($params[2])) {
            // just make sure it's a valid date
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                $args['cal_date'] = $matches[1];
            }
        }
        return array('addevent', $args);
    } elseif($params[1] == 'edit') {
        // if we have a 2nd parameter it should be an event id
        if(!empty($params[2])) {
            // just make sure it's a valid eid
            if(preg_match('/^(\d+)\.html$/',$params[2],$matches)) {

                $args['event_id'] = $matches[1];
            }
        }
        return array('edit', $args);
    } elseif($params[1] == 'viewevents') {
        // if we have a 2nd parameter it should be a date
        if(!empty($params[2])) {
            // just make sure it's a valid date
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                $args['cal_date'] = $matches[1];
            }
        }
        return array('viewevents', $args);
    } elseif($params[1] == 'alerts') {
        // if we have a 2nd parameter it should be a date
        if(!empty($params[2])) {
            // just make sure it's a valid date
            if(preg_match('/([0-9]{4,4}[0-9]{2,2}?[0-9]{2,2}?)/',$params[2],$matches)) {
                $args['cal_date'] = $matches[1];
            }
        }
        return array('alerts', $args);
    } elseif($params[1] == 'viewevent') {

        // if we have a 2nd parameter it should be an event id
        if(!empty($params[2])) {
            // just make sure it's a valid event_id
            if (preg_match('/^(\d+)\.html$/',$params[2],$matches)) {
           //     dump( $matches[1] );
                $args['event_id'] = $matches[1];
            }
        }
        return array('viewevent', $args);
    } elseif($params[1] == 'export') {

        // if we have a 2nd parameter it should be an event id
        if(!empty($params[2])) {
            // just make sure it's a valid event_id
            if (preg_match('/^(\d+)\.html$/',$params[2],$matches)) {
           //     dump( $matches[1] );
                $args['event_id'] = $matches[1];
            }
        }
        return array('export', $args);
    } else {
    //    die('bogus');
        return array('main', $args);
    }

    // default : return nothing -> no short URL
    // (e.g. for multiple category selections)

}

?>
