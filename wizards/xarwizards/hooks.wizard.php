<?php
/**
 * General Module Wizard: Hooks
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

class hooks extends xarModuleWizard
{

    function run()
    {
        xarResponseRedirect(xarModURL('modules','admin','hooks',array('hook' => $this->module)));
    }
}

// Now instantiate the class, give it a name and a description
$wizard = new hooks(xarML("Configure hooks"));
$wizard->setDescription(xarML("Goes to the hooks page to let you create hooks from this module to others"));

?>