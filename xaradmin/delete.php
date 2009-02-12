<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_admin_delete($args)
{
    extract($args);

    if (!xarVarFetch('programid',     'id', $programid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $programid = $objectid;
    }

    $item = xarModAPIFunc('labaffiliate',
        'user',
        'get',
        array('programid' => $programid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteProgram', 1, 'Program', "$item[program_name]:All:$programid")) {
        return;
    }

    if (empty($confirm)) {
        /* No confirmation yet - display a suitable form to obtain confirmation
         * of this action from the user
         * Initialise the $data variable that will hold the data to be used in
         * the blocklayout template, and get the common menu configuration - it
         * helps if all of the module pages have a standard menu at the top to
         * support easy navigation
         */

		$data = $item;

        /* Specify for which item you want confirmation */
        $data['programid'] = $programid;

        /* Add some other data you'll want to display in the template */
        $data['program_name'] = xarVarPrepForDisplay($item['program_name']);

        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();

        /* Return the template variables defined in this function */
        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('labaffiliate',
            'admin',
            'delete',
            array('programid' => $programid))) {
        return; // throw back
    }

    xarResponseRedirect(xarModURL('labaffiliate', 'admin', 'view'));

    /* Return */
    return true;
}

?>