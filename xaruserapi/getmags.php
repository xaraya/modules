<?php

/**
 * Get magazines.
 *
 * @param mid integer Magazine ID
 * @param ref string Magazine reference
 * @param startnum integer Start number
 * @param numitems integer Start number
 *
 * @todo Support startnum and numitems
 */

function mag_userapi_getmags($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_mags,'
            . 'sort_default_mags,image_mag_logo_vpath,default_numitems_mags'
        )
    ));

    // Only used for some text escaping methods.
    $dbconn =& xarDBGetConn();

    // Default sort order
    if (!isset($sort)) {
        $sort = $sort_default_mags;
    }

    // Initialise return value.
    $return = array();

    // Search criteria.
    $params = array (
        'module' => $module,
        'itemtype' => $itemtype_mags,
        'sort' => $sort,
    );

    // startnum
    if (!empty($startnum) && is_numeric($startnum)) {
        $params['startnum'] = (integer)$startnum;
    }

    // numitems
    if (!empty($numitems) && is_numeric($numitems)) {
        $params['numitems'] = (integer)$numitems;
    }

    $where = array();

    // Status
    if (!empty($status)) {
        if (is_string($status)) $status = array($status);
        $where[] = "status in ('" . implode("','", $status) . "')";
    }

    // Showin
    if (!empty($showin)) {
        if (is_string($showin)) $status = array($showin);
        $where[] = "showin in ('" . implode("','", $showin) . "')";
    }

    // Magazine ID
    if (!empty($mid) && is_numeric($mid)) {
        $where[] = 'mid eq ' . (integer)$mid;
    }

    // Magazine ref
    if (!empty($ref) && is_string($ref)) {
        $where[] = 'ref eq ' . $dbconn->qstr((string)$ref);
    }

    if (!empty($where)) $params['where'] = implode(' AND ', $where);

    // Fetch the matching magazines.
    $mags = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

    if (!empty($mags)) {
        foreach($mags as $mag) {
            // Add some additional useful information into the list of magazines.
            // Substitution variables are used so that path can be varied as required.
            $mag['logo_path'] = $issue['cover_img_path'] = xarModAPIfunc(
                'mag', 'user', 'imagepaths',
                array(
                    'path' => $image_mag_logo_vpath,
                    'fields' => array(
                        'mag_ref' => $mag['ref'],
                        'mag_logo' => $mag['logo'],
                    )
                )
            );

            $return[$mag['mid']] = $mag;
        }
    }

    return $return;
}

?>