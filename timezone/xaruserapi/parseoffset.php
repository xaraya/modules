<?php

/**
 *  timezone_userapi_parseOffset
 *  TimeZone userAPI to parse a valid timezone offset into seperate components
 *  @param $offset string valid timezone offset from UTC (-0500, etc)
 *  @return array(direction,hours,minutes,seconds,total)
 */
function timezone_userapi_parseOffset($offset)
{
    preg_match('/([-+])?    # -+ directional signs (optional)
                ([\d]{1,2}) # hours 0-9
                :?          # optional split
                ([\d]{2})   # minutes 0-9
                :?          # optional split
                ([\d]{2})?  # seconds 0-9 (optional)
                /x', 
                $offset, 
                $matches);
                
    // check for the existence of the seconds offset (usually not there)
    if(!isset($matches[4])) $matches[4] = '00';
    $return = array('direction'=>$matches[1],
                    'hours'=>(int)$matches[2],
                    'minutes'=>(int)($matches[3]/60),
                    'seconds'=>(int)$matches[4]);

    // small memory cleanup
    unset($matches);
    
    switch($return['direction']) {
        case '-':
            $return['total'] = 0 - (int)((($return['hours']+$return['minutes'])*3600)+$return['seconds']);
            break;
        default:
            $return['total'] = (int)((($return['hours']+$return['minutes'])*3600)+$return['seconds']);
            break;
    }
    
    return $return; 
}

?>
