<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * the main user function
 */
function publications_user_main($args)
{
# --------------------------------------------------------
#
# Try getting the id of the default page.
#
    $id = xarModVars::get('publications', 'defaultpage');

    if (!empty($id)) {
# --------------------------------------------------------
#
# Get the ID of the translation if required
#
        if(!xarVarFetch('translate', 'int:1', $translate,  1, XARVAR_NOT_REQUIRED)) {return;}
        return xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $id,'translate' => $translate)));
    } else {
# --------------------------------------------------------
#
# No default page, just show the view page
#
        return xarController::redirect(xarModURL('publications', 'user', 'view'));
    }
}

?>