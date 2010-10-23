<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
sys::import('modules.comments.xarincludes.defines');
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */

function comments_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminComments')) return;
    //$numstats       = xarModVars::get('comments','numstats');
  //  $rssnumitems    = xarModVars::get('comments','rssnumitems');
    
    if (empty($rssnumitems)) {
        //xarModVars::set('comments', 'rssnumitems', 25);
    }
    if (empty($numstats)) {
        //xarModVars::set('comments', 'numstats', 100);
    }

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'comments_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'comments', XARVAR_NOT_REQUIRED)) return;
    $hooks = xarModCallHooks('module', 'getconfig', 'comments');
    if (!empty($hooks) && isset($hooks['tabs'])) {
        foreach ($hooks['tabs'] as $key => $row) {
            $configarea[$key]  = $row['configarea'];
            $configtitle[$key] = $row['configtitle'];
            $configcontent[$key] = $row['configcontent'];
        }
        array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
    } else {
        $hooks['tabs'] = array();
    }
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'comments_general':
                default:
                    //check for comments hook in case it's set independently elsewhere
                    if (xarModIsHooked('comments', 'roles')) {
                        xarModVars::set('comments','usersetrendering',true);
                    } else {
                        xarModVars::set('comments','usersetrendering',false);
                    }
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
           // if (!xarSecConfirmAuthKey()) return;
            //if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('comments', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            //if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            //if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('comments', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
            //if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('comments', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('editstamp','int',$editstamp, xarModVars::get('comments', 'editstamp'),XARVAR_NOT_REQUIRED)) return;
           
            if (!xarVarFetch('wrap','checkbox', $wrap, xarModVars::get('comments', 'wrap'),XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('numstats', 'str', $numstats, 20, XARVAR_NOT_REQUIRED)) return;
            
            if (!xarVarFetch('rssnumitems', 'int', $rssnumitems, xarModVars::get('comments', 'rssnumitems'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showtitle', 'checkbox', $showtitle, xarModVars::get('comments', 'showtitle'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postanon', 'checkbox', $postanon, xarModVars::get('comments', 'postanon'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('useblacklist', 'checkbox', $useblacklist, xarModVars::get('comments', 'useblacklist'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('useblacklist', 'checkbox', $useblacklist, 1, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('depth', 'str:1:', $depth, _COM_MAX_DEPTH, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('render', 'str:1:', $render, _COM_VIEW_THREADED, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sortby', 'str:1:', $sortby, _COM_SORTBY_THREAD, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('order', 'str:1:', $order, _COM_SORT_ASC, XARVAR_NOT_REQUIRED)) return;
           // if (!xarVarFetch('authorize', 'checkbox', $authorize, xarModVars::get('comments', 'authorize'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('authorize', 'checkbox', $authorize, 1, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usersetrendering', 'checkbox', $usersetrendering, xarModVars::get('comments', 'usersetrendering'), XARVAR_NOT_REQUIRED)) return;


            if ($data['tab'] == 'comments_general') {
               // xarModVars::set('comments', 'itemsperpage', $itemsperpage);
               // xarModVars::set('comments', 'supportshorturls', $shorturls);
               // xarModVars::set('comments', 'useModuleAlias', $useModuleAlias);
               // xarModVars::set('comments', 'aliasname', $aliasname);
                xarModVars::set('comments', 'AllowPostAsAnon', $postanon);
                xarModVars::set('comments', 'AuthorizeComments', $authorize);
                xarModVars::set('comments', 'depth', $depth);
                xarModVars::set('comments', 'render', $render);
                xarModVars::set('comments', 'sortby', $sortby);
                xarModVars::set('comments', 'order', $order);
                xarModVars::set('comments', 'editstamp', $editstamp);
                xarModVars::set('comments', 'wrap', $wrap);
                xarModVars::set('comments', 'numstats', $numstats);
                xarModVars::set('comments', 'rssnumitems', $rssnumitems);
                xarModVars::set('comments', 'showtitle', $showtitle);
                xarModVars::set('comments', 'useblacklist', $useblacklist);
                xarModVars::set('comments','usersetrendering',$usersetrendering);
            }
            $regid = xarMod::getRegID($tabmodule);
            xarModItemVars::set('comments', 'AllowPostAsAnon', $postanon, $regid);
            xarModItemVars::set('comments', 'AuthorizeComments', $authorize, $regid);
            xarModItemVars::set('comments', 'depth', $depth, $regid);
            xarModItemVars::set('comments', 'render', $render, $regid);
            xarModItemVars::set('comments', 'sortby', $sortby, $regid);
            xarModItemVars::set('comments', 'order', $order, $regid);
            xarModItemVars::set('comments', 'editstamp', $editstamp, $regid);
            xarModItemVars::set('comments', 'wrap', $wrap, $regid);
            xarModItemVars::set('comments', 'numstats', $numstats, $regid);
            xarModItemVars::set('comments', 'rssnumitems', $rssnumitems, $regid);
            xarModItemVars::set('comments', 'showtitle', $showtitle, $regid);
            xarModItemVars::set('comments', 'useblacklist', $useblacklist, $regid);
            xarModItemVars::set('comments','usersetrendering',$usersetrendering, $regid);

            /* Blacklist feed unavailable
            xarModVars::set('comments', 'useblacklist', $useblacklist);
            if ($useblacklist == true){
                if (!xarMod::apiFunc('comments', 'admin', 'import_blacklist')) return;
            }
            */
             if ($usersetrendering == true) {
             //check and hook Comments to roles if not already hooked
                 if (!xarModIsHooked('comments', 'roles')) {
                     xarMod::apiFunc('modules','admin','enablehooks',
                                         array('callerModName' => 'roles',
                                               'hookModName' => 'comments'));
                 }

             } else {
               if (xarModIsHooked('comments', 'roles')) {
                //unhook Comments from roles
                     xarMod::apiFunc('modules','admin','disablehooks',
                                         array('callerModName' => 'roles',
                                               'hookModName' => 'comments'));
                  }
             }

            xarResponse::redirect(xarModURL('comments', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
            // Return
            return true;
            break;

    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>
