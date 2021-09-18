<?php
/**
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function ckeditor_dataapi_adminmenu()
    {
        return [
            ['includes' => ['main','overview'], 'target' => 'main', 'label' => xarML('CKEditor Overview')],
            ['mask' => 'AdminCKEditor', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the CKEditor configuration'), 'label' => xarML('Modify Config')],
        ];
    }
