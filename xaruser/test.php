<?php
// $Id: month.php,v 1.3 2003/06/24 21:22:21 roger Exp $

function calendar_user_test()
{
// some timing for now to see how fast|slow the parser is
include_once('Benchmark/Timer.php');
$t =& new Benchmark_Timer;
$t->start();
    $ical = xarModAPIFunc('icalendar','user','factory','ical_parser');
$t->setMarker('Class Instantiated');    
    xarVarFetch('file','str::',$file);
$t->setMarker('File Var Fetched');    
    //$ical->setFile('modules/timezone/zoneinfo/America/Phoenix.ics');
    $ical->setFile($file);
$t->setMarker('File Set');
    $ical->parse();
$t->setMarker('Parsing Complete');

$t->stop(); 
    
    ob_start();
        print_r($ical);
        $ical_out = ob_get_contents();
    ob_end_clean();
    
    $bl_data = array(
        'ical'=>$ical_out,
        'profile'=>$t->getOutput()
    );
    
    return $bl_data;
}

?>
