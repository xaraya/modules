<?php
/**
 * File: $Id
 *
 * MIME initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage mime
 * @author Carl P. Corliss <carl.corliss.com>
 */

/**
 * initialise the mime module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function mime_init()
{
// Initialisation successful
    include('modules/mime/xarincludes/mime.magic.php');

    xarModSetVar('mime','mime.magic',serialize($mime_list));

    return true;
}

/**
 * upgrade the mime module from an old version
 * This function can be called multiple times
 */
function mime_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 0.5:
            break;
        case 1.0:
            break;
        case 2.0:
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the mime module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function mime_delete()
{
    // Deletion successful
    return true;
}

?>
