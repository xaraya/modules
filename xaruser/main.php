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
        if (!empty($args['$translate']))
            $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
        return xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $id)));
    } else {
# --------------------------------------------------------
#
# No default page, just show the view page
#
        return xarController::redirect(xarModURL('publications', 'user', 'view'));
    }
}

?>