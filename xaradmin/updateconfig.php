<?php
/**
 * Update comment module configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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

    if (!xarVarFetch('editstamp','int:1',$editstamp,0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_wrap','checkbox', $xar_wrap, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rssnumitems', 'int', $rssnumitems, 25, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showoptions', 'checkbox', $showoptions, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_postanon', 'checkbox', $xar_postanon, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_useblacklist', 'checkbox', $xar_useblacklist, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_depth', 'str:1:', $xar_depth, _COM_MAX_DEPTH, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_render', 'str:1:', $xar_render, _COM_VIEW_THREADED, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_sortby', 'str:1:', $xar_sortby, _COM_SORTBY_THREAD, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_order', 'str:1:', $xar_order, _COM_SORT_ASC, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('xar_authorize', 'checkbox', $xar_authorize, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usersetrendering', 'checkbox', $usersetrendering, false, XARVAR_NOT_REQUIRED)) return;

    xarModSetVar('comments', 'AllowPostAsAnon', $xar_postanon);
    xarModSetVar('comments', 'AuthorizeComments', $xar_authorize);
    xarModSetVar('comments', 'depth', $xar_depth);
    xarModSetVar('comments', 'render', $xar_render);
    xarModSetVar('comments', 'sortby', $xar_sortby);
    xarModSetVar('comments', 'order', $xar_order);
    xarModSetVar('comments', 'editstamp', $editstamp);
    xarModSetVar('comments', 'wrap', $xar_wrap);
    xarModSetVar('comments', 'numstats', $numstats);
    xarModSetVar('comments', 'rssnumitems', $rssnumitems);
    xarModSetVar('comments', 'showtitle', $showtitle);
    xarModSetVar('comments', 'showoptions', $showoptions);
    xarModSetVar('comments', 'useblacklist', $xar_useblacklist);
    xarModSetVar('comments','usersetrendering',$usersetrendering);
    xarModCallHooks('module', 'updateconfig', 'comments', array('module' => 'comments'));
    /* Blacklist feed unavailable
    xarModSetVar('comments', 'useblacklist', $xar_useblacklist);
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