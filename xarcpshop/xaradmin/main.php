<?php
/**
 * File: $Id:
 * 
 * xarCPShop main administration function
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * the main administration function
 */
function xarcpshop_admin_main()
{
    if (!xarSecurityCheck('EditxarCPShop')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('xarcpshop', 'admin', 'menu');
        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the administration part of xarCPShop module...');
        // Return the template variables defined in this function
        return $data;
    } else {
        xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'view'));
    }
    // success
    return true;
} 

?>
