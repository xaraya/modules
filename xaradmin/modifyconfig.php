<?php
/**
 * DOSSIER admin functions
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>
 * Based on labDossier (on PostNuke) by Chad Kraeft <cdavidkraeft@miragelab.com>
 */
/**
 * Display form used to update the configuration settings
 * Handle the data submission
 *
 * @param GET / POST passed from modifyconfig form
 * @return array xarTemplate data
 */
function dossier_admin_modifyconfig()
{
    $output = xarModAPIFunc('dossier', 'admin', 'menu');

    /**
     * Security check first
     */
    if (xarSecurityCheck('AdminDossier',0)) {
        
        // User Title for Address Book
        $output['displaytitle'] = xarModGetVar('dossier', 'displaytitle');
        
        $output['itemsperpage']     = xarModGetVar('dossier', 'itemsperpage');
        
        // short urls
        $output['aliasname']      = xarModGetVar('dossier', 'aliasname');
        $output['useModuleAlias'] = xarModGetVar('dossier', 'useModuleAlias');
        $output['shorturls']      = xarModGetVar('dossier', 'SupportShortURLs');
        
        // csr_group
        $output['csr_group']      = xarModGetVar('dossier', 'csr_group');
        
        $output['authid'] = xarSecGenAuthKey('dossier');
        
        $hooks = xarModCallHooks('module', 'modifyconfig', 'dossier',
                           array('module' => 'dossier',
                               'itemtype' => 1));

        if (empty($hooks)) {
            $output['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for this module'));
        } else {
            $output['hooks'] = $hooks;
        }

    } else {
        return xarTplModule('dossier','user','noauth');
    }

    return $output;

} // END modifyconfig

?>
