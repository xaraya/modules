<?php
/***************************************
* authinvision - integrate xaraya users with
* invision board.
****************************************/
function authinvision_user_login() 
{
    $mainfile = xarModGetVar('authinvision','mainfile');
    require($mainfile);
	
}

function authinvision_user_main() 
{
    $mainfile = xarModGetVar('authinvision','mainfile');
	include("$mainfile");;
	$data['regform'] = show_reg();
	return $data;
}

?>