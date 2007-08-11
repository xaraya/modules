<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/
include_once('modules/comments/xarincludes/defines.php');
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function comments_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('Comments-Admin')) return;


    if (!xarVarFetch('showoptions', 'checkbox', $showoptions, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('postanon', 'checkbox', $postanon, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('depth', 'int:1:', $depth, _COM_MAX_DEPTH, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('render', 'str:1:', $render, _COM_VIEW_THREADED, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, _COM_SORTBY_THREAD, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'str:1:', $order, _COM_SORT_ASC, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('editstamp','checkbox',$editstamp,0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('wrap','checkbox', $wrap, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('edittimelimit','str:1:', $edittimelimit, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authorize', 'checkbox', $authorize, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rssnumitems', 'int', $rssnumitems, 25, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowhookoverride', 'checkbox', $allowhookoverride, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usersetrendering', 'checkbox', $usersetrendering, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useblacklist', 'checkbox', $useblacklist, false, XARVAR_NOT_REQUIRED)) return;

    xarModSetVar('comments', 'edittimelimit', $edittimelimit);
    xarModSetVar('comments', 'AllowPostAsAnon', $postanon);
    xarModSetVar('comments', 'AuthorizeComments', $authorize);
    xarModSetVar('comments', 'depth', $depth);
    xarModSetVar('comments', 'render', $render);
    xarModSetVar('comments', 'sortby', $sortby);
    xarModSetVar('comments', 'order', $order);
    xarModSetVar('comments', 'editstamp', $editstamp);
    xarModSetVar('comments', 'wrap', $wrap);
    xarModSetVar('comments', 'showoptions', $showoptions);

    xarModSetVar('comments', 'numstats', $numstats);
    xarModSetVar('comments', 'rssnumitems', $rssnumitems);
    xarModSetVar('comments', 'showtitle', $showtitle);
    xarModSetVar('comments', 'useblacklist', $useblacklist);
    xarModSetVar('comments','usersetrendering',$usersetrendering);
    xarModSetVar('comments', 'allowhookoverride', $allowhookoverride);
    xarModCallHooks('module', 'updateconfig', 'comments', array('module' => 'comments'));
    /* Blacklist feed unavailable
    xarModSetVar('comments', 'useblacklist', $useblacklist);
    if ($xar_useblacklist == true){
        if (!xarModAPIFunc('comments', 'admin', 'import_blacklist')) return;
    }
    */
     if ($usersetrendering == true) {
         //check and hook Comments to roles if not already hooked
         if (!xarModIsHooked('comments', 'roles')) {
             xarModAPIFunc('modules','admin','enablehooks',
                                 array('callerModName' => 'roles',
                                       'hookModName' => 'comments'));
         }

     } else {
       if (xarModIsHooked('comments', 'roles')) {
             //unhook Comments from roles
             xarModAPIFunc('modules','admin','disablehooks',
                                 array('callerModName' => 'roles',
                                       'hookModName' => 'comments'));
          }
     }
    xarResponseRedirect(xarModURL('comments', 'admin', 'modifyconfig'));
    return true;
}
?>