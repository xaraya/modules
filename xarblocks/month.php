<?php
/**
 * IEvents Month block
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 */

/**
 * Block init - holds security.
 */
function ievents_monthblock_init()
{
    return array('cid' => 0,
                 'usecalname' => 0,
                 'showfulllink' => 1,
                 'nocache' => 1, // don't cache by default
                 'pageshared' => 1, // but if you do, share across pages
                 'usershared' => 1, // and for group members
                 'cacheexpire' => null);
}

/**
 * block information array
 */
function ievents_monthblock_info()
{
    return array('text_type' => 'Month',
         'text_type_long' => 'IEvents Month',
         'module' => 'ievents',
         'func_update' => 'ievents_monthblock_update',
         'allow_multiple' => true,
         'form_content' => false,
         'form_refresh' => false,
         'show_preview' => true);

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

    if(!isset($vars['cid']) || (int)$vars['cid'] < 1) {
        return;
    }

    xarTplAddStyleLink('ievents','ievents');

    if(!isset($vars['usecalname'])) {
        $vars['usecalname'] = false;
    }

    if(!isset($vars['showfulllink'])) {
        $vars['showfulllink'] = true;
    }

    extract(xarModAPIfunc('ievents', 'user', 'params',
        array('knames' => 'html_fields,q_fields,display_formats,locale')    
    ));


    $data = $vars;

    $group = 'smallmonth';
    $startdayofweek = xarModGetVar('ievents','startdayofweek');
    $numitems = xarModGetVar('ievents', 'default_numitems');

    $ustartdate = strtotime(date('Ym') . '01');
    $uenddate = strtotime('+1 month -1 day', $ustartdate);

    include_once(dirname(__FILE__) . '/../xarincludes/calendar.inc.php');
    $cal = new ieventsCalendar;

    $cal->cid = $vars['cid'];

    $cal->calFormat = 'smallMonth';
    $cal->monthFormat = 'short';

    if(isset($vars['dowformat'])) {
      $cal->DOWformat = $vars['dowformat'];
    }
    else {
      $cal->DOWformat = 'xxshort';
    }

    $cal->displayPrevNext = false;
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
    );
    $events = xarModAPIfunc('ievents', 'user', 'getevents', $event_params);

    $groups = array();
    foreach($events as $eventkey => $eventvalue) {
        // Add some other details to each event, that will be useful.
        // Add the detail URL, taking into account the current search criteria.
        $eventvalue['detail_url'] = xarModURL(
            'ievents', 'user', 'view',
            array_merge($url_params, array('eid' => $eventvalue['eid']))
        );

        $cal->addEvent(
            $eventvalue['startdate'],
            $eventvalue['title'],
            $eventvalue['detail_url'],
            $eventvalue['all_day'],
            xarModURL('ievents','user','view',array(
                'startdate' => date('Ymd', $eventvalue['startdate']),
                'enddate' => date('Ymd', $eventvalue['startdate']),
                'group' => 'day'
            ))
        );
    }
    if($vars['usecalname']) {
        $calendars = xarModAPIFunc('ievents','user','getcalendars',array('cid' => $cid));
        $blockinfo['title'] = $calendars[$cid]['short_name'];
        $cal->showTitle = false;
    }

    $data['cid'] = $vars['cid'];
    $data['group'] = $group;
    $data['startdate'] = date('Ym01', $ustartdate);
    $data['enddate'] = date('Ymd', strtotime('+1 month -1 day', $ustartdate));
    $data['cal_output'] = $cal->display();
    $data['showfulllink'] = $vars['showfulllink'];

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
    if (!empty($vars['showfulllink']) && $vars['showfulllink'] != 0) {
        $vars['showfulllink'] = 1;
    }

    $vars['blockid'] = $blockinfo['bid'];
    $vars['calendarlist'] = xarModAPIfunc('ievents','user','list_calendars',array('readable'=>true,'mandatory'=>true));

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

    $blockinfo['content'] = $vars;

    return $blockinfo;
}

?>
