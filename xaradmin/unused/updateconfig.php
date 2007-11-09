<?php
/**
 * File: $Id:
 *
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @author calendar module development team
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function calendar_admin_updateconfig()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;


    // Variables from phpIcalendar config.inc.php
    if (!xarVarFetch('default_view', 'isset', $default_view, 'Week', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minical_view', 'isset', $minical_view, 'Week', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_cal',  'isset',$default_cal,  array(),  XARVAR_NOT_REQUIRED)) {return;}

    if (!xarVarFetch('cal_sdow', 'int:0:6'       , $cal_sdow, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('day_start', 'isset'            , $day_start                , '0800', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('day_end', 'isset'              , $day_end                 ,'2100', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('gridLength', 'int:0'           , $gridLength              , 15, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('num_years', 'int:0'            , $num_years               , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('month_event_lines', 'int:0'    , $month_event_lines       , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tomorrows_events_lines','int:0', $tomorrows_events_lines  , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allday_week_lines', 'int:0'    , $allday_week_lines       , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('week_events_lines', 'int:0'    , $week_events_lines       , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('second_offset', 'int:0'        , $second_offset           , 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bleed_time', 'int:0'           , $bleed_time              , 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('display_custom_goto', 'checkbox'  , $display_custom_goto     , 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('display_ical_list', 'checkbox'    , $display_ical_list       , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allow_webcals', 'checkbox'        , $allow_webcals           , 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('this_months_events', 'checkbox'   , $this_months_events      , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_color_cals', 'checkbox'       , $use_color_cals          , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('daysofweek_dayview', 'checkbox'   , $daysofweek_dayview      , 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enable_rss', 'checkbox'           , $enable_rss              , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_search', 'checkbox'          , $show_search             , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allow_preferences', 'checkbox'    , $allow_preferences       , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('printview_default', 'checkbox'    , $printview_default       , 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_todos', 'checkbox'           , $show_todos              , 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_completed', 'checkbox'       , $show_completed          , 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allow_login', 'checkbox'          , $allow_login             , 0, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.  Note that the default values are set in
    // xarVarFetch when recieving the incoming values, so no extra processing
    // is needed when setting the variables here.
    xarModVars::set('calendar', 'SupportShortURLs', $shorturls);

    // Variables from phpIcalendar config.inc.php
    xarModVars::set('calendar','default_view'           , $default_view);
    xarModVars::set('calendar','minical_view'           , $minical_view);
    xarModVars::set('calendar','default_cal'            , serialize($default_cal));
    xarModVars::set('calendar','cal_sdow'               , $cal_sdow);
    xarModVars::set('calendar','day_start'              , $day_start);
    xarModVars::set('calendar','day_end'                , $day_end);
    xarModVars::set('calendar','gridLength'             , $gridLength);
    xarModVars::set('calendar','num_years'              , $num_years);
    xarModVars::set('calendar','month_event_lines'      , $month_event_lines);
    xarModVars::set('calendar','tomorrows_events_lines' , $tomorrows_events_lines);
    xarModVars::set('calendar','allday_week_lines'      , $allday_week_lines);
    xarModVars::set('calendar','week_events_lines'      , $week_events_lines);
    xarModVars::set('calendar','second_offset'          , $second_offset);
    xarModVars::set('calendar','bleed_time'             , $bleed_time);
    xarModVars::set('calendar','display_custom_goto'    , $display_custom_goto);
    xarModVars::set('calendar','display_ical_list'      , $display_ical_list);
    xarModVars::set('calendar','allow_webcals'          , $allow_webcals);
    xarModVars::set('calendar','this_months_events'     , $this_months_events);
    xarModVars::set('calendar','use_color_cals'         , $use_color_cals);
    xarModVars::set('calendar','daysofweek_dayview'     , $daysofweek_dayview);
    xarModVars::set('calendar','enable_rss'             , $enable_rss);
    xarModVars::set('calendar','show_search'            , $show_search);
    xarModVars::set('calendar','allow_preferences'      , $allow_preferences);
    xarModVars::set('calendar','printview_default'      , $printview_default);
    xarModVars::set('calendar','show_todos'             , $show_todos);
    xarModVars::set('calendar','show_completed'         , $show_completed);
    xarModVars::set('calendar','allow_login'            , $allow_login);

    xarModCallHooks('module','updateconfig','calendar',
                   array('module' => 'calendar'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('calendar', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
