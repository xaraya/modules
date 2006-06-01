<?php
/**
 * Modify xarBB Configuration
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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
            //General setting for using nntp or not so we can easily grab it (until fully implemented and other settings in place)
            $masternntpsetting=xarModGetVar('xarbb','masternntpsetting');
            $data['masternntpsetting']       = !isset($masternntpsetting) ? false :$masternntpsetting;
            $xarsettings= xarModGetVar('xarbb', 'settings');
            if (!empty($xarsettings)) {
                $settings = unserialize($xarsettings);
            }
            // TODO: define these defaults in ONE place only.
            $data['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
            $data['postsortorder']   = !isset($settings['postsortorder']) ? 'ASC' :$settings['postsortorder'];
            $data['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
            $data['topicsortby']     = !isset($settings['topicsortby']) ? 'time' :$settings['topicsortby'];
            $data['topicsortorder']  = !isset($settings['topicsortorder']) ? 'DESC' :$settings['topicsortorder'];
            $data['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
            $data['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
            $data['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
            $data['editstamp']       = !isset($settings['editstamp']) ? 0 :$settings['editstamp'];
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
 
            // TODO: define these defaults in ONE place only.
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
            if (!xarVarFetch('editstamp','int:0:2',$editstamp,0,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showcats','checkbox', $showcats, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usenntp','checkbox', $usenntp, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('linknntp','checkbox', $linknntp, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpport','int:1:4',$nntpport, 119, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpserver', 'str:1:', $nntpserver, 'news.xaraya.com', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpgroup', 'str:1:', $nntpgroup, 'xaraya.test', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('xarbbtitle', 'str:1:', $xarbbtitle, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('masternntpsetting', 'checkbox', $masternntpsetting, false, XARVAR_NOT_REQUIRED)) return;
            // Update module variables
            xarModSetVar('xarbb', 'SupportShortURLs', $supportshorturls);
            xarModSetVar('xarbb', 'xarbbtitle', $xarbbtitle);
            xarModSetVar('xarbb', 'masternntpsetting', $masternntpsetting);            
            if (isset($aliasname) && trim($aliasname)<>'') {
                xarModSetVar('xarbb', 'useModuleAlias', $modulealias);
            } else{
                xarModSetVar('xarbb', 'useModuleAlias', 0);
            }
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

            // Set default settings
            xarModSetVar('xarbb', 'settings', serialize($settings));
 
            // Module alias for short URLs
           $currentalias = xarModGetVar('xarbb','aliasname');
           $newalias = trim($aliasname);
           /* Get rid of the spaces if any, it's easier here and use that as the alias*/
           if ( strpos($newalias,'_') === FALSE )
           {
               $newalias = str_replace(' ','_',$newalias);
           }
           $hasalias= xarModGetAlias($currentalias);
           $useAliasName= xarModGetVar('xarbb','useModuleAlias');

           if (($useAliasName==1) && !empty($newalias)){
               /* we want to use an aliasname */
               /* First check for old alias and delete it */
               if (isset($hasalias) && ($hasalias =='xarbb')){
                   xarModDelAlias($currentalias,'xarbb');
               }
               /* now set the new alias if it's a new one */
               xarModSetAlias($newalias,'xarbb');
           }
           /* now set the alias modvar */
           xarModSetVar('xarbb', 'aliasname', $newalias);

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