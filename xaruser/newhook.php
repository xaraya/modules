<?php
/**
 * New event hook
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
 * Provide GUI for new hook
 *
 * enter date/time for a new item - hook for ('item','new','GUI')
 *
 * @author  JornB MichelV. <michelv@xaraya.com>
 * @access  public
 * @param   $extrainfo
 * @return  array tplinfo
 * @todo    none
 */
function julian_user_newhook($args)
{
    extract($args);
    if (!xarVarFetch('event_summary', 'str:1:100', $event_summary, xarML('Not Entered'), XARVAR_NOT_REQUIRED)) return;

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    $data = array();
    $data['event_summary'] = $event_summary;
    $event_startdate = time();
    $event_enddate = time();

    $data['event_month'] = 1;
    $data['event_year'] = 1;
    $data['event_day'] = 1;

    $data['event_allday'] = true;

    list($data['event_year'],   $data['event_month'],   $data['event_day'])    = explode("-",date("Y-m-d",$event_startdate));
    list($data['event_endyear'],$data['event_endmonth'],$data['event_endday']) = explode("-",date("Y-m-d",$event_enddate));

    // Building start hour options (default = 12)
    $start_hour_options = '';
    for($i = 1;$i <= 12; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_hour_options.='<option value="'.$i.'"';
        if ($i == 12) $start_hour_options.= " SELECTED";
        $start_hour_options.='>'.$j.'</option>';
    }
    $data['start_hour_options'] = $start_hour_options;

    // Building duration minute options
    // Get the interval
    $StartMinInterval = xarModGetVar('julian', 'StartMinInterval');
    if ($StartMinInterval == 1) {
        $sminend = 60;
    } elseif ($StartMinInterval == 5) {
        $sminend = 56;
    } elseif ($StartMinInterval == 10) {
        $sminend = 51;
    } elseif ($StartMinInterval == 15) {
        $sminend = 46;
    }

    $start_minute_options = '';
    for($i = 0;$i < $sminend; $i = $i + $StartMinInterval) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_minute_options.='<option value="'.$j.'"';
        $start_minute_options.='>'.$j.'</option>';
    }
    $data['start_minute_options'] = $start_minute_options;

    // Building duration minute options
    // Get the interval
    $DurMinInterval = xarModGetVar('julian', 'DurMinInterval');
    if ($DurMinInterval == 1) {
        $minend = 60;
    } elseif ($DurMinInterval == 5) {
        $minend = 56;
    } elseif ($DurMinInterval == 10) {
        $minend = 51;
    } elseif ($DurMinInterval == 15) {
        $minend = 46;
    }

    $dur_minute_options = '';
    for($i = 0;$i < $minend; $i = $i + $DurMinInterval) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $dur_minute_options.='<option value="'.$j.'"';
        $dur_minute_options.='>'.$j.'</option>';
    }
    $data['dur_minute_options'] = $dur_minute_options;

    // Start AM/PM (default = AM)
    $data['event_startampm'] = false;    // true=PM, false=AM

    // Building duration hour options (default = 1)
    $dur_hour_options = '';
    for($i = 0;$i <= 24; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $dur_hour_options.='<option value="'.$i.'"';
        if ($i == 1) $dur_hour_options.= " selected";
        $dur_hour_options.='>'.$j.'</option>';
    }
    $data['dur_hour_options'] = $dur_hour_options;

    // Type of recurrence (0=none, 1=every, 2=on)
    $data['event_repeat'] = 0;

    // Repeat-every defaults.
    $data['event_repeat'] = 0;    // frequency unit (day=1, week=2, month=3, year=4)
    $data['event_repeat_freq'] = '';    // frequency (every x time units)

    // Repeat-on defaults
    $data['event_repeat_on_day'] = 0;    // day of the week
    $data['event_repeat_on_num'] = 0;    // instance within month (1st, 2nd, ..., last=5)
    $data['event_repeat_on_freq'] = '';    // frequency (every x months)

   //Setting freq type selection (days,weeks,months,years)
   for ($i = 1; $i < 5; $i++) {
     $data['freq_type_selected'][$i] = '';
   }
     $data['freq_type_selected'][1] = 'selected';

   //Setting repeat on num selection
   for ($i = 1; $i < 6; $i++) {
     $data['repeat_on_num_selected'][$i] = '';
   }
 //  $data['repeat_on_num_selected'][$i] = 'selected';
   //Setting allday checked
   $data['allday_checked'][0] = '';
   $data['allday_checked'][1] = 'checked';
   //Setting repeat on day selection
   for ($i = 1; $i < 8; $i++) {
     $data['repeat_on_day_selection'][$i] = '';
   }
   //Setting event repeat selection
   for ($i = 0; $i < 3; $i++) {
     $data['event_repeat_checked'][$i] = '';
   }
   $data['event_repeat_checked'][0] = "checked";

    // Determining which end date radio to check. 0 index indicates this event has an end date and 1 index means it does not
    // event_repeat tells the type of repeat
    $event_endtype_checked[0] = '';
    $event_endtype_checked[1] = 'checked';
    $data['event_endtype_checked'] = $event_endtype_checked;

    //determine if this is there is an enddate present
    $data['enddatedisabled'] = 'disabled';
    return xarTplModule('julian','user','edithook',$data);
}

?>
