<?php
/**
 * Wizard Script: Wizards Module
 *
 *
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @access  public
 * @throws  none
 * @todo    none
*/

// Create the class for this wizard
// The parent class already contains reasonable defaults
// What we need to do here is mainly define the task the wizard has to perform
// and the return message when the task is completed

class checkuser extends xarModuleWizard
{

// This is the code that is executed when you click on the "Run" button.
    function run()
    {
        if (!xarVarFetch('user', 'str', $user, '', XARVAR_NOT_REQUIRED)) {return;}
        if ($user == '') {
            $this->message = xarML("You need to enter a name");
            return true;
        }
        elseif ($found = xarFindRole($user)) {
            $parents = $found->getParents();
            $type = $found->getType() == 0 ? 'user' : 'group';
            $url = xarModURL('roles','admin','displayrole',array('uid' => $found->getID()));
            $names = array();
            foreach ($parents as $parent) $names[] = $parent->getName();
            $this->message = xarML('A #(1) named <a href="#(2)">#(3)</a> (display name) exists as a member of #(4)',$type, $url, $user,implode(',',$names));
            return true;
        }
        elseif ($found = xarUFindRole($user)) {
            $parents = $found->getParents();
            $type = $found->type ==0 ? 'user' : 'group';
            $names = array();
            foreach ($parents as $parent) $names[] = $parent->getName();
            $this->message = xarML("A user named #(1) (user name) exists as a member of #(2)",$user,implode(',',$names));
            return true;
        }
        else {
            $this->message = xarML("No user named #(1) could be found",$user);
            return true;
        }
    }
}

// Now instantiate the class, give it a name and a description
// The description is the line(s) that is displayed in the list of wizards

$wizard = new checkuser(xarML("Check User"));
$wizard->setDescription(
                xarML("Check if a user named #(1) exists ","<input name='user' value='' />"));

?>