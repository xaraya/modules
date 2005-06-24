<?php
xarModAPILoad('julian','defines');

function julian_blocklayout_getShortDayName()
{
    return "print('testing');";
}

function julian_blocklayout_getMonthNameLong($args)
{
    extract($args); unset($args);
    $code = "echo xarModAPIFunc('julian','user','getMonthNameLong',array('date'=>$date));";
    return $code;
}

function julian_blocklayout_getMonthNameMedium($args)
{
    extract($args); unset($args);
    $code = "echo xarModAPIFunc('julian','user','getMonthNameMedium',array('date'=>$date));";
    return $code;
}

function julian_blocklayout_getMonthNameShort($args)
{
    extract($args); unset($args);
    $code = "echo xarModAPIFunc('julian','user','getMonthNameShort',array('date'=>$date));";
    return $code;
}

function julian_blocklayout_substr($args)
{
    if(isset($args['length'])) {
        return "echo substr(\"$args[string]\",$args[start],$args[length]);";
    } else {
        return "echo substr(\"$args[string]\",$args[start]);";
    }
}



?>
