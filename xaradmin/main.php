<?php
/**
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
      xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'view'));
    // success
    return true;
} 

?>
