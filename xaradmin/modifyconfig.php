<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return mixed, true on update success, or array of form data
 */
function crispbb_admin_modifyconfig()
{

    // Admin only function
    if (!xarSecurityCheck('AdminCrispBB')) return;

    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('checkupdate', 'checkbox', $checkupdate, false, XARVAR_NOT_REQUIRED)) return;

    $now = time();
    $tracking = xarMod::apiFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModUserVars::set('crispbb', 'tracking', serialize($tracking));
    }

    $modid = xarMod::getRegID('crispbb');
    $modinfo = xarMod::getInfo($modid);
    if ($checkupdate) {
        $phase = 'form';
        $hasupdate = xarMod::apiFunc('crispbb', 'user', 'checkupdate',
            array('version' => $modinfo['version']));
        // set for waiting content
        if ($hasupdate) {
            xarModVars::set('crispbb', 'latestversion', $hasupdate);
        }
    }

    $invalid = array();
    if ($phase == 'update') {
        if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('usealias',  'checkbox', $usealias,false,XARVAR_NOT_REQUIRED)) return;

        if (!xarVarFetch('showuserpanel', 'checkbox', $showuserpanel, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showsearchbox', 'checkbox', $showsearchbox, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showforumjump', 'checkbox', $showforumjump, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showtopicjump', 'checkbox', $showtopicjump, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showquickreply', 'checkbox', $showquickreply, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showpermissions', 'checkbox', $showpermissions, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showsortbox', 'checkbox', $showsortbox, false, XARVAR_NOT_REQUIRED)) return;
        // input validated, update settings
        if (empty($invalid)) {
            if (!xarSecConfirmAuthKey()) return;
            xarModVars::set('crispbb', 'SupportShortURLs', $shorturls);
            if (isset($aliasname) && trim($aliasname)<>'') {
                xarModVars::set('crispbb', 'useModuleAlias', $usealias);
            } else{
                 xarModVars::set('crispbb', 'useModuleAlias', 0);
            }
            $currentalias = xarModVars::get('crispbb','aliasname');
            $newalias = trim($aliasname);
            /* Get rid of the spaces if any, it's easier here and use that as the alias*/
            if ( strpos($newalias,'_') === FALSE )
            {
                $newalias = str_replace(' ','_',$newalias);
            }
            $hasalias= xarModGetAlias($currentalias);
            $useAliasName= xarModVars::get('crispbb','useModuleAlias');

            if (($useAliasName==1) && !empty($newalias)){
                /* we want to use an aliasname */
                /* First check for old alias and delete it */
                if (isset($hasalias) && ($hasalias =='crispbb')){
                    xarModDelAlias($currentalias,'crispbb');
                }
                /* now set the new alias if it's a new one */
                  xarModSetAlias($newalias,'crispbb');
            }
            /* now set the alias modvar */
            xarModVars::set('crispbb', 'aliasname', $newalias);

            xarModCallHooks('module','updateconfig','crispbb',
                           array('module' => 'crispbb'));

            xarModVars::set('crispbb', 'showuserpanel', $showuserpanel);
            xarModVars::set('crispbb', 'showsearchbox', $showsearchbox);
            xarModVars::set('crispbb', 'showforumjump', $showforumjump);
            xarModVars::set('crispbb', 'showtopicjump', $showtopicjump);
            xarModVars::set('crispbb', 'showquickreply', $showquickreply);
            xarModVars::set('crispbb', 'showpermissions', $showpermissions);
            xarModVars::set('crispbb', 'showsortbox', $showsortbox);

            // update the status message
            xarSessionSetVar('crispbb_statusmsg', xarML('Module configuration updated'));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($returnurl)) {
                $returnurl = xarModURL('crispbb', 'admin', 'modifyconfig');
            }
            xarResponse::Redirect($returnurl);
            return true;
        }

    }
    $pageTitle = xarML('Modify Module Configuration');
    $data = array();
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modifyconfig',
            'current_sublink' => $sublink
        ));

    $data['shorturls'] = xarModVars::get('crispbb', 'SupportShortURLs');
    $data['usealias'] = xarModVars::get('crispbb', 'useModuleAlias');
    $data['aliasname'] = xarModVars::get('crispbb', 'aliasname');
    // display controls
    $data['showuserpanel'] = xarModVars::get('crispbb', 'showuserpanel');
    $data['showsearchbox'] = xarModVars::get('crispbb', 'showsearchbox');
    $data['showforumjump'] = xarModVars::get('crispbb', 'showforumjump');
    $data['showtopicjump'] = xarModVars::get('crispbb', 'showtopicjump');
    $data['showquickreply'] = xarModVars::get('crispbb', 'showquickreply');
    $data['showpermissions'] = xarModVars::get('crispbb', 'showpermissions');
    $data['showsortbox'] = xarModVars::get('crispbb', 'showsortbox');
    // update check
    $data['version'] = $modinfo['version'];
    $data['newversion'] = !empty($hasupdate) ? $hasupdate : NULL;
    $data['checkupdate'] = $checkupdate;


    // store function name for use by admin-main as an entry point
    xarSessionSetVar('crispbb_adminstartpage', 'modifyconfig');
    /* Return the template variables defined in this function */
    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));
    return $data;
}
?>
