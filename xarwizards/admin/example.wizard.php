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

// the name of the class needs to correspond to the name of the file, in this case "example.wizard.php".
class example extends xarModuleWizard
{

// This is the code that is executed when you click on the "Run" button.
// It can be as complicated as you like.
    function run()
    {
// The xarVarFetch function grabs a POST argument "phrase" from the page you click "Run" on.
// This argument is defined further below here.
        if (!xarVarFetch('phrase', 'str', $phrase, '', XARVAR_NOT_REQUIRED)) {return;}

// We simply wrap the phrase we got from the POST argument and echo it.
// The message (which could also be an error message) will be displayed at the bottom
// of the list of wizards.
        $this->message = xarML("You said #(1)",'<b>"' . $phrase . '"</b>');

// Return true to indicate everything is fine
        return true;
    }
}

// Now instantiate the class, give it a name and a description

// Here is where we define and instantiate the class
// The title is inserted in the constructor.
// We could also have added it later using a set function:
//       $example->setName("A Better Example");
$wizard = new example(xarML("Echo a Phrase"));

// The description is the line(s) that is displayed in the list of wizards
// Note we name the input field "phrase". xarVarFetch picks this up (as described above)
// for processing when you hit the "Run" button
$wizard->setDescription(
                xarML("Enter a phrase here: #(1)","<input name='phrase' value='' />"));
?>