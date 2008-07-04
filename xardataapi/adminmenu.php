<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function karma_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Karma Overview')),
            array('mask' => 'AdminKarma', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Karma configuration'), 'label' => xarML('Modify Configuration')),
        );
    }
?>