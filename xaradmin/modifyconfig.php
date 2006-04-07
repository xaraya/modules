<?php
/**
* Display GUI for config modification
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Display GUI for config modification
*/
function ebulletin_admin_modifyconfig()
{
    // security check
    if (!xarSecurityCheck('AdmineBulletin')) return;

    // get module vars
    $admin_issuesperpage = xarModGetVar('ebulletin', 'admin_issuesperpage');
    $admin_subsperpage   = xarModGetVar('ebulletin', 'admin_subsperpage');
    $supportshorturls    = xarModGetVar('ebulletin', 'SupportShortURLs');
    $usemodulealias      = xarModGetVar('ebulletin', 'useModuleAlias');
    $aliasname           = xarModGetVar('ebulletin', 'aliasname');
    $msglimit            = xarModGetVar('ebulletin', 'msglimit');
    $msgunit             = xarModGetVar('ebulletin', 'msgunit');
    $requirevalidation   = xarModGetVar('ebulletin', 'requirevalidation');

    // message units
    $msgunits = array();
    $msgunits['minute'] = xarML('Minute');
    $msgunits['hour']   = xarML('Hour');
    $msgunits['day']    = xarML('Day');
    $msgunits['week']   = xarML('Week');
    $msgunits['month']  = xarML('Minute');

    // set template vars
    $data = array();
    $data['admin_issues']      = $admin_issuesperpage;
    $data['admin_subs']        = $admin_subsperpage;
    $data['supportshorturls']  = $supportshorturls;
    $data['usemodulealias']    = $usemodulealias;
    $data['aliasname']         = $aliasname;
    $data['msglimit']          = $msglimit;
    $data['msgunit']           = $msgunit;
    $data['requirevalidation'] = $requirevalidation;

    // add other vars to template
    $data['authid']   = xarSecGenAuthKey();
    $data['msgunits'] = $msgunits;

    // get modifyconfig hooks
    $data['hookoutput'] = xarModCallHooks('module', 'modifyconfig', 'ebulletin',
        array('module' => 'ebulletin')
    );

    return $data;
}

?>
