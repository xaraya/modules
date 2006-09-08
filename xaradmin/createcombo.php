<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_createcombo($args)
{
    extract($args);
    
      
    
//    if (!xarVarFetch('project_name', 'str:1:', $project_name, $project_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'html:basic', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'id', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projecttype', 'str::', $projecttype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planned_end_date', 'str::', $planned_end_date, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('estimate', 'str::', $estimate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newcontact', 'checkbox', $newcontact, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fname', 'str::', $fname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lname', 'str::', $lname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('company', 'str::', $company, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newcompany', 'str::', $newcompany, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str::', $title, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact_1', 'str::', $contact_1, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact_2', 'str::', $contact_2, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('c_label_1', 'int::', $c_label_1, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('c_label_2', 'int::', $c_label_2, 0, XARVAR_NOT_REQUIRED)) return;
    
    include_once ('modules/addressbook/xarglobal.php');

    xarModAPILoad('addressbook','user');

    if($newcontact) {
        if(empty($newcompany)) $newcompany = $company;
        $clientid = xarModAPIFunc('addressbook',
                            'user',
                            'insertrecord',
                            array('fname' 	    => $fname,
                                'lname' 	    => $lname,
                                'title'	        => $title,
                                'company'	    => $newcompany,
                                'contact_1'	    => $contact_1,
                                'contact_2'	    => $contact_2,
                                'contact_3'	    => '',
                                'contact_4'	    => '',
                                'contact_5'	    => '',
                                'c_label_1'	    => 1,
                                'c_label_2'		=> 5,
                                'c_label_3'		=> 2,
                                'c_label_4'		=> 3,
                                'c_label_5'		=> 4,
                                'zip'		    => '',
                                'city'		    => '',
                                'address_1'		=> '',
                                'address_2'		=> '',
                                'state'		    => '',
                                'country'		=> '',
                                'note'		    => '',
                                'prfx'		    => '',
                                'cat_id'		=> 0,
                                'img'		    => '',
                                'c_main'		=> 1,
                                'user_id'		=> xarSessionGetVar('uid')));
    }
    $clientinfo = xarModAPIFunc('addressbook','user','getDetailValues',array('id'=>$clientid));
    if($clientinfo == false) return;
    $projects_objectid = xarModGetVar('xproject','projects_objectid');
    xarModAPILoad('dynamicdata','user');
    list($properties) = xarModAPIFunc('dynamicdata','user','getitemfordisplay',array('module'=>"xProject",'itemtype'=>"1",'itemid'=>$projects_objectid));
    $properties['projecttype']->setValue($projecttype);
    $strprojecttype = $properties['projecttype']->showOutput();
    $project_name = $clientinfo['company'] . " " . $strprojecttype;
    
    if (!xarSecConfirmAuthKey()) return;

    $projectid = xarModAPIFunc('xproject',
                        'admin',
                        'create',
                        array('project_name' 	=> $project_name,
                            'reference' 	    => '',
                            'private'	        => '',
                            'description'	    => $description,
                            'clientid'	        => $clientid,
                            'ownerid'	        => $ownerid,
                            'status'	        => 'Draft',
                            'priority'		    => 5,
                            'importance'		=> 5,
                            'projecttype'	    => $projecttype,
                            'date_approved'	    => NULL,
                            'planned_start_date'=> NULL,
                            'planned_end_date'	=> $planned_end_date,
                            'actual_start_date' => NULL,
                            'actual_end_date'	=> NULL,
                            'hours_planned'     => NULL,
                            'hours_spent'		=> NULL,
                            'hours_remaining'	=> NULL,
                            'estimate'	        => $estimate,
                            'budget'	        => 0,
                            'associated_sites'	=> NULL));


    if (!isset($projectid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'modify', array('projectid' => $projectid)));

    return true;
}

?>