<?php
/**
 * File: $Id$
 * 
 * Modify xarBB Configuration
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminxarBB')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
    switch(strtolower($phase)) {
        case 'modify':
        default:
            $data = array();
            $settings = array();
            $xarsettings= xarModGetVar('xarbb', 'settings');
            if (!empty($xarsettings)) {
                $settings = unserialize($xarsettings);
            }
            $data['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
            $data['postsortorder']   = !isset($settings['postsortorder']) ? 'ASC' :$settings['postsortorder'];
            $data['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
            $data['topicsortby']     = !isset($settings['topicsortby']) ? 'time' :$settings['topicsortby'];
            $data['topicsortorder']  = !isset($settings['topicsortorder']) ? 'DESC' :$settings['topicsortorder'];
            $data['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
            $data['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
            $data['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
            $data['editstamp']       = !isset($settings['editstamp']) ? 1 :$settings['editstamp'];
            $data['showcats']        = !isset($settings['showcats']) ? false :$settings['showcats'];
            $data['usenntp']         = !isset($settings['usenntp']) ? false :$settings['usenntp'];
            $data['linknntp']        = !isset($settings['linknntp']) ? false :$settings['linknntp'];
            $data['nntpport']        = !isset($settings['nntpport']) ? 119 :$settings['nntpport'];
            $data['nntpserver']      = !isset($settings['nntpserver']) ? 'news.xaraya.com' :$settings['nntpserver'];
            $data['nntpgroup']       = !isset($settings['nntpgroup']) ? '' :$settings['nntpgroup'];
            $hooks                   = xarModCallHooks('module', 'modifyconfig', 'xarbb', array('module' => 'xarbb')); // forum
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            $data['settings']        = $settings;
            $data['authid']          = xarSecGenAuthKey();
        break;

        case 'update':

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
 
            if (!xarVarFetch('hottopic','int:1:',$hottopic,10,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postsperpage','int:1:',$postsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postsortorder', 'str:1:', $postsortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('forumsperpage','int:1:',$forumsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsortby', 'str:1:', $topicsortby, 'time', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsortorder', 'str:1:', $topicsortorder, 'DESC', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('supportshorturls','checkbox', $supportshorturls,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowhtml','checkbox', $allowhtml, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowbbcode','checkbox', $allowbbcode, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('editstamp','int:1',$editstamp,0,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showcats','checkbox', $showcats, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usenntp','checkbox', $usenntp, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('linknntp','checkbox', $linknntp, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpport','int:1:4',$nntpport, 119, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpserver', 'str:1:', $nntpserver, 'news.xaraya.com', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpgroup', 'str:1:', $nntpgroup, 'xaraya.test', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cookiename', 'str:1:', $cookiename, 'xarbb', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cookiepath', 'str:1:', $cookiepath, '/', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('xarbbtitle', 'str:1:', $xarbbtitle, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;

            // Update module variables
            xarModSetVar('xarbb', 'SupportShortURLs', $supportshorturls);
            xarModSetVar('xarbb', 'useModuleAlias', $modulealias);
            xarModSetVar('xarbb', 'aliasname', $aliasname);
            xarModSetVar('xarbb', 'cookiename', $cookiename);
            xarModSetVar('xarbb', 'cookiepath', $cookiepath);
            xarModSetVar('xarbb', 'xarbbtitle', $xarbbtitle);            
            //xarModSetVar('xarbb', 'cookiedomain', $cookiedomain);
            xarModSetVar('xarbb', 'forumsperpage', $forumsperpage); //only required for admin view
            // default settings for xarbb
            $settings = array();
            $settings['postsperpage']       = $postsperpage;
            $settings['postsortorder']      = $postsortorder;
            $settings['topicsperpage']      = $topicsperpage;
            $settings['topicsortby']        = $topicsortby;
            $settings['topicsortorder']     = $topicsortorder;
            $settings['hottopic']           = $hottopic;
            $settings['allowhtml']          = $allowhtml;
            $settings['editstamp']          = $editstamp;            
            $settings['allowbbcode']        = $allowbbcode;
            $settings['showcats']           = $showcats;
            $settings['usenntp']            = $usenntp;
            $settings['linknntp']           = $linknntp;
            $settings['nntpport']           = $nntpport;
            $settings['nntpserver']         = $nntpserver;
            $settings['nntpgroup']          = $nntpgroup;

            //Set default settings
            xarModSetVar('xarbb', 'settings', serialize($settings));
 
            // Module alias for short URLs

            $useAliasName = xarModGetVar('xarbb', 'useModuleAlias');
            $aliasname = xarModGetVar('xarbb','aliasname');
            if (($useAliasName==1) && isset($aliasname)){
               $usealias = true;
            } else{
               $usealias = false;
               $aliasname='';
            }


            if ($usealias == 1) {
                xarModSetAlias($aliasname,'xarbb');
            } else {
                xarModDelAlias($aliasname,'xarbb');
            }
            // call modifyconfig hooks with module
            $hooks = xarModCallHooks('module', 'updateconfig', 'xarbb', array('module' => 'xarbb'));
            if (empty($hooks)) {
               $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for xarbb...'));
            } else {
              $data['hooks'] = $hooks;
            }

            xarResponseRedirect(xarModURL('xarbb', 'admin', 'modifyconfig'));
            return true;
            break;
    }
    return $data;
}
?>
