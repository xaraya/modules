<?php

function release_user_addnotes()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'getmodule';
    }
             //Set the stateoptions array for rstate field
                $stateoptions=array();
                $stateoptions[0] = xarML('Planning');
                $stateoptions[1] = xarML('Alpha');
                $stateoptions[2] = xarML('Beta');
                $stateoptions[3] = xarML('Production/Stable');
                $stateoptions[4] = xarML('Mature');
                $stateoptions[5] = xarML('Inactive');
                $data['stateoptions']=$stateoptions;

    switch(strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'addnote_getmodule', array('authid'    => $authid));

            break;

        case 'start':
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $rid = xarVarCleanFromInput('rid');

            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $rid));

            
            $uid = xarUserGetVar('uid');

            if (($data['uid'] == $uid) or (xarSecurityCheck('EditRelease', 0))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add a release notification to this module');               
            }

            //TODO FIX ME!!!
            if (empty($data['name'])){
                $message = xarML('There is no assigned ID for your module or theme.');
            }

            xarTplSetPageTitle(xarVarPrepForDisplay($data['name']));

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'addnote_start', array('rid'       => $data['rid'],
                                                                          'name'      => $data['name'],
                                                                          'desc'      => $data['desc'],
                                                                          'message'   => $message,
                                                                          'authid'    => $authid));

            break;

        case 'getbasics':

           list($rid,
                $name) = xarVarCleanFromInput('rid',
                                              'name');

           //if (!xarSecConfirmAuthKey()) return;


            xarTplSetPageTitle(xarVarPrepForDisplay($name));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getbasics', array('rid'       => $rid,
                                                                             'name'     => $name,
                                                                             'authid'   => $authid));
            break;

        case 'getdetails':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck) = xarVarCleanFromInput('rid',
                                                    'name',
                                                    'version',
                                                    'pricecheck',
                                                    'supportcheck',
                                                    'democheck');
            
           //if (!xarSecConfirmAuthKey()) return;

            xarTplSetPageTitle(xarVarPrepForDisplay($name));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getdetails', array('rid'         => $rid,
                                                                              'name'        => $name,
                                                                              'authid'      => $authid,
                                                                              'version'     => $version,
                                                                              'pricecheck'  => $pricecheck,
                                                                              'supportcheck' => $supportcheck,
                                                                              'democheck'    => $democheck,
                                                                              'stateoptions' => $stateoptions));

            break;
        
        case 'preview':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck,
                 $dllink,
                 $price,
                 $demolink,
                 $supportlink,
                 $changelog,
                 $notes,
                 $rstate) = xarVarCleanFromInput('rid',
                                                'name',
                                                'version',
                                                'pricecheck',
                                                'supportcheck',
                                                'democheck',
                                                'dllink',
                                                'price',
                                                'demolink',
                                                'supportlink',
                                                'changelog',
                                                'notes',
                                                'rstate');
            
           //if (!xarSecConfirmAuthKey()) return;
           //Get some info for the extensions state
           foreach ($stateoptions as $key => $value) {
               if ($key==$rstate) {
                   $extstate=$stateoptions[$key];
               }
           }

           $notesf = nl2br($notes);
           $changelogf = nl2br($changelog);

            xarTplSetPageTitle(xarVarPrepForDisplay($name));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_preview',    array('rid'         => $rid,
                                                                              'name'        => $name,
                                                                              'authid'      => $authid,
                                                                              'version'     => $version,
                                                                              'pricecheck'  => $pricecheck,
                                                                              'supportcheck'=> $supportcheck,
                                                                              'democheck'   => $democheck,
                                                                              'dllink'      => $dllink,
                                                                              'price'       => $price,
                                                                              'demolink'    => $demolink,
                                                                              'supportlink' => $supportlink,
                                                                              'changelog'   => $changelog,
                                                                              'changelogf'  => $changelogf,
                                                                              'notesf'      => $notesf,
                                                                              'notes'       => $notes,
                                                                              'rstate'      => $rstate,
                                                                              'stateoptions'=> $stateoptions,
                                                                              'extstate'     => $extstate));



            break;

        case 'update':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck,
                 $dllink,
                 $price,
                 $demolink,
                 $supportlink,
                 $changelog,
                 $notes,
                 $rstate) = xarVarCleanFromInput('rid',
                                                'name',
                                                'version',
                                                'pricecheck',
                                                'supportcheck',
                                                'democheck',
                                                'dllink',
                                                'price',
                                                'demolink',
                                                'supportlink',
                                                'changelog',
                                                'notes',
                                                'rstate');
            
           //if (!xarSecConfirmAuthKey()) return;
            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $rid));

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'createnote',
                                array('rid'         => $rid,
                                      'version'     => $version,
                                      'price'       => $pricecheck,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'priceterms'  => $price,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'type'        => $data['type'],
                                      'notes'       => $notes,
                                      'rstate'      => $rstate))) return;

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));

           $data = xarTplModule('release','user', 'addnote_thanks');

            break;
    }   
    
    return $data;
}

?>
