<?php

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
	
    // load the locale data
    $localeData =& xarMLSLoadLocaleData();

    // Initialize vars
    $picpath = 'modules/stats/xarimages';
	$barlen  = 230;
   
    // Get the stats-module installation date a.k.a. start-of-stats-collecting
    $startdate = xarLocaleFormatDate('%D', xarModGetVar('stats','startdate'));
    
    // API function to get the best days figure
	list($bestday, $day['mosthits']) = xarModAPIFunc('stats',
													 'user',
													 'gettopday',
													 array('type' => 'best'));
    $day['best'] = xarLocaleFormatDate('%D',
                       gmmktime(0,0,0,$bestday['month'],$bestday['day'],$bestday['year']));
	unset($bestday);

    // API function to get the worst days figure
	list($worstday, $day['leasthits']) = xarModAPIFunc('stats',
													   'user',
													   'gettopday',
													   array('type' => 'worst'));
    $day['worst'] = xarLocaleFormatDate('%D',
                       gmmktime(0,0,0,$worstday['month'],$worstday['day'],$worstday['year']));
	unset($worstday);

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

    // get the hits by browsers
	$top10 = true;
	$args = compact('top10', 'picpath', 'barlen');
	extract(xarModAPIFunc('stats','user','get_browser_data',$args));

    // get the hits by operating system
	extract(xarModAPIFunc('stats','user','get_os_data', $args));

    // API function to get the hits of the week
    list($phdata, $phsum, $phmax) = xarModAPIFunc('stats',
												  'user',
												  'getperhour');
	$perhour = array();
	foreach($phdata as $key => $val) {
		$phhour = sprintf('%02d', $val['hour']);
		$perhour[$key] = array('name' => $phhour.':00 - '.$phhour.':59',
							   'abs'  => $val['hits'],
							   'rel'  => sprintf('%01.2f',(100*$val['hits']/$phsum)),
							   'wid'  => round($barlen*$val['hits']/$phmax));
	}
	unset($phdata, $phsum, $phmax, $phhour);

    $hour = xarModAPIFunc('stats',
						  'user',
						  'gettophour',
						  $perhour);
    
	// API function to get the hits of the week
    //TODO: add to the api an argument of what year/month/week the stats are. if empty, cumulate all
	list($pwddata, $pwdsum, $pwdmax) = xarModAPIFunc('stats',
													 'user',
													 'getperweekday');
	$perweekday = array();
	foreach ($pwddata as $key => $val) {
        $localeIndex = '/dateSymbols/weekdays/'.++$val['weekday'].'/full';
        $perweekday[$key] = array('name' => $localeData[$localeIndex],        
								  'abs'  => $val['hits'],
								  'rel'  => sprintf('%01.2f',(100*$val['hits']/$pwdsum)),
								  'wid'  => round($barlen*$val['hits']/$pwdmax));
    }
	unset($pwddata, $pwdsum, $pwdmax, $localeIndex);

	$weekday = xarModAPIFunc('stats',
							 'user',
							 'gettopweekday',
							 $perweekday);

    // get hits of the last seven days
	list($l7data, $l7sum, $l7max) = xarModAPIFunc('stats',
												  'user',
												  'getlast7days');
    $last7days = array();
    foreach ($l7data as $key => $val) {
//TODO: use dateformat-medium from locales file here
        $formatted = xarLocaleFormatDate('%D',
                         gmmktime(0,0,0,$val['month'],$val['day'],$val['year']));        
        $last7days[$key] = array('formated' => $formatted,        
								 'abs'      => $val['hits'],
								 'rel'      => sprintf('%01.2f',(100*$val['hits']/$l7sum)),
								 'wid'      => round($barlen*$val['hits']/$l7max));
    }
	unset($l7data, $l7sum, $l7max, $formatted);

	// get overall hits per month. same month of different years are added!
	//TODO: Put the searched for year as input var to the api func. if empty, cumulate all years
	list($pmdata, $pmsum, $pmmax) = xarModAPIFunc('stats',
												  'user',
												  'getpermonth');
    $permonth = array();
    foreach ($pmdata as $key => $val) {
        $localeIndex = '/dateSymbols/months/'.$val['month'].'/full';
        $permonth[$key] = array('name' => $localeData[$localeIndex],        
								'abs'  => $val['hits'],
								'rel'  => sprintf('%01.2f',(100*$val['hits']/$pmsum)),
								'wid'  => round($barlen*$val['hits']/$pmmax));
    }
	unset($pmdata, $pmsum, $pmmax, $barlen, $localeIndex);

	// get misc stats
	$misc = xarModAPIFunc('stats',
						  'user',
						  'getmisc');

	// arrange return values
    $data = compact('startdate', 'hits', 'day', 'browsers', 'os', 'perhour', 'perweekday',
					'last7days', 'permonth', 'weekday', 'hour', 'misc', 'picpath');

	// return data to BL template
	return $data;
}

?>