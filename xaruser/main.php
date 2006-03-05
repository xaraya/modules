<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Main function of Stats module
 *
 * This function collects and packages up the data needed in the
 * main overview page of the stats module
 *
 * @param   none
 * @return  array $data - contains all data needed in the template user-main.xd
 */
function stats_user_main()
{
    // Security check
    if(!xarSecurityCheck('OverviewStats')) return;

    xarVarFetch('year',  'int', $year,  0, XARVAR_NOT_REQUIRED);
    xarVarFetch('month', 'int', $month, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('day',   'int', $day,   0, XARVAR_NOT_REQUIRED);

    // load the locale data
    $localeData =& xarMLSLoadLocaleData();

    // Initialize vars
    $picpath = 'modules/stats/xarimages';
    $barlen  = 230;

    // Get the stats-module installation date a.k.a. start-of-stats-collecting
    $startdate = xarModGetVar('stats','startdate');

    // API function to get the total hits
    $hits['total'] = xarModAPIFunc('stats',
                                   'user',
                                   'gettotalhits');

    // API function to get the hits of today
    $today = explode('-', gmdate('Y-n-j'));
    $hits['today'] = xarModAPIFunc('stats',
                                   'user',
                                   'getday',
                                   array('year'  => $today[0],
                                         'month' => $today[1],
                                         'day'   => $today[2]));

    // API function to get the hits of yesterday (respects DST jumps)
    $yesterday = explode('-', gmdate(('Y-n-j'), gmmktime(0,0,0,$today[1],$today[2]-1,$today[0])));
    $hits['yesterday'] = xarModAPIFunc('stats',
                                       'user',
                                       'getday',
                                       array('year'  => $yesterday[0],
                                             'month' => $yesterday[1],
                                             'day'   => $yesterday[2]));
    unset($today, $yesterday);

    // get hits of the last seven days
    list($l7data, $l7sum, $l7max) = xarModAPIFunc('stats',
                                                  'user',
                                                  'getlast7days');
    $last7days = array();
    foreach ($l7data as $key => $val) {
    //TODO: use dateformat-medium from locales file here
        $unformatted = gmmktime(0,0,0,$val['month'],$val['day'],$val['year']);
        $last7days[$key] = array('unformatted' => $unformatted,
                                 'link'        => xarModURL('stats','user','main',
                                                            array('year'  => $val['year'],
                                                                  'month' => $val['month'],
                                                                  'day'   => $val['day'])),
                                 'abs'         => $val['hits'],
                                 'rel'         => sprintf('%01.2f',(100*$val['hits']/$l7sum)),
                                 'wid'         => round($barlen*$val['hits']/$l7max));
    }
    unset($l7data, $l7sum, $l7max, $unformatted);

    $topday = array();
    // API function to get the best days figure
    list($bestday, $topday['mosthits']) = xarModAPIFunc('stats',
                                                        'user',
                                                        'gettopday',
                                                        array('type' => 'best',
                                                              'year' => $year,
                                                              'month' => $month));
    $topday['best'] = gmmktime(0,0,0,$bestday['month'],$bestday['day'],$bestday['year']);
    unset($bestday);

    // API function to get the worst days figure
    list($worstday, $topday['leasthits']) = xarModAPIFunc('stats',
                                                          'user',
                                                          'gettopday',
                                                          array('type' => 'worst',
                                                                'year' => $year,
                                                                'month' => $month));
    $topday['worst'] = gmmktime(0,0,0,$worstday['month'],$worstday['day'],$worstday['year']);
    unset($worstday);

    // API function to get the hits per year
    list($pydata, $pysum, $pymax) = xarModAPIFunc('stats',
                                                  'user',
                                                  'getperyear');
    $peryear = array();
    foreach($pydata as $key => $val) {
        $pyyear = sprintf('%04d', $val['year']);
        $peryear[$key] = array('name' => $pyyear,
                               'abs'  => $val['hits'],
                               'rel'  => sprintf('%01.2f',(100*$val['hits']/$pysum)),
                               'wid'  => round($barlen*$val['hits']/$pymax));
    }
    unset($pydata, $pysum, $pymax, $pyyear);

    $topyear = xarModAPIFunc('stats',
                             'user',
                             'gettopyear',
                             $peryear);

    // get overall hits per month. same month of different years may be added!
    list($pmdata, $pmsum, $pmmax) = xarModAPIFunc('stats',
                                                  'user',
                                                  'getpermonth',
                                                  array('year' => $year));
    $permonth = array();
    foreach ($pmdata as $key => $val) {
        $localeIndex = '/dateSymbols/months/'.$val['month'].'/full';
        $permonth[$key] = array('name' => $localeData[$localeIndex],
                                'num' => $key,
                                'abs'  => $val['hits'],
                                'rel'  => sprintf('%01.2f',(100*$val['hits']/$pmsum)),
                                'wid'  => round($barlen*$val['hits']/$pmmax));
    }
    unset($pmdata, $pmsum, $pmmax, $localeIndex);

    $topmonth = xarModAPIFunc('stats',
                              'user',
                              'gettopmonth',
                              $permonth);

    // API function to get the hits of the week
    list($pwddata, $pwdsum, $pwdmax) = xarModAPIFunc('stats',
                                                     'user',
                                                     'getperweekday',
                                                     array('year' => $year,
                                                           'month' => $month));
    $perweekday = array();
    foreach ($pwddata as $key => $val) {
        $localeIndex = '/dateSymbols/weekdays/'.++$val['weekday'].'/full';
        $perweekday[$key] = array('name' => $localeData[$localeIndex],
                                  'abs'  => $val['hits'],
                                  'rel'  => sprintf('%01.2f',(100*$val['hits']/$pwdsum)),
                                  'wid'  => round($barlen*$val['hits']/$pwdmax));
    }
    unset($pwddata, $pwdsum, $pwdmax, $localeIndex);

    $topweekday = xarModAPIFunc('stats',
                                'user',
                                'gettopweekday',
                                $perweekday);

    // API function to get the hits per day
    $perday = array();
    if (!empty($year) && !empty($month)) {
        list($pddata, $pdsum, $pdmax) = xarModAPIFunc('stats',
                                                      'user',
                                                      'getperday',
                                                      array('year' => $year,
                                                            'month' => $month));
        foreach($pddata as $key => $val) {
            $pdday = sprintf('%02d', $val['day']);
            $perday[$key] = array('name' => $pdday,
                                  'num'  => $key,
                                  'abs'  => $val['hits'],
                                  'rel'  => sprintf('%01.2f',(100*$val['hits']/$pdsum)),
                                  'wid'  => round($barlen*$val['hits']/$pdmax));
        }
        unset($pddata, $pdsum, $pdmax, $pdday);
    }

    // API function to get the hits of the week
    list($phdata, $phsum, $phmax) = xarModAPIFunc('stats',
                                                  'user',
                                                  'getperhour',
                                                  array('year'  => $year,
                                                        'month' => $month,
                                                        'day'   => $day));
    $perhour = array();
    foreach($phdata as $key => $val) {
        $phhour = sprintf('%02d', $val['hour']);
        $perhour[$key] = array('name' => $phhour.':00 - '.$phhour.':59',
                               'abs'  => $val['hits'],
                               'rel'  => sprintf('%01.2f',(100*$val['hits']/$phsum)),
                               'wid'  => round($barlen*$val['hits']/$phmax));
    }
    unset($phdata, $phsum, $phmax, $phhour);

    $tophour = xarModAPIFunc('stats',
                             'user',
                             'gettophour',
                             $perhour);

    // get the hits by browsers
    $top10 = true;
    $args = compact('top10', 'picpath', 'barlen', 'year', 'month', 'day');
    extract(xarModAPIFunc('stats','user','get_browser_data',$args));

    // get the hits by operating system
    extract(xarModAPIFunc('stats','user','get_os_data', $args));

    // get misc stats
    $misc = xarModAPIFunc('stats',
                          'user',
                          'getmisc');

    // arrange return values
    $data = compact('startdate', 'hits', 'topday', 'browsers', 'os',
                    'perhour', 'perweekday', 'last7days', 'permonth', 'topmonth',
                    'topweekday', 'tophour', 'misc', 'picpath', 'peryear', 'topyear',
                    'perday', 'year', 'month', 'day');

    // return data to BL template
    return $data;
}

?>
