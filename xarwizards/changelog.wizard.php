<?php
/**
 * General Module Wizard: Changelog
 *
 *
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @access  public
 * @throws  none
 * @todo    none
*/

// Create the class for this wizard
// The parent class already contains reasonable defaults
// What we need to do here is mainly define tyhe task the wizard has to perform
// and the return message when the task is completed

class changelog extends xarModuleWizard
{

    function run()
    {
        xarModAPIFunc('modules','admin','updatehooks',array('regid' => 185, 'hooked_' . $this->module . "[0]" => 1));
    }
}

// Now instantiate the class, give it a name and a description
$wizard = new changelog(xarML("Add Changelog"));
$wizard->setDescription(xarML("Add changelog functionality to this module"));

?>