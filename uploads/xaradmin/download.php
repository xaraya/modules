<?php
function uploads_admin_download()
{
    //get filter
    if (!xarVarFetch('ulid', 'int:1:', $ulid)) return;

    return xarModAPIFunc('uploads',
                          'admin',
                          'download',
						  array('ulid'=>$ulid));
}
?>