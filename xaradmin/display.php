<?php
/**
 * AccessMethods Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AccessMethods Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego
 */
function dossier_admin_display($args)
{
    extract($args);
    if (!xarVarFetch('contactid', 'id', $contactid)) return;
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'dossier',
                        'filename' => 'spinner.js'));

    $data = xarModAPIFunc('dossier','admin','menu');
    $data['contactid'] = $contactid;

    $item = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $contactid));

//    if (!isset($item)) {
    if (!xarSecurityCheck('TeamDossierAccess', 0, 'Contact', $item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid'])) {
        $msg = xarML('Not authorized to access this #(1) item',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg.'<br />'.$item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid']));
        return $msg;
    }
    
    $data['status'] = '';
    if($item['sortname'] == "[contact deleted]") {
        $data['status'] = $item['sortname'];
    }
    
    $data['item'] = $item;
    $data['authid'] = xarSecGenAuthKey('dossier');
    
    $data['addressform'] = xarModFunc('dossier', 'locations', 'new', array('contactid' => $contactid));
    
    $data['addresslist'] = xarModAPIFunc('dossier', 'locations', 'getallcontact', array('contactid' => $contactid));
    
    $data['reminders'] = xarModAPIFunc('dossier', 'reminders', 'getallcontact', array('contactid' => $contactid));
    
    $data['contactlogs'] = xarModAPIFunc('dossier', 'logs', 'getallcontact', array('contactid' => $contactid));
    
    $relationships = xarModAPIFunc('dossier', 'relationships', 'getall', array('contactid' => $contactid));
    
    if($relationships === false) return;
    
    $data['relationships'] = $relationships;
    
    $data['hookoutput'] = array();

    $hooks = xarModCallHooks('item',
                             'display',
                             $contactid,
                             xarModURL('dossier',
                                       'admin',
                                       'display',
                                       array('contactid' => $contactid)));
    if (!empty($hooks)) {
        $data['hookoutput'] = $hooks;
    }
    
    return $data;
}
?>
