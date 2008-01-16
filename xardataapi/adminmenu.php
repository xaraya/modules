<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function foo_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Foo Overview')),
            array('mask' => 'AdminAuthsystem', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Foo configuration'), 'label' => xarML('Modify Configuration')),
        );
    }
?>