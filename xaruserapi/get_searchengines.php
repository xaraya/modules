<?php
/**
 * Gets the search engine data
 * 
 * @author Chris "Alley" van de Steeg
 * @param  $args['start'] (Optional) The date to start counting
 * @param  $args['end'] (Optional) The date to stop counting
 * @param  $args['limit'] (Optional) The maximum number of items to return
 * @returns array
 * @return 
 *        $data['search_engines'] ::
 *            $data['search_engines']['rank'],
 *             $data['search_engines']['count'],
 *             $data['search_engines']['percent'],
 *             $data['search_engines']['string']
 *        $data['search_keywords'],
 *            $data['search_keywords']['rank'],
 *             $data['search_keywords']['count'],
 *             $data['search_keywords']['percent'],
 *             $data['search_keywords']['string']
 *        $data['total_search_engines'],
 *        $data['total_search_keywords']
 */
function opentracker_userapi_get_searchengines($args)
{
    extract ($args);
  $searchEngines       = array();
  $searchKeywords      = array();
  $totalSearchEngines  = 0;
  $totalSearchKeywords = 0;
    if (!isset($start))
        $start = false;
    if (!isset($end))
        $end = false;

  //if (phpOpenTracker_API::pluginLoaded('search_engines')) {
    $result = phpOpenTracker::get(
      array(
          'client_id' => 1,
          'api_call'  => 'search_engines',
          'what'      => 'top_search_engines',
          'start'     => $start,
          'end'       => $end,
          'limit'     => $limit
      )
    );

    $totalSearchEngines = $result['unique_items'];
    for ($i = 0; $i < sizeof($result['top_items']); $i++) {
      // Fill in item template variables
      $searchEngines[] = 
        array(
          'rank' => $i + 1,
          'count' => $result['top_items'][$i]['count'],
          'percent' => $result['top_items'][$i]['percent'],
          'string' => $result['top_items'][$i]['string']
        );
    }

    $result = phpOpenTracker::get(
      array(
          'client_id' => 1,
          'api_call'  => 'search_engines',
          'what'      => 'top_search_keywords',
          'start'     => $start,
          'end'       => $end,
          'limit'     => $limit
      )
    );

    $totalSearchKeywords = $result['unique_items'];
    for ($i = 0; $i < sizeof($result['top_items']); $i++) {
      $searchKeywords[] = 
        array(
          'rank' => $i + 1,
          'count' => $result['top_items'][$i]['count'],
          'percent' => $result['top_items'][$i]['percent'],
          'string' => $result['top_items'][$i]['string']
        );
    }
  //}

  return array(
    $searchEngines,
    $searchKeywords,
    $totalSearchEngines,
    $totalSearchKeywords
  );
}
?>