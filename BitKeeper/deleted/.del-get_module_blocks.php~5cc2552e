<?php

/**
 * File: $Id$
 *
 * Get filenames list from module blocks directory
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_get_module_blocks($moddir)
{
    $blocknames = array();
    if (file_exists("modules/$moddir/xarblocks")) {
        $dd = opendir("modules/$moddir/xarblocks");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.php$/i', $filename, $matches)) continue;
            $blocknames[] = $matches[1];
        }
        closedir($dd);
    }
    return $blocknames;
}

?>