<?php

/**
 *  timezone_userapi_parseOffset
 *  TimeZone userAPI to parse a valid timezone offset into seperate components
 *  @param $args['offset'] string valid timezone offset from UTC (-0500, etc)
 *  @return array(direction,hours,minutes,seconds,total)
 */
function timezone_userapi_parseOffset($args=array())
{
    extract($args); unset($args);
    preg_match('/([-+])?    # -+ directional signs (optional)
                ([\d]{1,2}) # hours 0-9
                :?          # optional split
                ([\d]{2})   # minutes 0-9
                :?          # optional split
                ([\d]{2})?  # seconds 0-9 (optional)
                /x', 
                $offset, 
                $matches);
    
    // if we don't have any matches, then there is no offset
    if(empty($matches)) {
        $retarr = array('direction'=>null,'hours'=>'00','minutes'=>'00','seconds'=>'00');
    } else {
        // check for the existence of the seconds offset (usually not there)
        if(!isset($matches[4])) $matches[4] = '00';
        $retarr = array('direction'=>$matches[1],
                        'hours'=>(int)$matches[2],
                        'minutes'=>(int)($matches[3]/60),
                        'seconds'=>(int)$matches[4]);
        // small memory cleanup
        unset($matches);
    }
    
    switch($retarr['direction']) {
        
        case '-':
            $retarr['total'] = 0 - (int)((($retarr['hours']+$retarr['minutes'])*3600)+$retarr['seconds']);
            break;
        
        default:
            $retarr['total'] = (int)((($retarr['hours']+$retarr['minutes'])*3600)+$retarr['seconds']);
            break;
    }
    
    return $retarr; 
}
?>