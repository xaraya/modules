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

class makemember extends xarModuleWizard
{

// This is the code that is executed when you click on the "Run" button.
    function run()
    {
        if (!xarVarFetch('childname', 'str', $childname, '', XARVAR_NOT_REQUIRED)) {return;}
        if ($childname == '') {
            $this->message = xarML("You need to enter the name of a user");
            return true;
        }
        $child = xarFindRole($childname);
        if (!$child || $child->getType() == 1) {
            $this->message = xarML("Could not find a user called #(1)",$childname);
            return true;
        }
        if (!xarVarFetch('parentid', 'str', $parentid, '', XARVAR_NOT_REQUIRED)) {return;}
        $roles = new xarRoles();
        $parent = $roles->getRole($parentid);
        if (!$parent) {
            $this->message = xarML("You need to choose a parent group");
            return true;
        }
        if ($child->isParent($parent)) {
            $this->message = xarML("#(1) is already a member of #(2)",$childname,$parent->getName());
            return true;
        }
        xarMakeRoleMemberByName($childname,$parent->getName());
        $this->message = xarML("#(1) is now a member of #(2)",$childname,$parent->getName());
      return true;
    }
}

// Now instantiate the class, give it a name and a description
// The description is the line(s) that is displayed in the list of wizards

// only choose certain groups
$restrictions = 'parent:Users;group:Users';
$wizard = new makemember(xarML("Make Member"));
$list = xarModAPIFunc('dynamicdata','user','getproperty',array('type' =>45,
                                                               'validation' => $restrictions));
$wizard->setDescription(
                    xarML("Make a user named #(1) a member of group ","<input name='childname' value='' />") .
                    $list->showInput(array('name' => 'parentid')));

?>