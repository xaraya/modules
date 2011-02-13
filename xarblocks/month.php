<?php
/**
 * IEvents Month block
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 */

// {ML_include 'modules/ievents/xarincludes/calendar.inc.php'}

/**
 * Block init - holds security.
 */
function ievents_monthblock_init()
{
    return array(
        'cid' => 0,
        'usecalname' => 0,
        'showfulllink' => 1,
        'showprevious' => false,
        'numberofmonths' => 1,
        'nocache' => 1, // don't cache by default
        'pageshared' => 1, // but if you do, share across pages
        'usershared' => 1, // and for group members
        'cacheexpire' => null
    );
}

/**
 * block information array
 */
function ievents_monthblock_info()
{
    return array(
        'text_type' => 'Month',
        'text_type_long' => 'IEvents Month',
        'module' => 'ievents',
        'func_update' => 'ievents_monthblock_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function ievents_monthblock_display($blockinfo)
{
    // Security Check
//    if (!xarSecurityCheck('ViewIEventBlock', 0, 'Block', "month:$blockinfo[title]:$blockinfo[bid]")) {return;}

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // FIXME: this should actually be allowed, i.e. all available calendars.
    if (!isset($vars['cid']) || (int)$vars['cid'] < 0) {
        return;
    }

    if (!isset($vars['usecalname'])) {
        $vars['usecalname'] = false;
    }

    if (!isset($vars['showfulllink'])) {
        $vars['showfulllink'] = true;
    }
    if (!isset($vars['showprevious'])) {
        $vars['showprevious'] = false;
    }
    if (!isset($vars['numberofmonths']) || empty($vars['numberofmonths'])) {
        $vars['numberofmonths'] = 1;
    }
    if (!isset($vars['nextprevday'])) {
        $vars['nextprevday'] = false;
    }
    if (!isset($vars['nextprevmonth'])) {
        $vars['nextprevmonth'] = false;
    }
    extract(xarModAPIfunc('ievents', 'user', 'params',
        array('knames' => 'html_fields,q_fields,display_formats,locale')
    ));


    $data = $vars;

    //JDJ 2009-07-03 Not sure the significance of 'smallmonth', but it messes up the listings that we link to.
    $group = 'month'; //'smallmonth';
    $startdayofweek = xarModGetVar('ievents','startdayofweek');
    $numitems = xarModGetVar('ievents', 'default_numitems');

    $callist = array();

    $months = $vars['numberofmonths'];
    $prior = array();

    if (!defined('YYYYMM_OR_YYYYMMDD_REGEXP')) define('YYYYMM_OR_YYYYMMDD_REGEXP', 'regexp:/^(19|20)[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])?$/');
    if (!defined('YYYYMMDD_REGEXP')) define('YYYYMMDD_REGEXP', 'regexp:/^(19|20)[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])$/');

    xarVarFetch('eid', 'id', $eid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('startdate', YYYYMM_OR_YYYYMMDD_REGEXP, $startdate, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('enddate', YYYYMM_OR_YYYYMMDD_REGEXP, $enddate, NULL, XARVAR_NOT_REQUIRED);

    if(!empty($eid)) {
        $event = xarModAPIfunc('ievents', 'user', 'getevent', array('eid' => $eid)); // TODO: pev: allow in api only get startdate
        if(!empty($event['startdate']) && $event['startdate'] > 0) {
            $eid_startdate = date('Ymd', $event['startdate']);
            // startdate is !(yyyymmdd or yyyymm)
            if (!xarVarValidate(YYYYMMDD_REGEXP, $eid_startdate, true)) $eid_startdate = NULL;
        }
    }

    if ($vars['nextprevday'] or $vars['nextprevmonth']) {
        if(!empty($eid_startdate)) {
            $yyyymm01 = substr($eid_startdate, 0, 6) . '01';
        } elseif(!empty($startdate)) {
            $yyyymm01 = substr($startdate, 0, 6) . '01';
        } else {
            // startdate is not defined (or bad) - other sites without startdate (non ievens sites)
            $yyyymm01 = date('Ym') . '01';
        }
    } else {
        $yyyymm01 = date('Ym') . '01';
    }

    if ($vars['showprevious'] == 1) {
        $callist[]= strtotime("-1 month ".$yyyymm01);
        $months = $months -1;
    }

    if ($months > 0) {
        for ($i = 0; $i<= $months-1; $i++) {
           if ($i ==0) {
                $callist[] = strtotime($yyyymm01);
            } else {
                $callist[] = strtotime("+".$i." month ".$yyyymm01);
            }
        }
    }

    $calarray = array();
    foreach ($callist as $k=>$ustartdate) {
        include_once(dirname(__FILE__) . '/../xarincludes/calendar.inc.php');
        $cal = new ieventsCalendar;
    
        $cal->cid = $vars['cid'];
        $cal->calFormat = 'smallMonth';

        $uenddate = strtotime('+1 month -1 day', $ustartdate);

        // day which is shown on calendar (in day view or in one day event), default no selected day
        $selected_day = "";
        // $selected_date shows date in format yyyymmdd, where dd is important
        $selected_date = "";

        $ustartdate_yyyymm = date("Ym", $ustartdate);
        if (!empty($eid_startdate)) {
            if (substr($eid_startdate,0, 6) == $ustartdate_yyyymm) {
                $selected_date = $eid_startdate;
                $selected_day = substr($eid_startdate, 6, 2);
            }
        } elseif(strlen($startdate) == 8 && $startdate == $enddate && substr($startdate, 0, 6) == $ustartdate_yyyymm) {
            // startdate is yyyymmdd
            $selected_date = $startdate;
            $selected_day = substr($startdate, 6, 2);
        } elseif(strlen($startdate) == 6 || strlen($startdate) == 8) {
            // startdate is yyyymmdd or yyyymm

            if ($ustartdate_yyyymm == date("Ym")) {
                $selected_date = $ustartdate_yyyymm . date('d'); // today
            } else {
                $selected_date = $ustartdate_yyyymm . '01'; // not current month in this year => first day
            }

            // TODO: pev: is it used?
            if(strlen($startdate) == 8 && substr($startdate, 0, 6) == $ustartdate_yyyymm) {
                $selected_day = substr($startdate, 6, 2);
            }
        }
    
        if (xarVarValidate('enum:long:short', $vars['monthformat'], true)) {
            $cal->monthFormat = $vars['monthformat'];
        } else {
            // There is more than enough room for full month names in the small calender.
            $cal->monthFormat = 'long';
        }
    
        if (xarVarValidate('enum:long:short:xshort:xxshort', $vars['dowformat'], true)) {
            $cal->DOWformat = $vars['dowformat'];
        } else {
            $cal->DOWformat = 'xxshort';
        }
    
        if (!empty($vars['showtitle']) && xarVarValidate('bool', $vars['showtitle'])) {
            $cal->showTitle = $vars['showtitle'];
        } else {
            $cal->showTitle = true;
        }
    
        if (xarVarValidate('bool', $vars['linkmonth'])) $cal->linkMonth = $vars['linkmonth'];
        if (xarVarValidate('bool', $vars['nextprevday'])) $cal->nextPrevDay = $vars['nextprevday'];
        if (xarVarValidate('bool', $vars['nextprevmonth'])) $cal->nextPrevMonth = $vars['nextprevmonth'];
    
        $cal->displayPrevNext = true;
        $cal->displayEvents = true;
        $cal->startingDOW = $startdayofweek;
        $cal->showWeek = false;
        $cal->calDay = strtotime(date('Ymd', strtotime($selected_date)));
        $cal->calWeek = $ustartdate;
        $cal->calMonth = date('m', $ustartdate);
        $cal->calQuarter = floor(((date('m', $ustartdate) - 1) / 3) + 1);
        $cal->calYear = date('Y', $ustartdate);
        $cal->quanta = xarModGetVar('ievents', 'quanta');
    
        // Pass the locale data in.
        $cal->dayNames = $locale['days']['long'];
        $cal->dayNamesShort = $locale['days']['short'];
        $cal->dayNamesXShort = $locale['days']['xshort'];
        $cal->dayNamesXXShort = $locale['days']['xxshort'];
    
        $cal->monthNames = $locale['months']['long'];
        $cal->monthNamesShort = $locale['months']['short'];

        $cal->selectedDay = $selected_day;
    
        $url_params = array(
            'startdate' => $ustartdate,
            'enddate' => $uenddate,
        );

        $event_params = array(
            'startnum' => 1,
            'numitems' => $numitems,
            'startdate' => $ustartdate,
            'enddate' => $uenddate,
            'cid' => $vars['cid'],
        );
    
        // Display only ACTIVE events on the main view. Include DRAFT and INACTIVE only when the user has permissions to change events in that calendar.
        if (!xarSecurityCheck('CommentIEvent', 0, 'IEvent')) {
            $event_params['status'] = 'ACTIVE';
        }

        // Get the events.
        $events = xarModAPIfunc('ievents', 'user', 'getevents', $event_params);
    //if (xarUserGetVar('uname') == 'judgej') var_dump($events);
        $groups = array();
        foreach($events as $eventkey => $eventvalue) {
            // Add some other details to each event, that will be useful.
            // Add the detail URL, taking into account the current search criteria.
            $eventvalue['detail_url'] = xarModURL(
                'ievents', 'user', 'view',
                array_merge($url_params, array('eid' => $eventvalue['eid'], 'range' => 'custom'))
            );
    
            // Add the event detail to the calendar.
            // At the moment only a count of the events is shown on the small calendar, but the details
            // are all there if needed.

            // if default_drule == overlap, show more days (recurring) event in all it's days
            // TODO: allow to config it individually for each event
            $dates = array();
            extract(xarModAPIfunc('ievents', 'user', 'params', array('knames' => 'default_drule')));
            if ($default_drule == 'overlap' && date('Ymd', $eventvalue['enddate']) > date('Ymd', $eventvalue['startdate'])) {

                $event_startdate = date('Ymd', $eventvalue['startdate']);
                // event started some month before - start from 1st this month
                if (date('m', $eventvalue['startdate']) < $cal->calMonth) $event_startdate = $cal->calYear . $cal->calMonth . "01";
                for ($i = $event_startdate; $i <= date('Ymd', $eventvalue['enddate']); $i = date('Ymd', strtotime("+1 day ".$i))) {
                    $date = strtotime($i);
                    if ($cal->calMonth == date('m', $date)) $dates[] = $date;
                    elseif ($cal->calMonth < date('m', $date)) break; // we're in next month
                }
            }
            else {
                $dates[] = $eventvalue['startdate'];
            }

            foreach ($dates as $date) {
                $cal->addEvent(
                    $date,

                    $eventvalue['title'],
                    $eventvalue['detail_url'],
                    $eventvalue['all_day'],
                    xarModURL('ievents','user','view',array(
                        'startdate' => date('Ymd', $eventvalue['startdate']),
                        'enddate' => date('Ymd', $eventvalue['startdate']),
                        'group' => 'day',
                        'range' => 'custom',
                        'eid' => $eventvalue['eid'],
                        'title' => $eventvalue['title'],
                        'summary' => $eventvalue['summary'],
                    ))
                );
            }
        }

        if ($vars['usecalname'] && !empty($vars['cid'])) {
            $calendars = xarModAPIFunc('ievents','user','getcalendars',array('cid' => $vars['cid']));
            $blockinfo['title'] = $calendars[$vars['cid']]['short_name'];
            $cal->showTitle = false;
        }
    
        $calarray[$k]['cid'] = $vars['cid'];
        $calarray[$k]['group'] = $group;
    
        $calarray[$k]['startdate'] = date('Ym01', $ustartdate);
        $calarray[$k]['enddate'] = date('Ymd', strtotime('+1 month -1 day', $ustartdate));
    
        $calarray[$k]['startmonth'] = date('Ym', $ustartdate);
        $calarray[$k]['endmonth'] =  $calarray[$k]['startmonth'];
    
        $calarray[$k]['cal_output'] = $cal->display();
        $calarray[$k]['showfulllink'] = $vars['showfulllink'];
        $calarray[$k]['cal'] = $cal;

    } //end foreach cal  
    
    if ($vars['numberofmonths'] == 1) {
        $data = current($calarray);
    } else {
        $data['cals'] = $calarray;
        $temp =current($calarray);
        $data['group']= $temp['group'];
        $data['startdate']= $temp['startdate'];
        $data['startmonth']= $temp['startmonth'];
        $data['enddate']= $temp['enddate'];
        $data['endmonth']= $temp['endmonth'];                
        $data['cid']=isset($temp['cid']) ?$temp['cid']:'';
    }
    $data['numberofmonths'] = $vars['numberofmonths'] ;
    $data['selected_month'] = $cal->monthNames[(int)substr($ustartdate_yyyymm, 4, 2)];
    $blockinfo['content'] = $data;
    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function ievents_monthblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['cid'])) {
        $vars['cid'] = 0;
    }
    if (empty($vars['usecalname'])) {
        $vars['usecalname'] = 0;
    }
    if (empty($vars['numberofmonths'])) {
        $vars['numberofmonths'] = 1;
    }
    if (empty($vars['showprevious'])) {
        $vars['showprevious'] = 0;
    }         
    if (!empty($vars['showfulllink']) && $vars['showfulllink'] != 0) {
        $vars['showfulllink'] = 1;
    }

    $vars['blockid'] = $blockinfo['bid'];
    $vars['calendarlist'] = xarModAPIfunc('ievents','user','list_calendars',array('readable'=>true,'mandatory'=>true));
    $vars['calendarlist'] = array_merge(array(0 => xarML('All Calendars')), $vars['calendarlist']);

    return $vars;

}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function ievents_monthblock_update($blockinfo)
{
   if (!xarVarFetch('cid', 'int', $vars['cid'], 0, XARVAR_NOT_REQUIRED)) {return;}
   if (!xarVarFetch('usecalname', 'checkbox', $vars['usecalname'], 0, XARVAR_NOT_REQUIRED)) {return;}
   if (!xarVarFetch('showfulllink', 'checkbox', $vars['showfulllink'], 0, XARVAR_NOT_REQUIRED)) {return;}
   if (!xarVarFetch('numberofmonths', 'int:1', $vars['numberofmonths'], 1, XARVAR_NOT_REQUIRED)) {return;}
   if (!xarVarFetch('showprevious', 'checkbox', $vars['showprevious'], false, XARVAR_NOT_REQUIRED)) {return;}   
    $blockinfo['content'] = $vars;

    return $blockinfo;
}

?>
