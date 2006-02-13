<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author jojodee
 * @return array
 */
function legis_admin_modifyconfig()
{ 
    $data = array();

    /* common menu configuration */
    $data = xarModAPIFunc('legis', 'admin', 'menu');
    
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminLegis')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    $data['itemsvalue'] = xarModGetVar('legis', 'itemsperpage');
    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xd template !
     */
    $data['shorturlschecked'] = xarModGetVar('legis', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you can use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('legis', 'useModuleAlias');
    $data['aliasname']= xarModGetVar('legis','aliasname');

    $defaultmaster= xarModGetVar('legis','defaultmaster');

    if (isset($defaultmaster)) {
      $data['defaultmasterdata']=xarModAPIFunc('legis','user','getmaster',array('mdid'=>(int)$defaultmaster));
    } else {
      $data['defaultmasterdata']='';
    }

    $data['legistypes']=xarModAPIFunc('legis','user','getmastertypes');
    $hallsparent=xarModGetVar('legis','mastercids');
    $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));
    $data['usergroups']= xarModAPIFunc('roles','user','getallgroups');
    $data['halls']=$halls;
    $data['defaulthall']=xarModGetVar('legis','defaulthall');

    $data['moderatorgroup']=xarModGetVar('legis','moderatorgroup');
    $docname=xarModGetVar('legis','docname');
    if (!isset($docname) || trim($docname)=='') {
      $docname=xarML('Legislation');
    }
    $data['docname']=$docname;
    $data['allowchange']=xarModGetVar('legis', 'allowhallchange') ? true : false;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'legis',
                       array('module' => 'legis'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for Legislation module'));
    } else {
        $data['hooks'] = $hooks;
    
         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>
