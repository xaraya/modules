<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminProgram')) return;

    $data = array();

    $data['authid'] = xarSecGenAuthKey();

    $data['shorturlschecked'] = xarModGetVar('labaffiliate', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you can use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('labaffiliate', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('labaffiliate','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'labaffiliate',
                       array('module' => 'labaffiliate'));
    if (!empty($hooks)) {
        $data['hooks'] = $hooks;
    
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;

}

?>