<?php
/**
 * Gets the top ranked items for all possible keys
 * 
 * @author Chris "Alley" van de Steeg
 * @param  $args['start'] (Optional) The date to start counting
 * @param  $args['end'] (Optional) The date to stop counting
 * @param  $args['limit'] (Optional) The maximum number of items to return
 * @returns array
 */
function opentracker_userapi_get_top_items($args)
{
  extract($args);
  $batchKeys = array(
    'referers',
    'pages',
    'mods',
    'entry_pages',
    'exit_pages',
    'exit_targets',
    'hosts',
    'operating_systems',
    'user_agents'
  );

  $batchWhat = array(
    'referer',
    'document',
    'mods',
    'entry_document',
    'exit_document',
    'exit_target',
    'host',
    'operating_system',
    'user_agent'
  );

  $batchResult = array();

  // Loop through $batchKeys / $batchWhat
  for ($i = 0; $i < sizeof($batchKeys); $i++) {
    // Query Top <$limit> items of category <$batchWhat[$i]>
    if ($batchWhat[$i] == 'mods')
    {
        $result = xarOpenTracker::get(
              array(
                'client_id' => 1,
                'api_call'  => 'xarmod_top',
                'start'     => $start,
                'end'       => $end,
                'limit'     => $limit
              )
            );
    }
    else 
    {
        $result = xarOpenTracker::get(
          array(
            'client_id' => 1,
            'api_call'  => 'top',
            'what'      => $batchWhat[$i],
            'start'     => $start,
            'end'       => $end,
            'limit'     => $limit
          )
        );
    }
    
    $batchResult[$batchKeys[$i]]['top_items'] = array();
    for ($j = 0; $j < sizeof($result['top_items']); $j++) {
      // Get item template
      $item = array();

      // Fill in item template variables
      $item['rank'] = $j + 1;
      $item['count'] = $result['top_items'][$j]['count'];
      $item['percent'] = $result['top_items'][$j]['percent'];
      $item['string'] = $result['top_items'][$j]['string'];

      $batchResult[$batchKeys[$i]]['top_items'][] = $item;
    }

    $batchResult[$batchKeys[$i]]['unique_items'] = $result['unique_items'];
  }

  return $batchResult;
}

?>
