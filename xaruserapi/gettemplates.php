<?php
/**
* Get list of available templates
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * Get list of available templates
 *
 * @author Curtis Farnham <curtis@farnham.com>
 * @return array item array, or false on failure
 * @throws BAD_PARAM, NO_PERMISSION
 */
function ebulletin_userapi_gettemplates()
{
    // get template directory
    $template_dir = xarModGetVar('ebulletin', 'template_dir');

    // if directory not available, throw error
    if (empty($template_dir) || !is_dir($template_dir) || !is_readable($template_dir)) {
        $msg = xarML('Invalid template directory #(1).  Make sure it exists and is readable.', $template_dir);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $templates = array();
    $hd = opendir($template_dir);
    while (false !== ($file = readdir($hd))) {
        if (preg_match("/\.x[dt]\$/", $file) &&
            is_file("$template_dir/$file") &&
            is_readable("$template_dir/$file")) {
            // add to array
            $templates[] = $file;
        }
    }

    // sort for convenience
    natcasesort($templates);

    return $templates;
}

?>
