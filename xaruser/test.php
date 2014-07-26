<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function calendar_user_test()
{
// some timing for now to see how fast|slow the parser is
include_once('Benchmark/Timer.php');
$t =& new Benchmark_Timer;
$t->start();
    $ical = xarMod::apiFunc('icalendar','user','factory','ical_parser');
$t->setMarker('Class Instantiated');
    xarVarFetch('file','str::',$file);
$t->setMarker('File Var Fetched');
    //$ical->setFile('code/modules/timezone/zoneinfo/America/Phoenix.ics');
    $ical->setFile($file);
$t->setMarker('File Set');
    $ical->parse();
$t->setMarker('Parsing Complete');

$t->stop();

    ob_start();
        print_r($ical);
        $ical_out = ob_get_contents();
    ob_end_clean();

    $data = array(
        'ical'=>$ical_out,
        'profile'=>$t->getOutput()
    );

    return $data;
}

?>
