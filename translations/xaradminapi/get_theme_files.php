<?php

/**
 * File: $Id$
 *
 * Get filenames list from specified directory in theme
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_get_theme_files($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($themedir) && isset($pattern)');

    $names = array();
    if (file_exists($themedir)) {
        $dd = opendir($themedir);
        while ($filename = readdir($dd)) {
            if (!preg_match($pattern, $filename, $matches)) continue;
            $names[] = $matches[1];
        }
        closedir($dd);
    }
    return $names;
}

?>