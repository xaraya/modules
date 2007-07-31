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
 * generate edit menu fragments
 *
 * @author Richard Cave
 * @param $args['page'] - func calling edit menu
 * @returns Menu template data
 * @return $data
 */
function newsletter_admin_editmenu()
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Create data array
    $data = array();

    xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED);
   // xarVarFetch('sortby', 'str', $data['selection'],  '', XARVAR_NOT_REQUIRED);

    $data['menulinks'] = xarModAPIFunc('newsletter', 'admin', 'editmenu');
    //$data['enabledimages']  = xarModGetVar('newsletter', 'Enable Images');

    // Return the template variables defined in this function
    return $data;

}

?>
