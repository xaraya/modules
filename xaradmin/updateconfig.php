<?php
/**
 * Standard function to update module configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Standard function to update module configuration parameters
 *
 * @author Lists module development team
 */
function lists_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.  Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModSetVar('lists', 'itemsperpage', $itemsperpage);

    xarModCallHooks('module','updateconfig','lists',
                   array('module' => 'lists'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('lists', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>