<?php

/**
 * File: $Id$
 *
 * Get filenames list from module blocks template directory
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_get_module_blktempl($moddir)
{
    $blktplnames = array();
    if (file_exists("modules/$moddir/xartemplates/blocks")) {
        $dd = opendir("modules/$moddir/xartemplates/blocks");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $blktplnames[] = $matches[1];
        }
        closedir($dd);
    }
    return $blktplnames;
}

?>