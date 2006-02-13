<?php
/**
 * The admin set hall function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * The main user function
 *
 * @author jojodee
 */
function legis_admin_sethall($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminLegis')) return;
    if (!xarVarFetch('defaulthall', 'int:0:', $defaulthall, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('reset', 'int:0:1', $reset, 0, XARVAR_DONT_SET)) {return;}
    $data = xarModAPIFunc('legis', 'admin', 'menu');

    $halldata= xarModAPIFunc('legis','user','sethall',array('defaulthall'=>$defaulthall,
                                                            'reset' => $reset));
    $data['defaulthall']=$halldata['defaulthall'];
    $data['defaulthalldata']=$halldata['defaulthalldata'];
    $data['halls']=$halldata['halls'];
    $data['welcome']='';
    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
        $data['cansethall']=true;
    } else {
        $data['cansethall']=false;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Welcome to Legislation')));
    /* Return the template variables defined in this function */
    return $data;

}
?>
