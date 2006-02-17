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
 * Test speed of parser
 *
 * @todo See if we need this file
 * @return array bl_data
 */
function julian_user_test()
{
// some timing for now to see how fast|slow the parser is
include_once('Benchmark/Timer.php');
$t = new Benchmark_Timer;
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
