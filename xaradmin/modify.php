<?php

/**
 * Modify a magazine item.
 */

function mag_admin_modify($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,itemtype_mags,itemtype_issues,itemtype_series,itemtype_articles,itemtype_authors,itemtype_articles_authors'
        )
    ));

    // Article ID
    xarVarFetch('aid', 'int:0', $aid, NULL, XARVAR_NOT_REQUIRED);

    // Fetch the itemtype
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);

    
    // An article ID or itemtype is set.
    if (isset($aid) || $itemtype == $itemtype_articles) {
        if (!empty($itemid)) $aid = $itemtype;
        $args['aid'] = $aid;
        return xarModFunc('mag', 'admin', 'modifyarticle', $args);
    }

    // We should only get here in the event of an error.
    return array();
}

?>