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
 * @author  JornB, MichelV. <michelv@xaraya.com>
 * @access  public
 * @param   $extrainfo
 * @return  array tplinfo
 * @todo    none
 */
function julian_user_newhook($args)
{
    extract($args);
    if (!xarVarFetch('event_summary', 'str:1:100', $event_summary, xarML('Not Entered'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_repeat', 'int:0:4', $event_repeat, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_repeat_on_day', 'int', $event_repeat_on_day, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_repeat_on_num', 'int', $event_repeat_on_num, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_repeat_on_freq', 'int', $event_repeat_on_freq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_repeat_freq', 'int', $event_repeat_freq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_month', 'int', $event_month, date('m'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_day', 'int', $event_day, date('d'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_year', 'int', $event_year, date('Y'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_endmonth', 'int', $event_endmonth, date('m'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_endday', 'int', $event_endday, date('d'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_endyear', 'int', $event_endyear, date('Y'), XARVAR_NOT_REQUIRED)) return;
    // all day event (otherwise timed)
   if (!xarVarFetch('event_allday','int:0:1',$event_allday,0, XARVAR_NOT_REQUIRED)) return;

    // start time
   if (!xarVarFetch('event_starttimeh','int::',$event_starttimeh,12, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_starttimem','int::',$event_starttimem,0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_startampm', 'int:1:2',$event_startampm,1, XARVAR_NOT_REQUIRED)) return;    // 1=AM, 2=PM

    // duration
   if (!xarVarFetch('event_dur_hours',  'int::',$event_dur_hours,  1, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('event_dur_minutes','int::',$event_dur_minutes,0, XARVAR_NOT_REQUIRED)) return;
    // Return array
    $data = array();
    $data['event_summary'] = $event_summary;
    $data['event_month'] = $event_month;
    $data['event_year'] = $event_year;
    $data['event_day'] = $event_day;
    $data['event_allday'] = true;
    $data['event_endyear'] = $event_endyear;
    $data['event_endmonth'] = $event_endmonth;
    $data['event_endday'] = $event_endday;

    // Repeat-every defaults.
    $data['event_repeat'] = $event_repeat;    // frequency unit (day=1, week=2, month=3, year=4)
    $data['event_repeat_freq'] = $event_repeat_freq;    // frequency (every x time units)

    // Repeat-on defaults
    $data['event_repeat_on_day'] = $event_repeat_on_day;    // day of the week
    $data['event_repeat_on_num'] = $event_repeat_on_num;    // instance within month (1st, 2nd, ..., last=5)
    $data['event_repeat_on_freq'] = $event_repeat_on_freq;    // frequency (every x months)

    // Start AM/PM (default = AM)
    $data['event_startampm'] = $event_startampm;    // true=PM, false=AM
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // Building start hour options (default = 12)
    $start_hour_options = '';
    for($i = 1;$i <= 12; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $start_hour_options.='<option value="'.$i.'"';
        if ($i == $event_starttimeh) $start_hour_options.= " SELECTED";
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
        if ($j == $event_starttimem) $start_minute_options.= " SELECTED";
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
        if ($j == $event_dur_minutes) $dur_minute_options.= " SELECTED";
        $dur_minute_options.='>'.$j.'</option>';
    }
    $data['dur_minute_options'] = $dur_minute_options;


    // Building duration hour options (default = 1)
    $dur_hour_options = '';
    for($i = 0;$i <= 24; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $dur_hour_options.='<option value="'.$i.'"';
        if ($i == $event_dur_hours) $dur_hour_options.= " selected";
        $dur_hour_options.='>'.$j.'</option>';
    }
    $data['dur_hour_options'] = $dur_hour_options;

   //Setting freq type selection (days,weeks,months,years)
   for ($i = 1; $i < 5; $i++) {
     $data['freq_type_selected'][$i] = '';
   }
     $data['freq_type_selected'][1] = 'selected';

   //Setting repeat on num selection
   for ($i = 1; $i < 6; $i++) {
     $data['repeat_on_num_selected'][$i] = '';
     if ($i == $event_repeat_on_num) {
         $data['repeat_on_num_selected'][$i] = 'selected';
     }
   }

   //Setting allday checked
   if ($event_allday ==  1) {
       // is allday
       $data['allday_checked'][1] = '';
       $data['allday_checked'][0] = 'checked';
       $data['timeddisabled'] = 'disabled';
   } else {
       $data['allday_checked'][0] = '';
       $data['allday_checked'][1] = 'checked';
       $data['timeddisabled'] = '';
   }

   //Setting repeat on day selection
   for ($i = 1; $i < 8; $i++) {
     $data['repeat_on_day_selection'][$i] = '';
     if ($i == $event_repeat_on_day) {
         $data['repeat_on_day_selection'][$i] = 'selected';
     }
   }
   //Setting event repeat selection
   for ($i = 0; $i < 3; $i++) {
     $data['event_repeat_checked'][$i] = '';
     if ($i == $event_repeat) {
         $data['event_repeat_checked'][$i] = 'checked';
     }
   }


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
