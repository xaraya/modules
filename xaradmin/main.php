<?php
/**
 * the main administration function
 *
 * @author John Cox
 * @access public
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function pmember_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminPMember')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('pmember', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>