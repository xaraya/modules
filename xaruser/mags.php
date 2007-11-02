<?php

/**
 * Overview of available magazines, or a single magazine.
 *
 * @param mid integer Magazine ID
 * @param mag string Magazine reference
 *
 * @todo Support startnum
 * @todo Support pager
 *
 */

function mag_user_mags($args)
{
    extract($args);
    
    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,default_numitems_mags,max_numitems_mags'
        )
    ));

    // Fetch the magazine ID or reference.
    // The short URL encoding/decoding may have converted these to a reference -- ?
    xarVarFetch('mid', 'id', $mid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('mag', 'str:0:30', $ref, '', XARVAR_NOT_REQUIRED);

    // Pager parameters
    xarVarFetch('startnum', 'int:1', $startnum, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('numitems', 'int:1:' . $max_numitems_mags, $numitems, $default_numitems_mags, XARVAR_NOT_REQUIRED);

    // mid overrides mag
    if (!empty($ref) && !empty($mid)) $ref = '';

    // Query parameters for the magazine.
    $mag_select = array();

    // startnum/numitems
    if (!empty($startnum)) $mag_select['startnum'] = $startnum;
    if (!empty($numitems)) $mag_select['numitems'] = $numitems;

    // If we have an ID or reference, then attempt to fetch the magazine.
    // Fetch all magazines viewable from the appropriate module.
    if (!empty($mid)) $mag_select['mid'] = $mid;
    if (!empty($mag)) $mag_select['ref'] = $ref;

    // Only active magazines (we have admin screens for others).
    $mag_select['status'] = 'ACTIVE';

    // Fetch the selected magazines.
    $mags = xarModAPIfunc($module, 'user', 'getmags', $mag_select);

    // Weed out any magazines we don't have privilege to look at.
    // CHECKME: is there any reason why this could not be done in the API?
    if (!empty($mags)) {
        foreach($mags as $key => $check_mag) {
            // If no overview privilege, then remove it from the list.
            if (!xarSecurityCheck('OverviewMag', 0, 'Mag', "$check_mag[mid]")) unset($mags[$key]);
        }
    }

    // Get the mag ID if there is only one.
    // It does not matter whether this happened by selecting a single magazine,
    // or because there is only one magazine.
    if (count($mags) == 1) {
        $mag = reset($mags);
        $mid = $mag['mid'];
    }

    // If there are no magazines, then ensure the mid is not set.
    if (empty($mags)) $mid = 0;

    $return = array(
        'mags' => $mags,
        'mid' => $mid,
    );

    // Set context information for custom templates and blocks.
    $return['function'] = 'mags';
    xarModAPIfunc($module, 'user', 'cachevalues', $return);

    return $return;
}

?>