<?php

// Get a list of possible page statues
// TODO: do we need a status/system status hierarchy?
// TODO: put this in a table so it can be extended.

function xarpages_userapi_getstatuses($args) {
    return array(
        'ACTIVE' => array('status' => 'ACTIVE', 'name' => xarML('Active'), 'desc' => xarML('')),
        'INACTIVE' => array('status' => 'INACTIVE', 'name' => xarML('Inactive'), 'desc' => xarML('')),
        'TEMPLATE' => array('status' => 'TEMPLATE', 'name' => xarML('Template'), 'desc' => xarML(''))
    );
}

?>