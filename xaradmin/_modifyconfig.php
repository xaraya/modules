<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    if (!xarSecurity::check('AdminComments')) {
        return;
    }
    //$numstats       = xarModVars::get('comments','numstats');
    //  $rssnumitems    = xarModVars::get('comments','rssnumitems');

    if (empty($rssnumitems)) {
        //xarModVars::set('comments', 'rssnumitems', 25);
    }
    if (empty($numstats)) {
        //xarModVars::set('comments', 'numstats', 100);
    }

    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'comments_general', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('tabmodule', 'str:1:100', $tabmodule, 'comments', xarVar::NOT_REQUIRED)) {
        return;
    }
    $hooks = xarModHooks::call('module', 'getconfig', 'comments');
    if (!empty($hooks) && isset($hooks['tabs'])) {
        foreach ($hooks['tabs'] as $key => $row) {
            $configarea[$key]  = $row['configarea'];
            $configtitle[$key] = $row['configtitle'];
            $configcontent[$key] = $row['configcontent'];
        }
        array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
    } else {
        $hooks['tabs'] = [];
    }
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'comments_general':
                default:
                    //check for comments hook in case it's set independently elsewhere
                    if (xarModHooks::isHooked('comments', 'roles')) {
                        xarModVars::set('comments', 'usersetrendering', true);
                    } else {
                        xarModVars::set('comments', 'usersetrendering', false);
                    }
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            // if (!xarSec::confirmAuthKey()) return;
            //if (!xarVar::fetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('comments', 'itemsperpage'), xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) return;
            //if (!xarVar::fetch('shorturls', 'checkbox', $shorturls, false, xarVar::NOT_REQUIRED)) return;
            //if (!xarVar::fetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('comments', 'useModuleAlias'), xarVar::NOT_REQUIRED)) return;
            //if (!xarVar::fetch('aliasname', 'str', $aliasname,  xarModVars::get('comments', 'aliasname'), xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('editstamp', 'int', $editstamp, xarModVars::get('comments', 'editstamp'), xarVar::NOT_REQUIRED)) {
                return;
            }

            if (!xarVar::fetch('wrap', 'checkbox', $wrap, xarModVars::get('comments', 'wrap'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('numstats', 'str', $numstats, 20, xarVar::NOT_REQUIRED)) {
                return;
            }

            if (!xarVar::fetch('rssnumitems', 'int', $rssnumitems, xarModVars::get('comments', 'rssnumitems'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('showtitle', 'checkbox', $showtitle, xarModVars::get('comments', 'showtitle'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('enable_comments', 'checkbox', $showtitle, xarModVars::get('comments', 'enable_comments'), xarVar::NOT_REQUIRED)) {
                return;
            }

            if (!xarVar::fetch('filters_min_item_count', 'int', $filters_min_item_count, xarModVars::get('comments', 'filters_min_item_count'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('filters_min_item_count', 'int', $filters_min_item_count, xarModVars::get('comments', 'filters_min_item_count'), xarVar::NOT_REQUIRED)) {
                return;
            }

            if (!xarVar::fetch('postanon', 'checkbox', $postanon, xarModVars::get('comments', 'postanon'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('useblacklist', 'checkbox', $useblacklist, xarModVars::get('comments', 'useblacklist'), xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('useblacklist', 'checkbox', $useblacklist, 1, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('depth', 'str:1:', $depth, _COM_MAX_DEPTH, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('render', 'str:1:', $render, _COM_VIEW_THREADED, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('sortby', 'str:1:', $sortby, _COM_SORTBY_THREAD, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('order', 'str:1:', $order, _COM_SORT_ASC, xarVar::NOT_REQUIRED)) {
                return;
            }
            // if (!xarVar::fetch('authorize', 'checkbox', $authorize, xarModVars::get('comments', 'authorize'), xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('authorize', 'checkbox', $authorize, 1, xarVar::NOT_REQUIRED)) {
                return;
            }
            if (!xarVar::fetch('usersetrendering', 'checkbox', $usersetrendering, xarModVars::get('comments', 'usersetrendering'), xarVar::NOT_REQUIRED)) {
                return;
            }


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
                xarModVars::set('comments', 'usersetrendering', $usersetrendering);
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
            xarModItemVars::set('comments', 'usersetrendering', $usersetrendering, $regid);

            /* Blacklist feed unavailable
            xarModVars::set('comments', 'useblacklist', $useblacklist);
            if ($useblacklist == true){
                if (!xarMod::apiFunc('comments', 'admin', 'import_blacklist')) return;
            }
            */
            if ($usersetrendering == true) {
                //check and hook Comments to roles if not already hooked
                if (!xarModHooks::isHooked('comments', 'roles')) {
                    xarMod::apiFunc(
                        'modules',
                        'admin',
                        'enablehooks',
                        ['callerModName' => 'roles',
                                              'hookModName' => 'comments', ]
                    );
                }
            } else {
                if (xarModHooks::isHooked('comments', 'roles')) {
                    //unhook Comments from roles
                    xarMod::apiFunc(
                        'modules',
                        'admin',
                        'disablehooks',
                        ['callerModName' => 'roles',
                                              'hookModName' => 'comments', ]
                    );
                }
            }

            xarController::redirect(xarController::URL('comments', 'admin', 'modifyconfig', ['tabmodule' => $tabmodule, 'tab' => $data['tab']]));
            // Return
            return true;
            break;
    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
