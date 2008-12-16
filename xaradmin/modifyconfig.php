<?php
/**
 * Modify module's configuration
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Modify module's configuration
 *
 * @return array
 */
function formantibot_admin_modifyconfig()
{ 
    $data = array();

    if (!xarSecurityCheck('FormAntiBot-Admin')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    $data['registered']      = xarModGetVar('formantibot', 'registered');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'formantibot',
                       array('module' => 'formantibot'));
                       
    if (empty($hooks)) {
        $data['hookoutput'] ='';
    } else {
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>
