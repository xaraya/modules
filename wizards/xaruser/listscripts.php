<?php
/**
 * List the available module wizards
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * List the available wizards
 * Run a wizard if the user clicks
 */
function wizards_user_listscripts()
{
    $wizards = xarModGetVar('wizards','status');
    if ($wizards % 2) {
        include_once "modules/wizards/xarclass/xarModuleWizards.php";
        return listwizards();
    }
    else {
        xarErrorSet(XAR_SYSTEM_MESSAGE,
        'NO_ACCESS',
         new SystemMessage("Wizards are currently not available"));
         return;
    }
}

?>
