<?php

/**
 * Get the current magazine.
 * Reads from $args and page parameters to get the current selected magazine.
 * Takes into account whether the magazine is available in the module context
 * (i.e. when called up from the 'mag' or 'xarpages' module).
 * If no magazine is selected, and there is only one magazine available, then
 * this is assumed to be the selected magazine.
 * This is an unusual API in that it reads page (HTTP) parameters, but a pass-in
 * 'mid' will always take precedence.
 *
 * @param mid integer Magazine ID (optional)
 * @param showin array List of places the magazine can be shown in; options MAG, XARPAGES and ALL; defaults according to where page is called from
 * @param status_group string PUBLISHED or DRAFT; sets all statuses appropriately
 * @return array 'mag': the magazine decord; 'mid': the magazine ID; or empty array if not found
 *
 */

function mag_userapi_currentmag($args)
{
    $return = array();
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module'
        )
    ));

    // Fetch the magazine ID or reference.
    // The short URL encoding/decoding may have converted these to a reference.
    xarVarFetch('mid', 'id', $mid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('mag', 'str:0:30', $mag_ref, '', XARVAR_NOT_REQUIRED);

    // Default the status to active.
    // Can override this by setting to '' or array() so inactive
    // mags can be viewed.
    if (!empty($status_group)) {
        if ($status_group == 'PUBLISHED') {
            $mag_status = array('ACTIVE');
        } elseif ($status_group == 'DRAFT') {
            $mag_status = array();
        }
    }
    if (!isset($mag_status)) $mag_status = array('ACTIVE');

    // mid overrides mag ref
    if (!empty($mag_ref) && !empty($mid)) $mag_ref = '';

    // Query parameters for the magazine.
    $mag_select = array();

    // If we are fetching from the mag module, then select
    // only the magazines that can be viewed direct from the module.
    // If we are coming from the xarpages module, then the mag ID should already
    // have been set.
    // Override this by setting it to '' or array()
    if (!isset($showin)) {
        $xarpages_pid = xarVarGetCached($module, 'pid');
        if (!empty($xarpages_pid)) {
            $mag_select['showin'] = array('ALL', 'XARPAGES');
        } else {
            $mag_select['showin'] = array('ALL', 'MAG');
        }
    }

    // If we have an ID or reference, then attempt to fetch the magazine.
    // Fetch all magazines viewable from the appropriate module.
    if (!empty($mid)) $mag_select['mid'] = $mid;
    if (!empty($mag_ref)) $mag_select['ref'] = $mag_ref;

    // Only active magazines.
    $mag_select['status'] = $mag_status;

    // Fetch a maximum of two magazines, since we only want one,
    // with a check whether we are selecting too many.
    $mag_select['numitems'] = 2;

    // Fetch the selected magazines.
    $mags = xarModAPIfunc($module, 'user', 'getmags', $mag_select);

    // Get the mag ID if there is only one.
    // It does not matter whether this happened by selecting a single magazine,
    // or because there is only one magazine.
    if (count($mags) == 1) {
        $mag = reset($mags);
        $mid = $mag['mid'];

        $return = array(
            'mag' => $mag,
            'mid' => $mid,
        );
    }

    return $return;
}

?>