<?php
function uploads_admin_download()
{
    //get filter
	//TODO: Place correct filters, only ulid OR ulname is required, not both.  ulname is a string
//    if (!xarVarFetch('ulid', 'int:1:', $ulid)) return;
//    if (!xarVarFetch('ulname', '', $ulname)) return;

    $ulid   = xarVarCleanFromInput('ulid');
    $ulname = xarVarCleanFromInput('ulname');

    $thumbwidth  = xarVarCleanFromInput('thumbwidth');
    $thumbheight = xarVarCleanFromInput('thumbheight');
    $thumb = xarVarCleanFromInput('thumb');


    return xarModAPIFunc('uploads',
                          'admin',
                          'download',
						  array('ulid'=>$ulid
						  		, 'ulname'=>$ulname
						  		, 'thumbwidth'=>$thumbwidth
						  		, 'thumbheight'=>$thumbheight
						  		, 'thumb'=>$thumb
								));
}
?>