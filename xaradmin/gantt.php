<?php
/**
 * Tasks module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tasks Module Development Team
 */
/**
 * @author Chad Kraeft
 *
 * Show a gantt chart
 *
 */
function tasks_admin_gantt($args)
{
    if (!xarVarFetch('parentid', 'int:1', $parentid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('module', 'str:1:', $module, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type', 'str:1:', $type, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('func', 'str:1:', $func, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter', 'str:1:', $filter, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('displaydepth', 'int:1', $displaydepth, NULL, XARVAR_NOT_REQUIRED)) return;

    extract($args);

    $tasks = xarModAPIFunc('tasks',
                          'user',
                          'getall',
                          array('parentid' => $parentid,
                                  'modname' => $module,
                                  'objectid' => $objectid,
                                  'displaydepth' => 1));

    include ("html/modules/tasks/gantt/jpgraph.php");
    include ("html/modules/tasks/gantt/jpgraph_gantt.php");

    // Some global configs
    $heightfactor=0.5;
    $groupbarheight=0.1;
    $revision="2002-10-14";

    // Standard calls to create a new graph
    $graph = new GanttGraph(0,0,"auto");
    $graph->SetShadow();
    $graph->SetBox();

    // Titles for chart
    $graph->title->Set("Xaraya scenario roadmap");
    $graph->subtitle->Set("(Revision: $revision)");
    $graph->title->SetFont(FF_FONT1,FS_BOLD,12);

    // For illustration we enable all headers.
    $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);

    // For the week we choose to show the start date of the week
    // the default is to show week number (according to ISO 8601)
    $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

    // Change the scale font
    $graph->scale->week->SetFont(FF_FONT0);
    $graph->scale->year->SetFont(FF_FONT1,FS_BOLD,12);

    // xaroad contains a list of records
    // *id;label;start;duration;predecessor;progress;type;lead;part_of;
    // start: date of start, if empty: today or if predecessor, from end of that one
    // type: 0: grouping; 1: normal task; 2: milestone
    // duration in days

    // Algorithm for sophistication
    // - DONE: scan database for group records and keep log of the latest date, so end-date can be set properly
    // - DONE: keep track of predecessors and adjust the start-date of successors
    // - draw arrows from end of predecessor to begin of successors

    // Generate the gantt bars
    $plots=array();
    $scenario=array();
    $latestdate = array();

    if(is_array($tasks) && count($tasks) > 0) {
        foreach($tasks as $task) {
            switch ($task[type]) {
                case 0: // Grouping record
                    // params: line, label, start, end, caption, heightfactor
                    $bar = new GanttBar($db->recordNr,$task[label],$task[start],"",$task[lead],$groupbarheight);
                    $bar->title->SetFont(FF_FONT1,FS_BOLD,8);
                    $bar->SetColor("black");
                    $bar->SetPattern(BAND_SOLID, "black");
                    $scenario[$task[id]]=$bar;
                    $plots[$task[id]]=$bar;
                    break;
                case 1: // Normal task, indent
                    // Calculate end date from start date and duration, if start-date is empty, use today
                    if ($task[start]=="") $task[start]=date("Y-m-d");
                    if ($task[duration]=="") $task[duration]=0;
                    $enddate= date("Y-m-d",(strtotime($task[start])+($task[duration]*24*60*60)));
                    if ($enddate > $latestdate[$task[part_of]] ) $latestdate[$task[part_of]]=$enddate;
                    $bar = new GanttBar($db->recordNr," ".$task[label],$task[start],$enddate,"[".$task[progress]."%] ".$task[lead],$heightfactor);
                    $bar->progress->Set($task[progress]/100);
                    $plots[$record[id]]=$bar;
                    break;
                case 2: // Milestone
                    // pos, label, date, caption
                    $ms = new MileStone($db->recordNr,$task[label],$task[start],$task[lead]);
                    if ($task[start] > $latestdate[$task[part_of]]) $latestdate[$task[part_of]]=$task[start];
                    $ms->title->Setfont(FF_FONT1,FS_BOLD,8);
                    $plots[$task[id]]=$ms;
                    break;
            }
        }

    // Now we have all plots in an array in memory and we can do some processing based on
    // dependencies
    // $plots contains all plot objects
    // 1. Adjust begin dates for objects when they have a predecessor
    // 2. Add lines from predecessor to successor and add them to the plot array
    // 3. Adjust end date of grouping records so line will extend to whole project
        foreach($tasks as $task) {
            if ($task[predecessor]) {
                // Predecessor found, get enddate for that record and set
                // begindate of current record at least to that date
                $searchrec=array('id' => $task[predecessor]);
                $pred = $db->search($searchrec);
                $earliest = $plots[$pred[id]]->GetMaxDate();
                // Set the begindate of this record to that date
                $plots[$task[id]]->iStart=$earliest;
                $plots[$task[id]]->iEnd=($earliest + $task[duration]*24*60*60);
                // Adjust scenario dates if necessary
                if (date("Y-m-d",$plots[$task[id]]->iEnd) > $latestdate[$plots[$task[partof]]]) {
                    $plots[$task[part_of]]->iEnd = $plots[$task[id]]->iEnd;
                }
            }
        }
    }


    // Add things for which date doesn't change anymore to the graph here.
    // Add a baseline for today
    $vl = new GanttVLine(date("Y-m-d"),"today","darkred");
    $graph->Add($vl);

    // Process the plot array for drawing
    while (list($key, $object) = each($plots)) {
      $graph->Add($object);
    }

    $graph->Stroke();
}
?>