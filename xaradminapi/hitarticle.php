<?php

/**
 * Increment the hitcount for an article, signifying a single view.
 *
 * @param aid integer Article ID
 */

function mag_adminapi_hitarticle($args)
{
    extract($args);

    if (empty($aid) || !is_numeric($aid)) return false;

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,'
        )
    ));

    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $query = 'UPDATE ' . $tables['mag_articles']
        . ' SET hitcount = hitcount + 1'
        . ' WHERE aid = ?';

    $result = $dbconn->Query($query, array((int)$aid));

    return ($result ? true : false);
}

?>