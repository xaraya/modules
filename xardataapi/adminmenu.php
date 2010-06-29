<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function ckeditor_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('CKEditor Overview')),
            array('mask' => 'AdminCKEditor', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the CKEditor configuration'), 'label' => xarML('Modify Config')),
        );
    }
?>