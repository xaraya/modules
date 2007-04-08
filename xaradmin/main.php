<?php
/**
 * Webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * the main administration function
 *
 * @author Andrea Moro
 * @access public
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function webshare_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('Adminwebshare')) return;

        xarResponseRedirect(xarModURL('webshare', 'admin', 'modifyconfig'));

    // success
    return true;
}

?>
