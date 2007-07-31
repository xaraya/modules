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
 * Modify configuration
 *
 * @author Richard Cave
 * @return array $data
 */
function newsletter_admin_modifyconfig()
{
    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin edit menu
    $data['menu'] = xarModApiFunc('newsletter', 'admin', 'configmenu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify buttons
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    // Hooks
    $hooks = xarModCallHooks('module', 'modifyconfig', 'newsletter',
                       array('module' => 'newsletter'));
    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
        $data['hookoutput'] = $hooks;
    }

    // Provide encode/decode functions forshort URLs
    $data['bulkemail'] = xarModGetVar('newsletter','bulkemail') ? 'checked' : '';
    $data['shorturlschecked'] = xarModGetVar('newsletter','SupportShortURLs') ? 'checked' : '';
    $data['activeuserschecked'] = xarModGetVar('newsletter','activeusers') ? 'checked' : '';

    // Return the template variables defined in this function
    return $data;
}

?>
