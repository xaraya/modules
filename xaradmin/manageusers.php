<?php
/**
 * Manage Legis Users
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * Manage Legis Users
 *
 * Allows a Legis Admin to set a User's Default Hall, and add them to a specific Group
 * @author jojodee
 */
function legis_admin_manageusers($args)
{
    extract($args);
    if (!isset($userid)) {
        $userid=0;
    }
    if (!xarSecurityCheck('AdminLegis', 1)) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useruid', 'int:1:', $useruid, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('selectedhall', 'int:1:', $selectedhall, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('usergroup', 'int:1:', $usergroup, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('subscribe', 'checkbox', $subscribe, false, XARVAR_NOT_REQUIRED)) return;

            $data=array();
            //Get the category halls
            $hallsparent=xarModGetVar('legis','mastercids');
            $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));

            $data['halls']=$halls;
            $data['defaulthall']=xarModGetVar('legis','defaulthall');

            /* get all the user groups */
            $usergroups= xarGetGroups();

            $groupselectlist=array();
            $groupexcludelist=array('Administrators','Everybody');
             foreach($usergroups as $k=>$v) {
                if (!in_array($v['name'],$groupexcludelist)){
                    $groupselectlist[$v['uid']]=array('uid'=>$v['uid'],'name'=>$v['name']);
                }
             }
             $data['groupselectlist']=$groupselectlist;


             $userlist= xarModAPIFunc('roles','user','getall');
             $userselectlist=array();
             $excludelist=array('Admin','myself','anonymous','admin');

             foreach($userlist as $k=>$v) {
                 $oldparents=array();
                if (!in_array($v['uname'],$excludelist)){
                   $roles = new xarRoles();
                    $role = $roles->getRole($v['uid']);
                    foreach ($role->getParents() as $oldparent) {
                       $oldparents[] = array('parentid' => $oldparent->getID(),
                                             'parentname' => $oldparent->getName());

                    }

                    $userselectlist[$v['uid']]=array('uid'=>$v['uid'],
                                            'uname'=>$v['uname'],
                                            'defaulthall'=>xarModGetUserVar('legis','defaulthall',$v['uid']),
                                            'parents'=>$oldparents);
                }
             }
             $userselectlist[0]=array('uid'=>0,'uname'=>'Please select','defaulthall'=>0,'parents'=>array('parentid'=>0,'parentname'=>'dummy'));
             $data['userselectlist']=$userselectlist;

             if (isset($useruid)) {
              $data['selecteduser']=$userselectlist[$useruid];
             }else{
              $data['selecteduser']=$userselectlist[0];
             }
             $data['subscribechecked'] = xarModGetVar('legis', 'subscribe') ? true : false;
             $oldhall = xarModGetUserVar('legis','defaulthall',$useruid);
             if (isset($oldhall)) {
                 $halllist = xarModGetVar('legis','subscribers_'.$oldhall);
                 if (isset($halllist)) {
                     $subscribers= unserialize($halllist);
                     if (is_array($subscribers) && in_array($useruid,$subscribers)) {
                         $data['currenthalllist']=1;
                         $data['subscribechecked']=true;
                     }
                     $data['currenthallname']=ucfirst($halls[$oldhall]['name']);
                 } else {
                     $data['currenthalllist']=0;
                     $data['currenthallname']='';
                     $data['subscribechecked']=false;
                 }
             } else {
                 $data['currenthalllist']=0;
                 $data['currenthallname']='';
                  $data['subscribechecked']=false;
             }

    switch (strtolower($phase)) {
        case 'modify':
        default:
            /* Return the template variables defined in this function */
            $data['authid']=xarSecGenAuthKey();
            //$data['hookoutput']=$hooks;
            break;
        case 'update':

         if (isset($selectedhall) && isset($usergroup) && isset($useruid)) {

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
                 $oldusergroup=$userselectlist[$useruid]['parents'];
                 //get the old hall
                 $oldhall = xarModGetUserVar('legis','defaulthall',$useruid);
                 //Unsubscribe from the old hall sub list (if any) whether wanted or not
                 $unsubscribed = xarModAPIFunc('legis','user','unsubscribe',
                                         array('userid' => $useruid,
                                               'hallid' => (int)$oldhall));
                 //set the user new default hall
                 xarModSetUserVar('legis','defaulthall',$selectedhall,$useruid);

                 if ($subscribe) {

                     //subscribe the user to the hall's list
                     $subscribed=xarModAPIFunc('legis', 'user', 'subscribe',
                                     array('userid' => $useruid,
                                           'hallid' => (int)$selectedhall));
                     if (!$subscribed) {
                         //put some error message here
                     }
                 } else {
                    //do we unsubscribe - yeah let's do it
                    $unsubscribed=xarModAPIFunc('legis', 'user', 'unsubscribe',
                                           array('userid' => $useruid,
                                                 'hallid' => (int)$selectedhall));
                     if (!$unsubscribed) {
                         //put some error message here
                     }
                 }

                 $childroles = new xarRoles();
                 $child = $childroles->getRole($useruid);
                 //Make sure they are in one group only
                 $roles = new xarRoles();
                 $newparent = $roles->getRole($usergroup);
                 if ($child->isParent($newparent)){
                     //we don't have anything to do
                 }else{
                     //make the role
                   xarMakeRoleMemberByID($useruid,$usergroup);
                 }
                 //now make sure we delete all other roles
                 foreach ($oldusergroup as $group=>$v) {
                     if ($v['parentid']!= $usergroup) {
                        xarRemoveRoleMemberByID($useruid, $v['parentid']);
                     }
                 }
                 xarSessionSetVar('statusmsg', xarML('User was successfully updated!'));
           }
/*          xarResponseRedirect(xarModURL('legis', 'admin', 'manageusers',array('useruid'=>$useruid,
                                                                              'usergroup'=>$usergroup,
                                                                              'selectedhall'=>$selectedhall)));
*/
        xarResponseRedirect(xarModURL('legis', 'admin', 'manageusers'));
        return true;
            break;
    }
    return $data;
}
?>