<?php
/**
 * Webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author Andrea Moro
 * @access public
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function webshare_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('Adminwebshare')) return;

    $websites = xarModAPIFunc('webshare', 'user', 'get');

    $data['websites'] = $websites;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
