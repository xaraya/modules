<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * Turn logging off
 */
function logconfig_adminapi_turnoff ()
{
    $filename = xarModAPIFunc('logconfig','admin','filename');

    if (file_exists($filename)) {
        //Turn off

        //In a busy site, this might be dificult to delete.
        $time = time();
        while (!unlink($filename) && ( ($time-time()) < 3) ) {}

         if (file_exists($filename)) {
            $msg = xarML('Unable to delete file (#(1))', $filename);
            xarErrorSet(XAR_SYSTEM_MESSAGE, 'UNABLE_DELETE_FILE', $msg);
            return;
         }
    }

    return true;
}

?>