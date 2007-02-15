<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * @param
 * @return array
 */
function uploads_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('mimetype',    'int:0:', $mimetype,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('subtype',     'int:0:', $subtype,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('userId',      'int:0:', $userId,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('fileId',      'int:0:', $fileId,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('userName',    'isset',  $userName,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('apply',       'isset',  $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',      'isset',  $extpid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extname',     'isset',  $extname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extrealm',    'isset',  $extrealm,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extmodule',   'isset',  $extmodule,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extcomponent','isset',  $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance', 'isset',  $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',    'isset',  $extlevel,     NULL, XARVAR_DONT_SET)) {return;}

    $userNameList = array();

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $mimetype = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $subtype = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $userId = $parts[2];
        if (count($parts) > 3 && !empty($parts[3])) $fileId = $parts[3];
    }

    // Check the mimetype to see if it's set and, if not assume 'All'
    // Otherwise do a quick check to make sure this user has access
    if (empty($mimetype) || !is_numeric($mimetype)) {
        $mimetype = 0;
        if (!xarSecurityCheck('AdminUploads')) return;
    } else {
        if (!xarSecurityCheck('AdminUploads',1,'Upload',"$mimetype:All:All:All")) return;
    }

    // Check to see if subtype is set, if not assume 'All'
    if (empty($subtype) || $subtype == 'All' || !is_numeric($subtype)) {
        $subtype = 0;
    } else {
        $subtypeInfo = xarModAPIFunc('mime', 'user', 'get_subtype', array('subtypeId' => $subtype));

        if (empty($subtypeInfo) || $subtypeInfo['typeId'] != $mimetype) {
            $subtype = 0;
        }
        unset($subtypeInfo);
    }

    // Here we check for the userId (which is based on a list of users
    // that have submitted files - otherwise, a specific username is entered
    // if that no Id is selected but a username was entered, find the id for it
    // and go with that :)

    if (!empty($userName)) {
        if (!strcasecmp('myself', $userName)) {
            $userId = 'myself';
        } else {
            $user = xarModAPIFunc('roles', 'user', 'get',
                            array('uname' => $userName));
            if (!empty($user)) {
                $userNameList[$user['uid']]['userId'] = $user['uid'];
                $userNameList[$user['uid']]['userName'] = $user['uname'];
                $userId = $user['uid'];
                $userName = '';
            } else {
                $userName = '';
            }
        }
    }

    if (empty($userId) || $userId == 'All' || !is_numeric($userId)) {
        if (!strcasecmp('myself', $userName)) {
            $userId = 'myself';
        } else {
            $userId = 0;
        }
    } else {
        $user = xarModAPIFunc('roles', 'user', 'get',
                            array('uid' => $userId));
        if (!empty($user)) {
            $userNameList[$user['uid']]['userId'] = $user['uid'];
            $userNameList[$user['uid']]['userName'] = $user['uname'];
            $userId = $user['uid'];
            $userName = '';
        } else {
            $userName = '';
        }
    }

    // Again, if the Id is not specified, assume 'All'
    // however, if it is set - make sure it's mime type matches up with the
    // currently selected mimetype / subtype - otherwise, switch to All files
    if (empty($fileId) || $fileId == 'All' || !is_numeric($fileId)) {
        $fileId = 0;
    } else {
        $fileInfo = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId));

        if (isset($fileInfo[$fileId])) {

            $fileTypeInfo =& $fileInfo[$fileId]['fileTypeInfo'];

            // If the mimetype is the same and the subtype is either
            // the same or ALL (0) then add the file to the list
            // otherwise reset the fileId to ALL (0)
            if (($fileTypeInfo['typeId'] == $mimetype || $mimetype == 0) &&
                ($fileTypeInfo['subtypeId'] == $subtype || $subtype == 0)) {
                    $fileList = $fileInfo;
            } else {
                $fileId = 0;
            }

        } else {
            $fileId = 0;
        }
    }

    // define the filters for creating the select boxes
    // as well as for generating the filters used for 'count items'
    $filters['mimetype'] = $mimetype;
    $filters['subtype']  = $subtype;

    // define the new instance
    $newinstance    = array();
    $newinstance[]  = empty($mimetype)   ? 'All' : $mimetype;
    $newinstance[]  = empty($subtype)    ? 'All' : $subtype;
    $newinstance[]  = empty($userId)     ? 'All' : $userId;
    $newinstance[]  = empty($fileId)     ? 'All' : $fileId;

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

    $filters['storeOptions'] = FALSE;
    $options            = xarModAPIFunc('uploads', 'user', 'process_filters', $filters);
    unset($filters);

    $filter             = $options['filter'];
    $filter['userId']   = $userId;
    $filter['fileId']   = $fileId;

    $instances = $options['data']['filters'];

    // Count how many items there are based on
    // the currently selected privilege settings
    $numitems = xarModAPIFunc('uploads', 'user', 'db_count', $filter);

    $userNameList += xarModAPIFunc('uploads','user','db_get_users',
                                    array('mimeType' => $filter['fileType']));

    // Set up default 'All' option for users
    $userNameList[0]['userId'] = 0;
    $userNameList[0]['userName'] = xarML('All');

    if (isset($userNameList[$userId])) {
        $userNameList[$userId]['selected'] = TRUE;
    }

    // We don't need the userid nor the fileid for
    // retrieving a list of files - in this particular instance
    // we are only retrieving the list of files based on mimetype
    unset($filter['userId']);
    unset($filter['fileId']);

    $fileList = xarModAPIFunc('uploads', 'user', 'db_get_file', $filter);
    $fileList[0]['fileId'] = 0;
    $fileList[0]['fileName'] = xarML('All');
    $fileList[0]['fileLocation'] = $fileList[0]['fileName'];

    if (isset($fileList[$fileId])) {
        $fileList[$fileId]['selected'] = TRUE;
    }


    ksort($fileList);
    ksort($userNameList);

    if (!empty($userName) && isset($userNamelist[$userId])) {
        $userName = '';
    }

    $data['fileId']         = $fileId;
    $data['fileList']       = $fileList;
    $data['mimetype']       = $mimetype;
    $data['mimetypeList']   = $instances['mimetypes'];
    $data['subtype']        = $subtype;
    $data['subtypeList']    = $instances['subtypes'];
    $data['userId']         = $userId;
    $data['userName']       = xarVarPrepForDisplay($userName);
    $data['userNameList']   = $userNameList;
    $data['numitems']       = $numitems;
    $data['extpid']         = $extpid;
    $data['extname']        = $extname;
    $data['extrealm']       = $extrealm;
    $data['extmodule']      = $extmodule;
    $data['extcomponent']   = $extcomponent;
    $data['extlevel']       = $extlevel;
    $data['extinstance']    = xarVarPrepForDisplay(join(':',$newinstance));
    $data['refreshlabel']   = xarML('Refresh');
    $data['applylabel']     = xarML('Finish and Apply to Privilege');

    return $data;

}

?>