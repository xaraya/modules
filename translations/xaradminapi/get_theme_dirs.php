<?php

/**
 * File: $Id$
 *
 * Get directories list from theme directory
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

//  This function returns an array containing all the php files
//  in a given directory that start with "xar"

$staticNames = array();

function searchDir($path, $prefix) 
{
  global $staticNames;

  $fileModules = array();
  $dh = opendir($path);
  $path2 = ereg_replace($prefix,"",$path);
  $staticNames[] = $path2;
  while ($entry = readdir($dh)) {
    if (is_dir("$path/$entry")) {
      if (($entry != '.') && ($entry != '..') && ($entry != 'SCCS')) {
        //Recurse
        searchDir("$path/$entry", $prefix);
      }
    }
  }
  closedir($dh);
}

function translations_adminapi_get_theme_dirs($args)
{
    global $staticNames;

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($themedir)');
    $prefix = "themes/$themedir/";

    if (file_exists("themes/$themedir")) {
        $dd = opendir("themes/$themedir");
        while ($filename = readdir($dd)) {
            if ($filename == 'blocks' || $filename == 'modules' || $filename == 'pages') 
                searchDir("themes/$themedir/$filename", $prefix);
        }
        closedir($dd);
    }
    return $staticNames;
}

?>