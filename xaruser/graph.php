<?php
function opentracker_user_graph($args) 
{
    
    $time = time();

    if (!xarVarFetch('start', 'int:1:', $start,  mktime( 0, 0, 0, date('m', $time),   1, date('Y', $time)), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end', 'int:1:', $end,   mktime( 0, 0, 0, date('m', $time)+1, 0, date('Y', $time)), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('interval', 'str', $interval,  'day', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('width', 'int:1:', $width,  640, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('height', 'int:1:', $height,  480, XARVAR_NOT_REQUIRED)) return;
    
    phpOpenTracker::plot(
      array(
        'api_call'  => 'access_statistics',
        'client_id' => 1,
        'start'     => $start,
        'end'       => $end,
        'interval'  => $interval,
        'width'     => $width,
        'height'    => $height
      )
    );
    //TODO: Is this the right way? I guess not.. but the template shouldn't be parsed from here on..
    die();
}
?>
