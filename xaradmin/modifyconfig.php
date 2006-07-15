<?php
/**
 *
 * Modify configuration settings for the products module
 *
 * @package modules
 * @copyright (C) 2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage commerce
 * @link  link to information for the subpackage
 * @author author name Marc Lutolf <mfl@netspan.ch>
 */

//---------------------------------------------------------
// Modify Configuration Wrapper
//---------------------------------------------------------
function products_admin_modifyconfig($args)
{
    if (!xarVarFetch('update', 'isset', $update, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab', 'str', $data['tab'], 'product_general', XARVAR_NOT_REQUIRED)) return;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
