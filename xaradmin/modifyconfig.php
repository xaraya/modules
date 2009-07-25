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
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }

    $modid = xarModGetIDFromName('crispbb');
    $modinfo = xarModGetInfo($modid);
    if ($checkupdate) {
        $phase = 'form';
        $hasupdate = xarModAPIFunc('crispbb', 'user', 'checkupdate',
            array('version' => $modinfo['version']));
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

        // input validated, update settings
        if (empty($invalid)) {
            if (!xarSecConfirmAuthKey()) return;
            xarModSetVar('crispbb', 'SupportShortURLs', $shorturls);
            if (isset($aliasname) && trim($aliasname)<>'') {
                xarModSetVar('crispbb', 'useModuleAlias', $usealias);
            } else{
                 xarModSetVar('crispbb', 'useModuleAlias', 0);
            }
            $currentalias = xarModGetVar('crispbb','aliasname');
            $newalias = trim($aliasname);
            /* Get rid of the spaces if any, it's easier here and use that as the alias*/
            if ( strpos($newalias,'_') === FALSE )
            {
                $newalias = str_replace(' ','_',$newalias);
            }
            $hasalias= xarModGetAlias($currentalias);
            $useAliasName= xarModGetVar('crispbb','useModuleAlias');

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
            xarModSetVar('crispbb', 'aliasname', $newalias);

            xarModCallHooks('module','updateconfig','crispbb',
                           array('module' => 'crispbb'));

            xarModSetVar('crispbb', 'showuserpanel', $showuserpanel);
            xarModSetVar('crispbb', 'showsearchbox', $showsearchbox);
            xarModSetVar('crispbb', 'showforumjump', $showforumjump);
            xarModSetVar('crispbb', 'showtopicjump', $showtopicjump);
            xarModSetVar('crispbb', 'showquickreply', $showquickreply);
            xarModSetVar('crispbb', 'showpermissions', $showpermissions);

            // update the status message
            xarSessionSetVar('crispbb_statusmsg', xarML('Module configuration updated'));
            // if no returnurl specified, return to the modify function for the newly created forum
            if (empty($returnurl)) {
                $returnurl = xarModURL('crispbb', 'admin', 'modifyconfig');
            }
            xarResponseRedirect($returnurl);
            return true;
        }

    }
    $pageTitle = xarML('Modify Module Configuration');
    $data = array();
    $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modifyconfig',
            'current_sublink' => $sublink
        ));

    $data['shorturls'] = xarModGetVar('crispbb', 'SupportShortURLs');
    $data['usealias'] = xarModGetVar('crispbb', 'useModuleAlias');
    $data['aliasname'] = xarModGetVar('crispbb', 'aliasname');
    // display controls
    $data['showuserpanel'] = xarModGetVar('crispbb', 'showuserpanel');
    $data['showsearchbox'] = xarModGetVar('crispbb', 'showsearchbox');
    $data['showforumjump'] = xarModGetVar('crispbb', 'showforumjump');
    $data['showtopicjump'] = xarModGetVar('crispbb', 'showtopicjump');
    $data['showquickreply'] = xarModGetVar('crispbb', 'showquickreply');
    $data['showpermissions'] = xarModGetVar('crispbb', 'showpermissions');
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
