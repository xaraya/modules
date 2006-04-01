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
    $template_dir        = xarModGetVar('ebulletin', 'template_dir');
    $theme               = xarModGetVar('ebulletin', 'theme');
    $issuenumsago        = xarModGetVar('ebulletin', 'issuenumsago');
    $issueunitsago       = xarModGetVar('ebulletin', 'issueunitsago');
    $issuestartsign      = xarModGetVar('ebulletin', 'issuestartsign');
    $issuenumsfromnow    = xarModGetVar('ebulletin', 'issuenumsfromnow');
    $issueunitsfromnow   = xarModGetVar('ebulletin', 'issueunitsfromnow');
    $issueendsign        = xarModGetVar('ebulletin', 'issueendsign');
    $msglimit            = xarModGetVar('ebulletin', 'msglimit');
    $msgunit             = xarModGetVar('ebulletin', 'msgunit');
    $requirevalidation   = xarModGetVar('ebulletin', 'requirevalidation');

    // get other vars
    $themes = xarModAPIFunc('themes', 'admin', 'getlist', array('Class' => 0));
    $authid = xarSecGenAuthKey();
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall');

    // message units
    $msgunits = array();
    $msgunits['minute'] = xarML('Minute');
    $msgunits['hour']   = xarML('Hour');
    $msgunits['day']    = xarML('Day');
    $msgunits['week']   = xarML('Week');
    $msgunits['month']  = xarML('Minute');

    // issue units
    $units = array();
    $units[] = array('days',   xarML('Days'));
    $units[] = array('weeks',  xarML('Weeks'));
    $units[] = array('months', xarML('Months'));
    $units[] = array('years',  xarML('Years'));

    // get signs
    $signs = array();
    $signs[] = array('before', xarML('before'));
    $signs[] = array('after', xarML('after'));

    // initialize template data array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // add module vars to template
    $data['admin_issues']      = $admin_issuesperpage;
    $data['admin_subs']        = $admin_subsperpage;
    $data['supportshorturls']  = $supportshorturls;
    $data['usemodulealias']    = $usemodulealias;
    $data['aliasname']         = $aliasname;
    $data['template_dir']      = $template_dir;
    $data['theme']             = $theme;
    $data['issuenumsago']      = $issuenumsago;
    $data['issueunitsago']     = $issueunitsago;
    $data['issuestartsign']    = $issuestartsign;
    $data['issuenumsfromnow']  = $issuenumsfromnow;
    $data['issueunitsfromnow'] = $issueunitsfromnow;
    $data['issueendsign']      = $issueendsign;
    $data['msglimit']          = $msglimit;
    $data['msgunit']           = $msgunit;
    $data['requirevalidation'] = $requirevalidation;

    // add other vars to template
    $data['themes']   = $themes;
    $data['authid']   = $authid;
    $data['pubs']     = $pubs;
    $data['units']    = $units;
    $data['signs']    = $signs;
    $data['msgunits'] = $msgunits;

    // get modifyconfig hooks
    $data['hookoutput'] = xarModCallHooks('module', 'modifyconfig', 'ebulletin',
        array('module' => 'ebulletin')
    );

    return $data;
}

?>
