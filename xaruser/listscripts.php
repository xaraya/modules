<?php
/**
 * List the available module wizards
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage wizards
 * @link http://xaraya.com/index.php/release/3007.html
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
