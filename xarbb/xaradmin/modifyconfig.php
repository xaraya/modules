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

/**
 * modify configuration
 */
function xarbb_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminxarBB')) return;
 
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'modify':
        default:
            $data = array();
            $xarsettings= xarModGetVar('xarbb', 'settings');
            if (!empty($xarsettings)) {
                $settings = unserialize($xarsettings);
            }
            $data['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
            $data['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
            $data['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
            $data['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
            $data['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
            $data['showcats']        = !isset($settings['showcats']) ? false :$settings['showcats'];
            $data['linknntp']        = !isset($settings['linknntp']) ? false :$settings['linknntp'];
            $data['nntpport']        = !isset($settings['nntpport']) ? 119 :$settings['nntpport'];
            $data['nntpserver']      = !isset($settings['nntpserver']) ? 'news.xaraya.com' :$settings['nntpserver'];
            $data['nntpgroup']       = !isset($settings['nntpgroup']) ? '' :$settings['nntpgroup'];

            $hooks = xarModCallHooks('module', 'modifyconfig', 'xarbb',
                                    array('module' => 'xarbb')); // forum
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            $data['authid'] = xarSecGenAuthKey();
        break;

        case 'update':

            if (!xarVarFetch('hottopic','int:1:',$hottopic,10,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postsperpage','int:1:',$postsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('forumsperpage','int:1:',$forumsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('supportshorturls','checkbox', $supportshorturls,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowhtml','checkbox', $allowhtml, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowbbcode','checkbox', $allowbbcode, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showcats','checkbox', $showcats, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('linknntp','checkbox', $linknntp, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpport','int:1:4',$nntpport, 119, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpserver', 'str:1:', $nntpserver, 'news.xaraya.com', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntpgroup', 'str:1:', $nntpgroup, 'xaraya.test', XARVAR_NOT_REQUIRED)) return;
	        if (!xarVarFetch('cookiename', 'str:1:', $cookiename, 'xarbb', XARVAR_NOT_REQUIRED)) return;
	        //if (!xarVarFetch('cookiedomain', 'str:1:', $cookiepath, ' ', XARVAR_NOT_REQUIRED)) return;
	        if (!xarVarFetch('cookiepath', 'str:1:', $cookiepath, '/', XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Update module variables
            xarModSetVar('xarbb', 'SupportShortURLs', $supportshorturls);
            xarModSetVar('xarbb', 'cookiename', $cookiename);
            xarModSetVar('xarbb', 'cookiepath', $cookiepath);
            //xarModSetVar('xarbb', 'cookiedomain', $cookiedomain);
            xarModSetVar('xarbb', 'forumsperpage', $forumsperpage); //only required for admin view
            // default settings for xarbb
            $settings = array();
            $settings['postsperpage']       = $postsperpage;
            $settings['topicsperpage']      = $topicsperpage;
            $settings['hottopic']           = $hottopic;
            $settings['allowhtml']          = $allowhtml;
            $settings['allowbbcode']        = $allowbbcode;
            $settings['showcats']           = $showcats;
            $settings['linknntp']           = $linknntp;
            $settings['nntpport']           = $nntpport;
            $settings['nntpserver']         = $nntpserver;
            $settings['nntpgroup']          = $nntpgroup;

            //Set default settings
            xarModSetVar('xarbb', 'settings', serialize($settings));

            // call modifyconfig hooks with module
            $hooks = xarModCallHooks('module', 'updateconfig', 'xarbb', array('module' => 'xarbb'));

           if (empty($hooks)) {
               $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for xarbb...'));
           } else {
              $data['hooks'] = $hooks;
           }

            xarResponseRedirect(xarModURL('xarbb', 'admin', 'modifyconfig')); 

            // Return
            return true;

            break;
    }

    return $data;
}
?>
