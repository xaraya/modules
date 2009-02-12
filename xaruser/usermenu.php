<?php
/**
 * Display the user menu
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param $phase is the which part of the loop you are on
 *
 */
function dossier_user_usermenu($args)
{
    extract($args);
    
    $uid = xarUserGetVar('uid');

    if (!xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "All:$uid:All:All")) return;


    if(!xarVarFetch('phase','str', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('contactid','str::', $contactid, 0, XARVAR_NOT_REQUIRED)) {return;}
    
    if (!xarVarFetch('cat_id', 'int::', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('address_1', 'str::', $address_1, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('address_2', 'str::', $address_2, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('city', 'str::', $city, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('us_state', 'str::', $us_state, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('postalcode', 'str::', $postalcode, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('country', 'str::', $country, '', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {
        case 'menu':

            $icon = 'modules/dossier/xarimages/admin.gif';
            $current = xarModURL('roles', 'user', 'account', array('moduleload' => 'dossier'));

            $data = xarTplModule('dossier','user', 'usermenu_icon', array('iconbasic'    => $icon, 'current' => $current));

            return $data;
    
            break;

        case 'form':
            if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;

            $name = xarUserGetVar('name');
            
            $defaultcontactid = xarModGetUserVar('dossier', 'defaultcontactid');

            
            
            $authid = xarSecGenAuthKey('dossier');
            $uid = xarSessionGetVar('uid');

            $submitlabel = xarML('Submit');

            $data = xarTplModule('dossier','user', 'usermenu_form', array('authid'      => $authid,
                                                                          'contactid'   => $contactid,
                                                                          'submitlabel' => $submitlabel,
                                                                          'name'        => $name,
                                                                          'uid'         => $uid,
                                                                          'returnurl'   => $returnurl));
            return $data;
            
            break;

        case 'update':
            if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cat_id', 'id', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('userid', 'id', $userid, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactcode', 'str:1:', $contactcode, $contactcode, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('prefix', 'str:1:', $prefix, $prefix, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lname', 'str::', $lname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fname', 'html:basic', $fname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sortname', 'str:1:', $sortname, $sortname, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dateofbirth', 'str::', $dateofbirth, $dateofbirth, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('title', 'str:1:', $title, $title, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('company', 'str::', $company, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sortcompany', 'str::', $sortcompany, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('img', 'str::', $img, $img, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_work', 'isset::', $phone_work, $phone_work, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_cell', 'isset::', $phone_cell, $phone_cell, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_fax', 'isset::', $phone_fax, $phone_fax, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_home', 'isset::', $phone_home, $phone_home, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email_1', 'str::', $email_1, $email_1, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email_2', 'str::', $email_2, $email_2, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_AIM', 'str::', $chat_AIM, $chat_AIM, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_YIM', 'str::', $chat_YIM, $chat_YIM, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_MSNM', 'str::', $chat_MSNM, $chat_MSNM, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_ICQ', 'str::', $chat_ICQ, $chat_ICQ, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactpref', 'str::', $contactpref, $contactpref, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('notes', 'str::', $notes, $notes, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
        
            if (is_array($phone_work)) {
                if (!empty($phone_work['NPA']) && !empty($phone_work['NXX']) && !empty($phone_work['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_work['NXX']) && is_numeric($phone_work['phone']) &&
                        strlen($phone_work['NXX']) == 3 && strlen($phone_work['NXX']) == 3 && strlen($phone_work['phone']) == 4) {
                        
                        if(is_numeric($phone_work['extension']) && strlen($phone_work['extension']) > 0) {
                            $phone_work = sprintf('%03d-%03d-%04d x%04d',$phone_work['NPA'],$phone_work['NXX'],$phone_work['phone'],$phone_work['extension']);
                        } else {
                            $phone_work = sprintf('%03d-%03d-%04d',$phone_work['NPA'],$phone_work['NXX'],$phone_work['phone']);
                        }
                        
                    } else {
                        $phone_work = $phone_work['NPA']."-".$phone_work['NXX']."-".$phone_work['phone'];
                    }
                } else {
                    $phone_work = xarML('invalid');
                }
            }
        
            if (is_array($phone_cell)) {
                if (!empty($phone_cell['NPA']) && !empty($phone_cell['NXX']) && !empty($phone_cell['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_cell['NXX']) && is_numeric($phone_cell['phone']) &&
                        strlen($phone_cell['NXX']) == 3 && strlen($phone_cell['NXX']) == 3 && strlen($phone_cell['phone']) == 4) {
                        
                        if(is_numeric($phone_cell['extension']) && strlen($phone_cell['extension']) > 0) {
                            $phone_cell = sprintf('%03d-%03d-%04d x%04d',$phone_cell['NPA'],$phone_cell['NXX'],$phone_cell['phone'],$phone_cell['extension']);
                        } else {
                            $phone_cell = sprintf('%03d-%03d-%04d',$phone_cell['NPA'],$phone_cell['NXX'],$phone_cell['phone']);
                        }
                        
                    } else {
                        $phone_cell = $phone_cell['NPA']."-".$phone_cell['NXX']."-".$phone_cell['phone'];
                    }
                } else {
                    $phone_cell = xarML('invalid');
                }
            }
        
            if (is_array($phone_fax)) {
                if (!empty($phone_fax['NPA']) && !empty($phone_fax['NXX']) && !empty($phone_fax['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_fax['NXX']) && is_numeric($phone_fax['phone']) &&
                        strlen($phone_fax['NXX']) == 3 && strlen($phone_fax['NXX']) == 3 && strlen($phone_fax['phone']) == 4) {
                        
                        if(is_numeric($phone_fax['extension']) && strlen($phone_fax['extension']) > 0) {
                            $phone_fax = sprintf('%03d-%03d-%04d x%04d',$phone_fax['NPA'],$phone_fax['NXX'],$phone_fax['phone'],$phone_fax['extension']);
                        } else {
                            $phone_fax = sprintf('%03d-%03d-%04d',$phone_fax['NPA'],$phone_fax['NXX'],$phone_fax['phone']);
                        }
                        
                    } else {
                        $phone_fax = $phone_fax['NPA']."-".$phone_fax['NXX']."-".$phone_fax['phone'];
                    }
                } else {
                    $phone_fax = xarML('invalid');
                }
            }
        
            if (is_array($phone_home)) {
                if (!empty($phone_home['NPA']) && !empty($phone_home['NXX']) && !empty($phone_home['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_home['NXX']) && is_numeric($phone_home['phone']) &&
                        strlen($phone_home['NXX']) == 3 && strlen($phone_home['NXX']) == 3 && strlen($phone_home['phone']) == 4) {
                        
                        if(is_numeric($phone_home['extension']) && strlen($phone_home['extension']) > 0) {
                            $phone_home = sprintf('%03d-%03d-%04d x%04d',$phone_home['NPA'],$phone_home['NXX'],$phone_home['phone'],$phone_home['extension']);
                        } else {
                            $phone_home = sprintf('%03d-%03d-%04d',$phone_home['NPA'],$phone_home['NXX'],$phone_home['phone']);
                        }
                        
                    } else {
                        $phone_home = $phone_home['NPA']."-".$phone_home['NXX']."-".$phone_home['phone'];
                    }
                } else {
                    $phone_home = xarML('invalid');
                }
            }
            
            if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'view');
            
            extract($args);
            if (!xarSecConfirmAuthKey()) return;
            if(!xarModAPIFunc('dossier',
        					'admin',
        					'update',
        					array('contactid'	=> $contactid,
                                'userid'	    => $userid,
                                'private'	    => $private,
                                'contactcode'	=> $contactcode,
                                'prefix'	    => $prefix,
                                'lname'	        => $lname,
                                'fname'	        => $fname,
                                'sortname'	    => $sortname,
                                'dateofbirth'   => $dateofbirth,
                                'title'		    => $title,
                                'company'	    => $company,
                                'sortcompany'	=> $sortcompany,
                                'img'	        => $img,
                                'phone_work'	=> $phone_work,
                                'phone_cell'	=> $phone_cell,
                                'phone_fax'	    => $phone_fax,
                                'phone_home'	=> $phone_home,
                                'email_1'	    => $email_1,
                                'email_2'	    => $email_2,
                                'chat_AIM'	    => $chat_AIM,
                                'chat_YIM'	    => $chat_YIM,
                                'chat_MSNM'	    => $chat_MSNM,
                                'chat_ICQ'	    => $chat_ICQ,
                                'contactpref'	=> $contactpref,
                                'notes'	        => $notes))) {
        		return;
        	}
        
        
        	xarSessionSetVar('statusmsg', xarML('Contact Updated'));
            
            if (!xarVarFetch('locationid', 'id', $locationid, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cat_id', 'int::', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('address_1', 'str::', $address_1, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('address_2', 'str::', $address_2, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('city', 'str::', $city, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('us_state', 'str::', $us_state, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postalcode', 'str::', $postalcode, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('country', 'str::', $country, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('latitude', 'str::', $latitude, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('longitude', 'str::', $longitude, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('startdate', 'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('enddate', 'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;
        
            if(is_numeric($locationid) && $locationid > 0) {
            
                if(!xarModAPIFunc('dossier',
                                'locations',
                                'update',
                                array('locationid'      => $locationid,
                                    'cat_id'            => $cat_id,
                                    'address_1'         => $address_1,
                                    'address_2'         => $address_2,
                                    'city'              => $city,
                                    'us_state'          => $us_state,
                                    'postalcode'        => $postalcode,
                                    'country'           => $country,
                                    'latitude'          => $latitude,
                                    'longitude'         => $longitude))) {
                    return;
                }
            } elseif(!empty($address_1)) {

                $locationid = xarModAPIFunc('dossier',
                                    'locations',
                                    'create',
                                    array('cat_id'            => $cat_id,
                                        'address_1'         => $address_1,
                                        'address_2'         => $address_2,
                                        'city'              => $city,
                                        'us_state'          => $us_state,
                                        'postalcode'        => $postalcode,
                                        'country'           => $country,
                                        'latitude'          => $latitude,
                                        'longitude'         => $longitude));

                if(!xarModAPIFunc('dossier',
                                    'locations',
                                    'createdata',
                                    array('contactid'            => $contactid,
                                        'locationid'         => $locationid))) {
                    return;
                }
            
                
            }

            if (is_numeric($locationid) && $locationid > 0) {
                xarModAPIFunc('dossier', 'locations', 'setdefault', array('contactid' => $contactid, 'locationid' => $locationid));
            }

            xarResponseRedirect($returnurl);
        
            return true;

            break;

        case 'create':
            if (!xarVarFetch('cat_id', 'id', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('ownerid', 'id', $ownerid, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('userid', 'id', $userid, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactcode', 'str:1:', $contactcode, $contactcode, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('prefix', 'str:1:', $prefix, $prefix, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lname', 'str::', $lname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fname', 'html:basic', $fname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sortname', 'str:1:', $sortname, $sortname, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dateofbirth', 'str::', $dateofbirth, $dateofbirth, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('title', 'str:1:', $title, $title, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('company', 'str::', $company, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sortcompany', 'str::', $sortcompany, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('img', 'str::', $img, $img, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_work', 'isset::', $phone_work, $phone_work, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_cell', 'isset::', $phone_cell, $phone_cell, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_fax', 'isset::', $phone_fax, $phone_fax, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phone_home', 'isset::', $phone_home, $phone_home, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email_1', 'str::', $email_1, $email_1, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email_2', 'str::', $email_2, $email_2, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_AIM', 'str::', $chat_AIM, $chat_AIM, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_YIM', 'str::', $chat_YIM, $chat_YIM, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_MSNM', 'str::', $chat_MSNM, $chat_MSNM, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('chat_ICQ', 'str::', $chat_ICQ, $chat_ICQ, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactpref', 'str::', $contactpref, $contactpref, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('notes', 'str::', $notes, $notes, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
            if (is_array($phone_work)) {
                if (!empty($phone_work['NPA']) && !empty($phone_work['NXX']) && !empty($phone_work['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_work['NXX']) && is_numeric($phone_work['phone']) &&
                        strlen($phone_work['NXX']) == 3 && strlen($phone_work['NXX']) == 3 && strlen($phone_work['phone']) == 4) {
                        
                        if(is_numeric($phone_work['extension']) && strlen($phone_work['extension']) > 0) {
                            $phone_work = sprintf('%03d-%03d-%04d x%04d',$phone_work['NPA'],$phone_work['NXX'],$phone_work['phone'],$phone_work['extension']);
                        } else {
                            $phone_work = sprintf('%03d-%03d-%04d',$phone_work['NPA'],$phone_work['NXX'],$phone_work['phone']);
                        }
                        
                    } else {
                        $phone_work = $phone_work['NPA']."-".$phone_work['NXX']."-".$phone_work['phone'];
                    }
                } else {
                    $phone_work = xarML('invalid');
                }
            }
        
            if (is_array($phone_cell)) {
                if (!empty($phone_cell['NPA']) && !empty($phone_cell['NXX']) && !empty($phone_cell['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_cell['NXX']) && is_numeric($phone_cell['phone']) &&
                        strlen($phone_cell['NXX']) == 3 && strlen($phone_cell['NXX']) == 3 && strlen($phone_cell['phone']) == 4) {
                        
                        if(is_numeric($phone_cell['extension']) && strlen($phone_cell['extension']) > 0) {
                            $phone_cell = sprintf('%03d-%03d-%04d x%04d',$phone_cell['NPA'],$phone_cell['NXX'],$phone_cell['phone'],$phone_cell['extension']);
                        } else {
                            $phone_cell = sprintf('%03d-%03d-%04d',$phone_cell['NPA'],$phone_cell['NXX'],$phone_cell['phone']);
                        }
                        
                    } else {
                        $phone_cell = $phone_cell['NPA']."-".$phone_cell['NXX']."-".$phone_cell['phone'];
                    }
                } else {
                    $phone_cell = xarML('invalid');
                }
            }
        
            if (is_array($phone_fax)) {
                if (!empty($phone_fax['NPA']) && !empty($phone_fax['NXX']) && !empty($phone_fax['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_fax['NXX']) && is_numeric($phone_fax['phone']) &&
                        strlen($phone_fax['NXX']) == 3 && strlen($phone_fax['NXX']) == 3 && strlen($phone_fax['phone']) == 4) {
                        
                        if(is_numeric($phone_fax['extension']) && strlen($phone_fax['extension']) > 0) {
                            $phone_fax = sprintf('%03d-%03d-%04d x%04d',$phone_fax['NPA'],$phone_fax['NXX'],$phone_fax['phone'],$phone_fax['extension']);
                        } else {
                            $phone_fax = sprintf('%03d-%03d-%04d',$phone_fax['NPA'],$phone_fax['NXX'],$phone_fax['phone']);
                        }
                        
                    } else {
                        $phone_fax = $phone_fax['NPA']."-".$phone_fax['NXX']."-".$phone_fax['phone'];
                    }
                } else {
                    $phone_fax = xarML('invalid');
                }
            }
        
            if (is_array($phone_home)) {
                if (!empty($phone_home['NPA']) && !empty($phone_home['NXX']) && !empty($phone_home['phone'])) {
                    if (is_numeric($phone_work['NPA']) && is_numeric($phone_home['NXX']) && is_numeric($phone_home['phone']) &&
                        strlen($phone_home['NXX']) == 3 && strlen($phone_home['NXX']) == 3 && strlen($phone_home['phone']) == 4) {
                        
                        if(is_numeric($phone_home['extension']) && strlen($phone_home['extension']) > 0) {
                            $phone_home = sprintf('%03d-%03d-%04d x%04d',$phone_home['NPA'],$phone_home['NXX'],$phone_home['phone'],$phone_home['extension']);
                        } else {
                            $phone_home = sprintf('%03d-%03d-%04d',$phone_home['NPA'],$phone_home['NXX'],$phone_home['phone']);
                        }
                        
                    } else {
                        $phone_home = $phone_home['NPA']."-".$phone_home['NXX']."-".$phone_home['phone'];
                    }
                } else {
                    $phone_home = xarML('invalid');
                }
            }
        
            if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'view');
        
            extract($args);
            if (!xarSecConfirmAuthKey()) return;
        
            $contactid = xarModAPIFunc('dossier',
                                'admin',
                                'create',
                                array('cat_id' 	    => $cat_id,
                                    'ownerid'	    => $ownerid,
                                    'userid'	    => $userid,
                                    'private'	    => $private,
                                    'contactcode'	=> $contactcode,
                                    'prefix'	    => $prefix,
                                    'lname'	        => $lname,
                                    'fname'	        => $fname,
                                    'sortname'	    => $sortname,
                                    'dateofbirth'	=> $dateofbirth,
                                    'title'		    => $title,
                                    'company'	    => $company,
                                    'sortcompany'	=> $sortcompany,
                                    'img'	        => $img,
                                    'phone_work'	=> $phone_work,
                                    'phone_cell'	=> $phone_cell,
                                    'phone_fax'	    => $phone_fax,
                                    'phone_home'	=> $phone_home,
                                    'email_1'	    => $email_1,
                                    'email_2'	    => $email_2,
                                    'chat_AIM'	    => $chat_AIM,
                                    'chat_YIM'	    => $chat_YIM,
                                    'chat_MSNM'	    => $chat_MSNM,
                                    'chat_ICQ'	    => $chat_ICQ,
                                    'contactpref'	=> $contactpref,
                                    'notes'	        => $notes));
        
        
            if (!isset($contactid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        
            xarSessionSetVar('statusmsg', xarMLByKey('CONTACTCREATED'));
            
            if (!xarVarFetch('locationid', 'id', $locationid, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cat_id', 'int::', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('address_1', 'str::', $address_1, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('address_2', 'str::', $address_2, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('city', 'str::', $city, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('us_state', 'str::', $us_state, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postalcode', 'str::', $postalcode, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('country', 'str::', $country, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('latitude', 'str::', $latitude, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('longitude', 'str::', $longitude, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('startdate', 'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('enddate', 'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;
        
            if(!empty($address_1)) {

                $locationid = xarModAPIFunc('dossier',
                                    'locations',
                                    'create',
                                    array('cat_id'            => $cat_id,
                                        'address_1'         => $address_1,
                                        'address_2'         => $address_2,
                                        'city'              => $city,
                                        'us_state'          => $us_state,
                                        'postalcode'        => $postalcode,
                                        'country'           => $country,
                                        'latitude'          => $latitude,
                                        'longitude'         => $longitude));
                if($locationid == false) return;
                if(!xarModAPIFunc('dossier',
                                    'locations',
                                    'createdata',
                                    array('contactid'            => $contactid,
                                        'locationid'         => $locationid))) {
                    return;
                }
            
                
            }

            if (is_numeric($locationid) && $locationid > 0) {
                xarModAPIFunc('dossier', 'locations', 'setdefault', array('contactid' => $contactid, 'locationid' => $locationid));
            }

            xarResponseRedirect($returnurl);
            
            return true;

            break;

        case 'delete':
            xarResponseRedirect(xarModURL('dossier','admin','delete',array('contactid' => $contactid)));
            return true;
//            return xarModFunc('dossier','admin','delete');

            break;
    }

    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return;
}

?>
