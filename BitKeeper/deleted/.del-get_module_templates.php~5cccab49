<?php

/**
 * File: $Id$
 *
 * Get filenames list from module templates directory
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_adminapi_get_module_templates($moddir)
{
    $tplnames = array();
    if (file_exists("modules/$moddir/xartemplates")) {
        $dd = opendir("modules/$moddir/xartemplates");
        while ($filename = readdir($dd)) {
            if (!preg_match('/^([a-zA-Z\-_]+)\.xd$/i', $filename, $matches)) continue;
            $tplnames[] = $matches[1];
        }
        closedir($dd);
    }
    return $tplnames;
}

?>