<?php
// $Id: month.php,v 1.3 2003/06/24 21:22:21 roger Exp $

function timezone_user_parse_tzdata()
{
    $dir = 'modules/timezone/tzdata';
    // these are the timezone files we want to gather data from
    // some of this are ommitted because they currently don't contain anything useful
    // we will not be using the leapseconds data, we'll leave that to the OS
    $files = array('africa',
                   'antarctica',
                   'asia',
                   'australasia',
                   'etcetera',
                   'europe',
                   'northamerica',
                   //'pacificnew',
                   //'solar87',
                   //'solar88',
                   //'solar89',
                   'southamerica',
                   //'systemv',
                   //'leapseconds',
                   'backward');
    
    $tzdata = array();
    foreach($files as $file) {
        $contents = file("$dir/$file");
        foreach($contents as $c) {
            // remove comments
            $c = trim(preg_replace('/#.*$/i','',$c));
            if(!empty($c)) {
                $tzdata[] = trim(preg_replace('/(\t|\s)+/i',"\t",$c));
            }
        }
    }
    ksort($tzdata);
    unset($contents);
    $results = parse_data($tzdata);
    unset($tzdata);
    return array('results'=>$results);
}

// ok, now we can parse this array
// Rule	NAME	FROM	TO	TYPE	IN	ON	AT	SAVE	LETTER
// Zone	NAME    GMTOFF	RULES	FORMAT	[UNTIL]
//              GMTOFF  RULES   FORMAT  UNTILYEAR   UNTILMONTH  UNTILDAY    UNTILTIME  
// Link NAME    OLD NAME
// Leap	YEAR	MONTH	DAY	HH:MM:SS	CORR	R/S
function parse_data(&$tzdata) 
{
    $last = $zone_name = null;
    $rules = array();
    $links = array();
    $leaps = array();
    $zones = array();
    
    $links_script = null;
    $rules_script = $rule_last = null;
    $zones_script = $zone_last = null;
    $leaps_script = $leap_last = null;
    
    // since the array is large, this method provides better performance over foreach
    foreach($tzdata as $k=>$v) {
            
        $data = explode("\t",$v);
    
        if($data[0] == 'Rule') $last = 'rule';
        elseif($data[0] == 'Link') $last = 'link';
        elseif($data[0] == 'Leap') $last = 'leap';
        elseif($data[0] == 'Zone') $last = 'zone';
        else $last = 'zone2';
        
        switch($last) {
            case 'rule':
                // get rid of the Rule keyword
                array_shift($data); 
                // get the name of this Rule
                $name = array_shift($data); 
                if($rules_script == null) {
                    $rules_script .= "\n".'//'.str_repeat('=',72)."\n";
                    $rules_script .= "//\tTimeZone Rules\n";
                    $rules_script .= '//'.str_repeat('=',72)."\n";
                    $rules_script .= "\$Rules = array();\n";
                }
                // if the current name is the same as the
                // last name supplied, increment the counter
                if($name == $rule_last) {
                    $c++;
                } else {
                    $c = 0;
                }
                $rule_last = $name;
                
                $rules_script .= "\$Rules['{$name}'][{$c}] = array(";
                $internal = '';
                foreach($data as $rule) {
                    if(!empty($internal)) $internal .= ',';
                    $internal .= "'{$rule}'"; 
                }
                $rules_script .= "{$internal});\n";
                break;

            case 'link':
                // get rid of the Link keyword
                array_shift($data); 
                // get the current name for this zone
                $name = array_shift($data); 
                if($links_script == null) {
                    $links_script .= '//'.str_repeat('=',72)."\n";
                    $links_script .= "//\tLinks for Backwards Compatibility\n";
                    $links_script .= '//'.str_repeat('=',72)."\n";
                    $links_script .= "\$Links = array();\n";
                }
                $old_name = array_shift($data); 
                $links[$old_name] = $name;
                $links_script .= "\$Links['{$old_name}'] = '{$name}';\n";
                break;

            case 'leap':
                // get rid of the Leap keyword
                array_shift($data); 
                // get the year for this leap second
                $year = array_shift($data); 
                if($leaps_script == null) {
                    $leaps_script .= '//'.str_repeat('=',72)."\n";
                    $leaps_script .= "//\tLeapSecond Rules\n";
                    $leaps_script .= '//'.str_repeat('=',72)."\n";
                    $leaps_script .= "\$Leaps = array();\n";
                }
                // if the current year is the same as the
                // last year supplied, increment the counter
                if($year == $leap_last) {
                    $c++;
                } else {
                    $c = 0;
                }
                $leap_last = $year;
                
                $leaps_script .= "\$Leaps['{$year}'][{$c}] = array(";
                $internal = '';
                foreach($data as $rule) {
                    if(!empty($internal)) $internal .= ',';
                    $internal .= "'{$rule}'"; 
                }
                $leaps_script .= "{$internal});\n";
                break;
                
            case 'zone':
                // get rid of the Zone keyword
                array_shift($data); 
                // get the name of this
                $name = array_shift($data); 
                
                if($zones_script == null) {
                    $zones_script .= '//'.str_repeat('=',72)."\n";
                    $zones_script .= "//\tTimeZones\n";
                    $zones_script .= '//'.str_repeat('=',72)."\n";
                    $zones_script .= "\$Zones = array();\n";
                }
                // if the current name is the same as the
                // last name supplied, increment the counter
                if($name == $zone_last) {
                    $c++;
                } else {
                    $c = 0;
                }
                $zone_last = $name;
                
                $zones_script .= "\$Zones['{$name}'][{$c}] = array(";
                $internal = '';
                foreach($data as $rule) {
                    if(!empty($internal)) $internal .= ',';
                    $internal .= "'{$rule}'";
                }
                $zones_script .= "{$internal});\n";
                break;
            
            case 'zone2':
                $c++;
                $zones_script .= "\$Zones['{$name}'][{$c}] = array(";
                $internal = '';
                foreach($data as $rule) {
                    if(!empty($internal)) $internal .= ',';
                    $internal .= "'{$rule}'"; 
                }
                $zones_script .= "{$internal});\n";
                break;
                
        }
    }
    $results = $rules_script.$zones_script.$links_script.$leaps_script;
    return $results;
}
?>