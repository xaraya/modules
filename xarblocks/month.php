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
    if ($vars['showprevious'] == 1) {
        $callist[]= strtotime("-1 month ".date('Ym') . '01');
        $months = $months -1;
    }

    if ($months > 0) {
        for ($i = 0; $i<= $months-1; $i++) { 
           if ($i ==0) {
                $callist[] = strtotime(date('Ym') . '01');
            } else {
                $callist[] = strtotime("+".$i." month ".date('Ym') . '01');
            }
        }
    }

    $calarray = array();
    foreach ($callist as $k=>$calitem) {

    //$ustartdate = strtotime("+1 month ".date('Ym') . '01');
        $ustartdate = $calitem;
        $uenddate = strtotime('+1 month -1 day', $ustartdate);
    
        include_once(dirname(__FILE__) . '/../xarincludes/calendar.inc.php');
        $cal = new ieventsCalendar;
    
        $cal->cid = $vars['cid'];
        $cal->calFormat = 'smallMonth';
    
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
    
        $cal->displayPrevNext = true;
        $cal->displayEvents = true;
        $cal->startingDOW = $startdayofweek;
        $cal->showWeek = false;
        $cal->calDay = strtotime(date('Ymd', $ustartdate));
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
            $cal->addEvent(
                $eventvalue['startdate'],
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
        $data['enddate']= $temp['enddate'];
        $data['cid']=isset($temp['cid']) ?$temp['cid']:'';
    }
    $data['numberofmonths'] = $vars['numberofmonths'] ;
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