<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

function photoshare_userapi_gettemplates($args)
{
    extract($args);
    if (!isset($templatetype))
        $templatetype="viewimages";

    $templates         = array();
    $templateDirectory = "modules/photoshare/xartemplates";

    $shouldStartWith = "user-$templatetype-";
    $len =strlen($shouldStartWith);
    $dh = opendir($templateDirectory);
    while ($f = readdir($dh))
    {
        if (substr(strtolower($f), 0, $len) == $shouldStartWith)
        {
            $tmp = split('\.', substr($f, $len));
            $templateName = $tmp[0];
            $templates[] = array( 'id'       => $templateName,
                                'name'     => $templateName,
                                'selected' => ($templateName == $currentTemplate ) ? 'selected="selected" ': '');

        }
    }
    closedir($dh);

    return $templates;
}

?>
