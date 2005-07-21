<?php
function calendar_admin_modifyconfig()
{
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('calendar', 'admin', 'menu'); 
    $data = array_merge($data,xarModAPIFunc('calendar', 'admin', 'get_calendars')); 
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('Admincalendar')) return; 
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 

    // Variables from phpIcalendar config.inc.php    
    $data['default_view'] = xarModGetVar('calendar', 'default_view');
    $data['minical_view'] = xarModGetVar('calendar', 'minical_view');
    $data['default_cal'] = unserialize(xarModGetVar('calendar', 'default_cal'));
    $data['cal_sdow']         = xarModGetVar('calendar', 'cal_sdow');     
    $data['week_start_day']         = xarModGetVar('calendar','week_start_day'         ); 
    $data['day_start']              = xarModGetVar('calendar','day_start'              ); 
    $data['day_end']                = xarModGetVar('calendar','day_end'                ); 
    $data['gridLength']             = xarModGetVar('calendar','gridLength'             ); 
    $data['num_years']              = xarModGetVar('calendar','num_years'              ); 
    $data['month_event_lines']      = xarModGetVar('calendar','month_event_lines'      ); 
    $data['tomorrows_events_lines'] = xarModGetVar('calendar','tomorrows_events_lines' ); 
    $data['allday_week_lines']      = xarModGetVar('calendar','allday_week_lines'      ); 
    $data['week_events_lines']      = xarModGetVar('calendar','week_events_lines'      ); 
    $data['second_offset']          = xarModGetVar('calendar','second_offset'          ); 
    $data['bleed_time']             = xarModGetVar('calendar','bleed_time'             ); 
    
    $data['display_custom_goto']    = xarModGetVar('calendar','display_custom_goto'    ); 
    $data['display_custom_gotochecked'] = xarModGetVar('calendar', 'display_custom_goto') ? 'checked' : '';
    $data['display_ical_list']      = xarModGetVar('calendar','display_ical_list'      ); 
    $data['display_ical_listchecked'] = xarModGetVar('calendar', 'display_ical_list') ? 'checked' : '';
    $data['allow_webcals']          = xarModGetVar('calendar','allow_webcals'          ); 
    $data['allow_webcalschecked'] = xarModGetVar('calendar', 'allow_webcals') ? 'checked' : '';
    $data['this_months_events']     = xarModGetVar('calendar','this_months_events'     ); 
    $data['this_months_eventschecked'] = xarModGetVar('calendar', 'this_months_events') ? 'checked' : '';
    $data['use_color_cals']         = xarModGetVar('calendar','use_color_cals'         ); 
    $data['use_color_calschecked'] = xarModGetVar('calendar', 'use_color_cals') ? 'checked' : '';
    $data['daysofweek_dayview']     = xarModGetVar('calendar','daysofweek_dayview'     ); 
    $data['daysofweek_dayviewchecked'] = xarModGetVar('calendar', 'daysofweek_dayview') ? 'checked' : '';
    $data['enable_rss']             = xarModGetVar('calendar','enable_rss'             ); 
    $data['enable_rsschecked'] = xarModGetVar('calendar', 'enable_rss') ? 'checked' : '';
    $data['show_search']            = xarModGetVar('calendar','show_search'            ); 
    $data['show_searchchecked'] = xarModGetVar('calendar', 'show_search') ? 'checked' : '';
    $data['allow_preferences']      = xarModGetVar('calendar','allow_preferences'      );
    $data['allow_preferenceschecked'] = xarModGetVar('calendar', 'allow_preferences') ? 'checked' : '';
    $data['printview_default']      = xarModGetVar('calendar','printview_default'      ); 
    $data['printview_defaultchecked'] = xarModGetVar('calendar', 'printview_default') ? 'checked' : '';
    $data['show_todos']             = xarModGetVar('calendar','show_todos'             ); 
    $data['show_todoschecked'] = xarModGetVar('calendar', 'show_todos') ? 'checked' : '';
    $data['show_completed']         = xarModGetVar('calendar','show_completed'         ); 
    $data['show_completedchecked'] = xarModGetVar('calendar', 'show_completed') ? 'checked' : '';
    $data['allow_login']            = xarModGetVar('calendar','allow_login'            ); 
    $data['allow_loginchecked'] = xarModGetVar('calendar', 'allow_login') ? 'checked' : '';

    /*
    //  list of options from config.inc.php not included
    $style_sheet            = 'silver';         // Themes support - silver, red, green, orange, grey, tan
    $language               = 'English';        // Language support - 'English', 'Polish', 'German', 'French', 'Dutch', 'Danish', 'Italian', 'Japanese', 'Norwegian', 'Spanish', 'Swedish', 'Portuguese', 'Catalan', 'Traditional_Chinese', 'Esperanto', 'Korean'
    $calendar_path          = '';               // Leave this blank on most installs, place your full path to calendars if they are outside the phpicalendar folder.
    $tmp_dir                = '/tmp';           // The temporary directory on your system (/tmp is fine for UNIXes including Mac OS X)
    $cookie_uri             = '';               // The HTTP URL to the PHP iCalendar directory, ie. http://www.example.com/phpicalendar -- AUTO SETTING -- Only set if you are having cookie issues.
    $download_uri           = '';               // The HTTP URL to your calendars directory, ie. http://www.example.com/phpicalendar/calendars -- AUTO SETTING -- Only set if you are having subscribe issues.
    $default_path           = 'http://www.example.com/phpicalendar';                        // The HTTP URL to the PHP iCalendar directory, ie. http://www.example.com/phpicalendar
    $timezone               = '';               // Set timezone. Read TIMEZONES file for more information
    $save_parsed_cals       = 'yes';            // Recommended 'yes'. Saves a copy of the cal in /tmp after it's been parsed. Improves performence.
    */

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration')); 
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('calendar', 'SupportShortURLs') ?
    'checked' : '';


    //TODO: should I include this stuff? --amoro    
    $hooks = xarModCallHooks('module', 'modifyconfig', 'calendar',
        array('module' => 'calendar'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    } 
    // Return the template variables defined in this function
    return $data;
}
?>
