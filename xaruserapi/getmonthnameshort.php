<?php
// $Id: getmonthnameshort.php,v 1.2 2005/01/26 08:45:26 michelv01 Exp $

function julian_userapi_getMonthNameShort($args)
{
    extract($args); unset($args);
    if(!isset($month)) $month = date('m');
    
    // make sure we have a valid month value
    if(!xarVarValidate('int:1:12',$month)) {
        return;
    }
    $c =& xarModAPIFunc('julian','user','factory','julian');
    return $c->MonthShort($month);
}

?>
