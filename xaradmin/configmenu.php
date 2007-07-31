<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * generate config menu fragments
 *
 * @author Richard Cave
 * @param $args['page'] - func calling config menu
 * @return array Menu template data
 */
function newsletter_admin_configmenu()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Create data array
    $data = array();

    xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED);
   // xarVarFetch('sortby', 'str', $data['selection'],  '', XARVAR_NOT_REQUIRED);

    $data['menulinks'] = xarModAPIFunc('newsletter', 'admin', 'configmenu');
    //$data['enabledimages']  = xarModGetVar('newsletter', 'Enable Images');
    // Return the template variables defined in this function
    return $data;

}

?>
