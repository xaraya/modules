<?php
/**
 * Gets the number of unique visitors between two dates
 * 
 * @author Chris "Alley" van de Steeg
 * @param  $args['start'] (Optional) The date to start counting
 * @param  $args['end'] (Optional) The date to stop counting
 * @returns int
 * @return number of visitors between $start and $end
 */
function opentracker_userapi_get_visitors($args)
{
    extract($args);
    if (!isset($start))
        $start = false;
    if (!isset($end))
        $end = false;
    if (!isset($interval))
        $interval = false;
    return xarOpenTracker::get(
        array(
          'client_id' => 1,
          'api_call'  => 'visitors',
          'start'     => $start,
          'end'       => $end,
          'interval'  => $interval
        )
      );
}
?>