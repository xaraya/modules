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

class newgroup extends xarModuleWizard
{

// This is the code that is executed when you click on the "Run" button.
    function run()
    {
        if (!xarVarFetch('childgroup', 'str', $childgroup, '', XARVAR_NOT_REQUIRED)) {return;}
        if ($childgroup == '') {
            $this->message = xarML("You need to enter a name");
            return true;
        }
        if (xarFindRole($childgroup)) {
            $this->message = xarML("A group called #(1) already exists",$childgroup);
            return true;
        }
        xarMakeGroup($childgroup);
        if (!xarVarFetch('parentgroup', 'str', $parentgroup, '', XARVAR_NOT_REQUIRED)) {return;}
        $roles = new xarRoles();
        $role = $roles->getRole($parentgroup);
        xarMakeRoleMemberByName($childgroup,$role->getName());
        $this->message = xarML("Created #(1) as a child of #(2)",$childgroup,$role->getName());
        return true;
    }
}

// Now instantiate the class, give it a name and a description
// The description is the line(s) that is displayed in the list of wizards

$wizard = new newgroup(xarML("New Group"));
$list = xarModAPIFunc('dynamicdata','user','getproperty',array('type' =>45));
$wizard->setDescription(
                xarML("Create a group named #(1) that is a child of group ","<input name='childgroup' value='' />") .
                $list->showInput(array('name' => 'parentgroup')));

?>