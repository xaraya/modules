<?php /**
 * flickring module - wraps phpFlickr
 */ 
function flickring_init() {
    // Set up module variables
    xarModSetVar('flickring', 'key', '');
    // Initialisation successful
    return true;
}
/**
 * delete the flickring module
 * @return bool
 */ 
function flickring_delete() {
    // Delete any module variables
    xarModDelAllVars('chat');
    xarRemoveMasks('chat');
    xarRemoveInstances('chat');
    // Deletion successful
    return true;
}
?>
