<?php

/**
 * Return a list of magazines.
 * Privileges are checked against each magazine as requested (see 'level')
 * @param level string The level to test the magazine against ('edit', delete', 'admin').
 */

function mag_listapi_mags($args)
{
    extract($args);
    $return = array();

    if (empty($level)) $level = 'edit';

    $mags = xarModAPIfunc('mag', 'user', 'getmags');

    if (!empty($mags)) {
        foreach($mags as $mag) {
            $allowed = false;
            switch($level) {
                default:
                case('edit'):
                    // Check if we are allowed to edit articles in this magazine.
                    if (xarSecurityCheck('EditMag', 0, 'Mag', (string)$mag['mid'])) $allowed = true;
                    break;
            }

            if ($allowed) $return[$mag['mid']] = $mag['title'];
        }
    }

    return $return;
}

?>