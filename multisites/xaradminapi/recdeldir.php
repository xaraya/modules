<?php
//Recursively delete everything in a directory!!!
function multisites_adminapi_recdeldir($args)
{
	extract($args);
    if (!isset($sitedirpath)) {
            $msg = xarML("Could not remove #(1)", $sitedirpath);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-REMOVING DIRECTORY', new DefaultUserException($msg));
            return $msg;
    }
    $current_dir = opendir($sitedirpath);
    while($topdir = readdir($current_dir)){
        if(is_dir("{$sitedirpath}/{$topdir}") and ($topdir != "." and $topdir!="..")){
            xarModAPIFunc('multisites','admin','recdeldir',array('sitedirpath' =>"${sitedirpath}/${topdir}"));
        }elseif($topdir != "." and $topdir!=".."){
            unlink("${sitedirpath}/${topdir}");
        }
    }
    closedir($current_dir);
    rmdir($sitedirpath);

return true;
}
?>