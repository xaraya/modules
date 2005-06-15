<?php

function images_admin_derivatives()
{
    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    $data = array();
    $data['thumbsdir'] = xarModGetVar('images', 'path.derivative-store');
    $data['images'] = xarModAPIFunc('images','admin','getderivatives',
                                    array('thumbsdir' => $data['thumbsdir']));

    // Return the template variables defined in this function
    return $data;
}
?>
