<?php
/**
 * display the summary report
 * 
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args['page'] The report page
 */
function opentracker_user_main($args)
{ 
	if(!xarSecurityCheck('OverviewOpentracker')) return;
	
	if (ini_get('safe_mode') <> 1)
		@set_time_limit(0);

	if (!xarVarFetch('page', 'str', $page, 'access_statistics', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('day', 'int:1:', $day,  date('j'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('month', 'int:1:', $month,  date('n'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('year', 'int:1:', $year,  date('Y'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('period', 'str', $period,  'total', XARVAR_NOT_REQUIRED)) return;
	
    extract($args); 
	
    $data['period'] = $period;
    $monthNames = array(
	  xarML('January'),
	  xarML('February'),
	  xarML('March'),
	  xarML('April'),
	  xarML('May'),
	  xarML('June'),
	  xarML('July'),
	  xarML('August'),
	  xarML('September'),
	  xarML('October'),
	  xarML('November'),
	  xarML('December')
	);
	
	$limit = 10;
	$clientID = 1;
    
	$dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables(); 
	
	// Prevent caching
	header('Expires: Sat, 22 Apr 1978 02:19:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	$buffer = '';
	$time   = time();
	$data = array();
	$data['limit'] = $limit;
	
switch ($period) {
  case 'month': {
    $start = mktime(0,   0,  0, $month, 1, $year);
    $end   = mktime(23, 59, 59, $month, date('t', $start), $year);
  }
  break;

  case 'day': {
    $start = mktime(0,   0,  0, $month, $day, $year);
    $end   = mktime(23, 59, 59, $month, $day, $year);
  }
  break;
  
  default: 
    $start = false;
    $end   = false;
    break;
}
	
	$data['totalPageImpressions'] = xarModAPIFunc('opentracker', 'user', 'get_page_impressions', array ('start' => $start, 'end' => $end));
	$data['totalVisitors'] = xarModAPIFunc('opentracker', 'user', 'get_visitors', array ('start' => $start, 'end' => $end));
	
	$data['top'] = xarModAPIFunc('opentracker', 'user', 'get_top_items', array ('start' => $start, 'end' => $end, 'limit' => $limit));
	
	list(
		$data['top']['search_engines']['top_items'],
		$data['top']['search_keywords']['top_items'],
		$data['top']['search_engines']['unique_items'],
		$data['top']['search_keywords']['unique_items'],
	) = xarModAPIFunc('opentracker', 'user', 'get_searchengines',
						array(
							'start' => $start,
							'end' => $end,
							'limit' => $limit
						));
switch ($period)
{
	case 'total' :
    $query =       
	   	sprintf(
	        'SELECT MIN(timestamp) AS first_access,
	                MAX(timestamp) AS last_access
	         FROM %s
	         WHERE client_id = %d',
	
	        $xartable['accesslog'],
	        xarVarPrepForStore($clientID)
		);

    $result = &$dbconn->Execute($query); 
    if (!$result) return; 
	list($firstAccess, $lastAccess) = $result->fields; 

    $month = $firstMonth = date('n', $firstAccess);
    $year  = $firstYear  = date('Y', $firstAccess);
    $lastMonth           = date('n', $lastAccess);
    $lastYear            = date('Y', $lastAccess);
    
    $data['statisticitems'] = array();
    // Loop through months from first to last access
    while ($year <= $lastYear) {
      // Get start and end timestamp for this month
      $start = mktime( 0,  0,  0, $month, 1, $year);
      $end   = mktime(23, 59, 59, $month, date('t', $start), $year);

      // Query Page Impressions for this client and month
      $pi = xarModAPIFunc('opentracker', 'user', 'get_page_impressions', array ('start' => $start, 'end' => $end));
      
      // Query Visitors for this client and month
      $visitors = xarModAPIFunc('opentracker', 'user', 'get_visitors', array ('start' => $start, 'end' => $end));

      $data['statisticitems'][] = array(
          'pi_number' => $pi,
          'pi_percent' => ($data['totalPageImpressions'] > 0) ? number_format(((100 * $pi) / $data['totalPageImpressions']), 2) : 0,
          'visitors_number' => $visitors,
          'visitors_percent' => ($data['totalVisitors'] > 0) ? number_format(((100 * $visitors) / $data['totalVisitors']), 2) : 0,
          'href' => xarModURL('opentracker', 'user', 'main', array(
          																'month' => $month,
          																'year' => $year,
          																'period' => 'month'
          															)
          						),
          'displayname' => $monthNames[($month-1)]
      );
      
      if ($month == $lastMonth && $year == $lastYear) {
        break;
      }

      if ($month < 12) {
        $month++;
      } else {
        $month = 1;
        $year++;
      }
    }
    //
    $data['first'] = date('d-M-Y', isset($firstAccess) ? $firstAccess : $time);
    $data['last'] = date('d-M-Y', isset($lastAccess)  ? $lastAccess  : $time);
    $data['graphurl'] = xarModUrl('opentracker', 'user', 'graph',
    	array(
			'start' => mktime(0, 0, 0, $lastMonth, 1, $lastYear -1),
			'end' => $lastAccess,
			'interval' => 'month',
			'width' => 700,
			'height' => 260
    	));
    $data['item_statistics_title'] = xarML('Monthly Statistics');
	$data['maintitle'] = xarML("Total Statistics (#(1) to #(2))", $data['first'], $data['last']);
	break;
	
	case 'month':
    // Query Page Impressions for this client and each day of this month
    $pi = xarModAPIFunc('opentracker', 'user', 'get_page_impressions',
    		array ('start' => $start, 'end' => $end, 'interval' => 86400));

    // Query visitors for this client and each day of this month
    $visitors = xarModAPIFunc('opentracker', 'user', 'get_visitors',
    		array ('start' => $start, 'end' => $end, 'interval' => 86400));

    $data['statisticitems'] = array();
    // Loop through days
    for ($i = 0; $i < sizeof($pi); $i++) {
      // Get daily_statistics template
      $data['statisticitems'][] = array(
          'pi_number' => $pi[$i]['value'],
          'pi_percent' => $data['totalPageImpressions'] ? number_format(((100 * $pi[$i]['value']) / $data['totalPageImpressions']), 2) : '0',
          'visitors_number' => $visitors[$i]['value'],
          'visitors_percent' => $data['totalVisitors'] ? number_format(((100 * $visitors[$i]['value']) / $data['totalVisitors']), 2) : '0',
          'href' => xarModURL('opentracker', 'user', 'main', array(
          																'month' => $month,
          																'year' => $year,
          																'day' => $i + 1,
          																'period' => 'day'
          															)
          						),
          'displayname' => $i +1
      );
    }
    $data['graphurl'] = xarModUrl('opentracker', 'user', 'graph',
    	array(
			'start' => $start,
			'end' => $end,
			'interval' => 'day',
			'width' => 700,
			'height' => 260
    	));
    $data['item_statistics_title'] = xarML('Daily Statistics');
    $data['maintitle'] = xarML("Monthly Statistics for #(1) #(2)", $monthNames[$month], $year);
	break;
	
  case 'day':
    $pi = xarModAPIFunc('opentracker', 'user', 'get_page_impressions',
    		array ('start' => $start, 'end' => $end, 'interval' => 3600));

    // Query visitors for this client and each day of this month
    $visitors = xarModAPIFunc('opentracker', 'user', 'get_visitors',
    		array ('start' => $start, 'end' => $end, 'interval' => 3600));
    
   	$data['statisticitems'] = array();
    // Loop through hours
    for ($i = 0; $i < sizeof($pi); $i++) {
      $data['statisticitems'][] = array(
          'pi_number' => $pi[$i]['value'],
          'pi_percent' => $data['totalPageImpressions'] ? number_format(((100 * $pi[$i]['value']) / $data['totalPageImpressions']), 2) : '0',
          'visitors_number' => $visitors[$i]['value'],
          'visitors_percent' => $data['totalVisitors'] ? number_format(((100 * $visitors[$i]['value']) / $data['totalVisitors']), 2) : '0',
          'href' => false,
          'displayname' => sprintf('%02d:00 - %02d:00', $i, (($i + 1) < 24) ? ($i + 1) : 0 )
      );
    }
    $data['graphurl'] = xarModUrl('opentracker', 'user', 'graph',
    	array(
			'start' => $start,
			'end' => $end,
			'interval' => 'hour',
			'width' => 700,
			'height' => 260
    	));
    $data['item_statistics_title'] = xarML('Hourly Statistics');
    $data['maintitle'] = xarML("Daily Statistics for #(1). #(2) #(3)", $day, $monthNames[$month], $year);
  break;
	
}
	$data['top']['pages']['title'] = xarML('Top #(1) of #(2) Total Pages', $limit, $data['top']['pages']['unique_items']);
	$data['top']['pages']['subtitle1'] = xarML('Page impressions');
	$data['top']['pages']['subtitle2'] = xarML('Page');
	$data['top']['mods']['title'] = xarML('Top #(1) of #(2) Total Xaraya modules (unused mods are excluded)', $limit, $data['top']['mods']['unique_items']);
	$data['top']['mods']['subtitle1'] = xarML('Page impressions');
	$data['top']['mods']['subtitle2'] = xarML('Xaraya module');
	$data['top']['entry_pages']['title'] = xarML('Top #(1) of #(2) Total Entry Pages', $limit, $data['top']['entry_pages']['unique_items']);
	$data['top']['entry_pages']['subtitle1'] = xarML('Visitors entered here');
	$data['top']['entry_pages']['subtitle2'] = xarML('Page');
	$data['top']['exit_pages']['title'] = xarML('Top #(1) of #(2) Exit Pages', $limit, $data['top']['exit_pages']['unique_items']);
	$data['top']['exit_pages']['subtitle1'] = xarML('Visitors exited here');
	$data['top']['exit_pages']['subtitle2'] = xarML('Page');
	$data['top']['exit_targets']['title'] = xarML('Top #(1) of #(2) Exit Targets', $limit, $data['top']['exit_targets']['unique_items']);
	$data['top']['exit_targets']['subtitle1'] = xarML('Clicks');
	$data['top']['exit_targets']['subtitle2'] = xarML('Target URL');
	$data['top']['hosts']['title'] = xarML('Top #(1) of #(2) Total Hosts', $limit, $data['top']['hosts']['unique_items']);
	$data['top']['hosts']['subtitle1'] = xarML('Visitors from there');
	$data['top']['hosts']['subtitle2'] = xarML('Host');
	$data['top']['referers']['title'] = xarML('Top #(1) of #(2) Total Referers', $limit, $data['top']['referers']['unique_items']);
	$data['top']['referers']['subtitle1'] = xarML('Visitors refered here');
	$data['top']['referers']['subtitle2'] = xarML('Referer');
	$data['top']['operating_systems']['title'] = xarML('Top #(1) of #(2) Operating Systems', $limit, $data['top']['operating_systems']['unique_items']);
	$data['top']['operating_systems']['subtitle1'] = 
		$data['top']['search_engines']['subtitle1'] =  
		$data['top']['user_agents']['subtitle1'] = xarML('Visitors used this');
	$data['top']['operating_systems']['subtitle2'] = xarML('Operating system');
	$data['top']['user_agents']['title'] = xarML('Top #(1) of #(2) User Agents', $limit, $data['top']['user_agents']['unique_items']);
	$data['top']['user_agents']['subtitle2'] = xarML('User agent');
	$data['top']['search_keywords']['title'] = xarML('Top #(1) of #(2) Search keywords', $limit, $data['top']['search_keywords']['unique_items']);
	$data['top']['search_keywords']['subtitle1'] = xarML('Visitors searched for this');
	$data['top']['search_keywords']['subtitle2'] = xarML('Search Keyword(s)');
	$data['top']['search_engines']['title'] = xarML('Top #(1) of #(2) Search engines', $limit, $data['top']['search_engines']['unique_items']);
	$data['top']['search_engines']['subtitle2'] = xarML('Search Engine');
	
	$data['url'] = xarServerGetCurrentURL();
  ///

    xarTplSetPageTitle(xarVarPrepForDisplay($page)); 
    return $data; 
} 

function top($clientID, $limit, $start = false, $end = false) {
  $batchKeys = array(
    'pages',
    'entry_pages',
    'exit_pages',
    'exit_targets',
    'hosts',
    'referers',
    'operating_systems',
    'user_agents'
  );

  $batchWhat = array(
    'document',
    'entry_document',
    'exit_document',
    'exit_target',
    'host',
    'referer',
    'operating_system',
    'user_agent'
  );

  $batchResult = array();

  // Loop through $batchKeys / $batchWhat
  for ($i = 0; $i < sizeof($batchKeys); $i++) {
    // Query Top <$limit> items of category <$batchWhat[$i]>
    $result = xarOpenTracker::get(
      array(
        'client_id' => $clientID,
        'api_call'  => 'top',
        'what'      => $batchWhat[$i],
        'start'     => $start,
        'end'       => $end,
        'limit'     => $limit
      )
    );

    for ($j = 0; $j < sizeof($result['top_items']); $j++) {
      // Get item template
      $item = array();

      // Fill in item template variables
      $item['rank'] = $j + 1;
      $item['count'] = $result['top_items'][$j]['count'];
      $item['percent'] = $result['top_items'][$j]['percent'];
      $item['string'] = $result['top_items'][$j]['string'];

      if (!isset($batchResult[$batchKeys[$i]]['top_items'])) {
        $batchResult[$batchKeys[$i]]['top_items'] = '';
      }

      $batchResult[$batchKeys[$i]]['top_items'][] = $item;
    }

    $batchResult[$batchKeys[$i]]['unique_items'] = $result['unique_items'];
  }

  return $batchResult;
}

?>
