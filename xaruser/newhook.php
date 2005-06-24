<?php

/**
 * enter date/time for a new item - hook for ('item','new','GUI')
 */
function julian_user_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
     
    $bl_data = array();
    
    $event_startdate = time();
    $event_enddate = time();

   $bl_data['event_month'] = 1;
   $bl_data['event_year'] = 1;
   $bl_data['event_day'] = 1;

   $bl_data['event_allday'] = true;

   list($bl_data['event_year'],   $bl_data['event_month'],   $bl_data['event_day'])    = explode("-",date("Y-m-d",$event_startdate));
   list($bl_data['event_endyear'],$bl_data['event_endmonth'],$bl_data['event_endday']) = explode("-",date("Y-m-d",$event_enddate));

   // Building start hour options (default = 12)
   $start_hour_options = '';
   for($i = 1;$i <= 12; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_hour_options.='<option value="'.$i.'"';
     if ($i == 12) $start_hour_options.= " SELECTED";
      $start_hour_options.='>'.$j.'</option>';
   }
   $bl_data['start_hour_options'] = $start_hour_options;
   
   // Building start minute options (default = 00)
   $start_minute_options = '';
   for($i = 0;$i < 46; $i = $i + 15)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $start_minute_options.='<option value="'.$i.'"';
     if ($i == 0) $start_minute_options.= " selected";
     $start_minute_options.='>'.$j.'</option>';
   }
   $bl_data['start_minute_options'] = $start_minute_options;

    // Start AM/PM (default = AM)
    $bl_data['event_startampm'] = false;    // true=PM, false=AM
   
   // Building duration hour options (default = 1)
   $dur_hour_options = '';
   for($i = 0;$i <= 24; $i++)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_hour_options.='<option value="'.$i.'"';
     if ($i == 1) $dur_hour_options.= " selected";
     $dur_hour_options.='>'.$j.'</option>';
   }
   $bl_data['dur_hour_options'] = $dur_hour_options;
   
   // Building duration minute options (default = 0)
   $dur_minute_options = '';
   for($i = 0;$i < 46; $i = $i + 15)
   {
     $j = str_pad($i,2,"0",STR_PAD_LEFT);
     $dur_minute_options.='<option value="'.$j.'"';
     if ($i == 0) $dur_minute_options.= " selected";
     $dur_minute_options.='>'.$j.'</option>';
   }
   $bl_data['dur_minute_options'] = $dur_minute_options;

   // Type of recurrence (0=none, 1=every, 2=on)
    $bl_data['event_repeat'] = 0;
    
    // Repeat-every defaults.
    $bl_data['event_repeat_freq_type'] = 0;    // frequency unit (day=1, week=2, month=3, year=4)
    $bl_data['event_repeat_every_freq'] = '';    // frequency (every x time units)

    // Repeat-on defaults
    $bl_data['event_repeat_on_day'] = 0;    // day of the week
    $bl_data['event_repeat_on_num'] = 0;    // instance within month (1st, 2nd, ..., last=5)
    $bl_data['event_repeat_on_freq'] = '';    // frequency (every x months)

    return xarTplModule('julian','user','edithook',$bl_data);
}

?>
