<?php
/**
* Get a template file
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
 * Get a template file
 *
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['filename'] the template filename
 * @returns string
 * @return $sourceFile
 */
function ebulletin_adminapi_gettemplatefile($args)
{
    // Extract args
    extract($args);

    if (empty($filename)) {
        $msg = xarML('Must provide a filename!  #(1) function #(2)() in module #(3)',
                'adminapi', 'gettemplatefile', 'eBulletin');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Get the template source
    if (!($modBaseInfo = xarMod_getBaseInfo("ebulletin"))) return;
    $modOsDir = $modBaseInfo['osdirectory'];
    $sourceFile = "modules/$modOsDir/xartemplates/$filename";

    // Check if the template file exists
    if (!file_exists($sourceFile)) {
        $msg = xarML('Template file #(1) does not exist!', $sourceFile);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    return $sourceFile;
}

?>
