<?php
function weirdmind_init() {
    xarRegisterMask('AdminWeirdMind','All','weirdmind','All','All','ACCESS_ADMIN');
    return true;  
}
function weirdmind_upgrade($args = null) {
    return true;
}

function weirdmind_delete() {
    xarRemoveMasks('weirdmind');
    return true;
}
?>