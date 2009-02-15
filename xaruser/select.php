<?php
/**
 * DOSSIER user functions
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>
 * Based on labDossier (on PostNuke) by Chad Kraeft <cdavidkraeft@miragelab.com>
 */

function dossier_user_select($args)
{
    extract($args);

    if (!xarVarFetch('fieldname', 'str:1:', $fieldname)) return;
    if (!xarVarFetch('fieldid', 'int', $fieldid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('size', 'int', $size, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactid', 'isset', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('multiple', 'str', $multiple, $multiple, XARVAR_NOT_REQUIRED)) return;

    $data = array();
    $companylist = array();
    $optionlist = array();
    if(!isset($company) && !is_int($contactid)) $company = $contactid;
    
    $uid = xarUserGetVar('uid');
    
    if((!empty($company) || $company == " ") && $company != "[blank]" && $company != "select_company") {
        if(xarSecurityCheck('AuditDossier', 0, 'Contact')) {
            $optionlist = xarModAPIFunc('dossier','user','getall',array('company'=>$company, 'sortby' => "sortname"));
            if($optionlist === false) return 1;
        } elseif(xarSecurityCheck('TeamDossierAccess', 0, 'Contact', "All:All:All:".$uid)) {
            $optionlist = xarModAPIFunc('dossier','user','getall',array('company'=>$company, 'sortby' => "sortname", 'agentuid' => $uid));
            if($optionlist === false) return 2;
        } elseif(xarSecurityCheck('ClientDossierAccess', 0, 'Contact', "All:".$uid.":All:All")) {
            $optionlist = xarModAPIFunc('dossier','user','getall',array('company'=>$company, 'sortby' => "sortname", 'userid' => $uid));
            if($optionlist === false) return 3;
        }
    
        if (!isset($optionlist)) $optionlist = array();
    } else {
        if(xarSecurityCheck('AuditDossier', 0, 'Contact')) {
            $companylist = xarModAPIFunc('dossier', 'user', 'getcompanies');
            if($companylist === false) return 4;
        } elseif(xarSecurityCheck('TeamDossierAccess', 0, 'Contact', "All:All:All:".$uid)) {
            $companylist = xarModAPIFunc('dossier', 'user', 'getcompanies', array('agentuid' => $uid));
            if($companylist === false) return 5;
        } elseif(xarSecurityCheck('ClientDossierAccess', 0, 'Contact', "All:".$uid.":All:All")) {
            $companylist = xarModAPIFunc('dossier', 'user', 'getcompanies', array('userid' => $uid));
            if($companylist === false) return 6;
        }
    }
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.pack.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'jquery',
                        'filename' => 'jquery.compat-1.1.js'));
    
    xarModAPIFunc('base','javascript','modulefile',
                  array('module' => 'dossier',
                        'filename' => 'jquerycontactlist.js'));
                        
    $data['company'] = $company ? $company : "";
    $data['contactid'] = $contactid ? $contactid : "";
    $data['options'] = $optionlist;
    $data['companylist'] = $companylist;
//    $data['company'] = $company;
    $data['multiple'] = $multiple ? $multiple : "";
    $data['fieldname'] = $fieldname;
    $data['fieldid'] = $fieldid;
    $data['size'] = $size;
    
    return $data;
} // END main

?>
