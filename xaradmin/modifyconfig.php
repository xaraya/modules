<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function calendar_admin_modifyconfig()
{
    $data = xarMod::apiFunc('calendar', 'admin', 'menu');
    $data = array_merge($data,xarMod::apiFunc('calendar', 'admin', 'get_calendars'));
    if (!xarSecurity::check('AdminCalendar')) return;
    
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'calendar'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls','use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'calendar_general':
                    sys::import('modules.calendar.pear.Calendar.Util.Textual');
                    $data['weekdays'] = Calendar_Util_Textual::weekdayNames();
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) return;
            if (!xarVar::fetch('windowwidth', 'int:1', $windowwidth, xarModVars::get('calendar', 'aliasname'), xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('minutesperunit', 'int:1', $minutesperunit, xarModVars::get('calendar', 'minutesperunit'), xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('unitheight', 'int:1', $unitheight, xarModVars::get('calendar', 'unitheight'), xarVar::NOT_REQUIRED)) return;

            if (!xarVar::fetch('default_view', 'str:1', $default_view, xarModVars::get('calendar', 'default_view'), xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('cal_sdow', 'str:1', $cal_sdow, xarModVars::get('calendar', 'cal_sdow'), xarVar::NOT_REQUIRED)) return;

            $isvalid = $data['module_settings']->checkInput();
            if (!$isvalid) {
                return xarTpl::module('calendar','admin','modifyconfig', $data);        
            } else {
                $itemid = $data['module_settings']->updateItem();
            }

            sys::import('modules.dynamicdata.class.properties.master');
            $timeproperty = DataPropertyMaster::getProperty(array('type' => 'formattedtime'));
            $day_start = $timeproperty->checkInput('day_start') ? $timeproperty->getValue() : xarModVars::get('calendar','day_start');
            $day_end = $timeproperty->checkInput('day_end') ? $timeproperty->getValue() : xarModVars::get('calendar','day_end');

            if ($data['tab'] == 'calendar_general') {
                xarModVars::set('calendar', 'items_per_page', $items_per_page);
                xarModVars::set('calendar', 'supportshorturls', $shorturls);
                xarModVars::set('calendar', 'useModuleAlias', $useModuleAlias);
                xarModVars::set('calendar', 'aliasname', $aliasname);
                xarModVars::set('calendar', 'windowwidth', $windowwidth);
                xarModVars::set('calendar', 'minutesperunit', $minutesperunit);
                xarModVars::set('calendar', 'unitheight', $unitheight);

                xarModVars::set('calendar', 'default_view', $default_view);
                xarModVars::set('calendar', 'cal_sdow', $cal_sdow);
                xarModVars::set('calendar', 'day_start', $day_start);
                xarModVars::set('calendar', 'day_end', $day_end);
            }
            $regid = xarMod::getRegID($tabmodule);
            xarModItemVars::set('calendar', 'windowwidth', $windowwidth, $regid);
            xarModItemVars::set('calendar', 'minutesperunit', $minutesperunit, $regid);
            xarModItemVars::set('calendar', 'unitheight', $unitheight, $regid);

            xarModItemVars::set('calendar', 'default_view', $default_view, $regid);
            xarModItemVars::set('calendar', 'cal_sdow', $cal_sdow, $regid);
            xarModItemVars::set('calendar', 'day_start', $day_start, $regid);
            xarModItemVars::set('calendar', 'day_end', $day_end, $regid);

            xarController::redirect(xarController::URL('calendar', 'admin', 'modifyconfig',array('tab' => $data['tab'])));
            return true;
            break;
    }

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation

    // Variables from phpIcalendar config.inc.php
    $data['default_view'] = xarModVars::get('calendar', 'default_view');
    $data['minical_view'] = xarModVars::get('calendar', 'minical_view');
    $data['default_cal'] = unserialize(xarModVars::get('calendar', 'default_cal'));
    $data['cal_sdow']         = xarModVars::get('calendar', 'cal_sdow');
    $data['week_start_day']         = xarModVars::get('calendar','week_start_day'         );
    $data['day_start']              = xarModVars::get('calendar','day_start'              );
    $data['day_end']                = xarModVars::get('calendar','day_end'                );
    $data['gridLength']             = xarModVars::get('calendar','gridLength'             );
    $data['num_years']              = xarModVars::get('calendar','num_years'              );
    $data['month_event_lines']      = xarModVars::get('calendar','month_event_lines'      );
    $data['tomorrows_events_lines'] = xarModVars::get('calendar','tomorrows_events_lines' );
    $data['allday_week_lines']      = xarModVars::get('calendar','allday_week_lines'      );
    $data['week_events_lines']      = xarModVars::get('calendar','week_events_lines'      );
    $data['second_offset']          = xarModVars::get('calendar','second_offset'          );
    $data['bleed_time']             = xarModVars::get('calendar','bleed_time'             );

    $data['display_custom_goto']    = xarModVars::get('calendar','display_custom_goto'    );
    $data['display_custom_gotochecked'] = xarModVars::get('calendar', 'display_custom_goto') ? 'checked' : '';
    $data['display_ical_list']      = xarModVars::get('calendar','display_ical_list'      );
    $data['display_ical_listchecked'] = xarModVars::get('calendar', 'display_ical_list') ? 'checked' : '';
    $data['allow_webcals']          = xarModVars::get('calendar','allow_webcals'          );
    $data['allow_webcalschecked'] = xarModVars::get('calendar', 'allow_webcals') ? 'checked' : '';
    $data['this_months_events']     = xarModVars::get('calendar','this_months_events'     );
    $data['this_months_eventschecked'] = xarModVars::get('calendar', 'this_months_events') ? 'checked' : '';
    $data['use_color_cals']         = xarModVars::get('calendar','use_color_cals'         );
    $data['use_color_calschecked'] = xarModVars::get('calendar', 'use_color_cals') ? 'checked' : '';
    $data['daysofweek_dayview']     = xarModVars::get('calendar','daysofweek_dayview'     );
    $data['daysofweek_dayviewchecked'] = xarModVars::get('calendar', 'daysofweek_dayview') ? 'checked' : '';
    $data['enable_rss']             = xarModVars::get('calendar','enable_rss'             );
    $data['enable_rsschecked'] = xarModVars::get('calendar', 'enable_rss') ? 'checked' : '';
    $data['show_search']            = xarModVars::get('calendar','show_search'            );
    $data['show_searchchecked'] = xarModVars::get('calendar', 'show_search') ? 'checked' : '';
    $data['allow_preferences']      = xarModVars::get('calendar','allow_preferences'      );
    $data['allow_preferenceschecked'] = xarModVars::get('calendar', 'allow_preferences') ? 'checked' : '';
    $data['printview_default']      = xarModVars::get('calendar','printview_default'      );
    $data['printview_defaultchecked'] = xarModVars::get('calendar', 'printview_default') ? 'checked' : '';
    $data['show_todos']             = xarModVars::get('calendar','show_todos'             );
    $data['show_todoschecked'] = xarModVars::get('calendar', 'show_todos') ? 'checked' : '';
    $data['show_completed']         = xarModVars::get('calendar','show_completed'         );
    $data['show_completedchecked'] = xarModVars::get('calendar', 'show_completed') ? 'checked' : '';
    $data['allow_login']            = xarModVars::get('calendar','allow_login'            );
    $data['allow_loginchecked'] = xarModVars::get('calendar', 'allow_login') ? 'checked' : '';

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

    $data['updatebutton'] = xarVar::prepForDisplay(xarML('Update Configuration'));
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModVars::get('calendar', 'SupportShortURLs') ?
    'checked' : '';


/*    //TODO: should I include this stuff? --amoro
    $hooks = xarModHooks::call('module', 'modifyconfig', 'calendar',
        array('module' => 'calendar'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
*/
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
?>
