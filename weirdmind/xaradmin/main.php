<?php
function weirdmind_admin_main($arg = NULL)
{
//this should work, but doesnt on my install....
//uncomment this for windows users
//    include_once('modules/weirdmind/includes/README.html'};

// for nix this should work, but i still don't know why the above is not working :(
//comment this out if you are a windows user
    $path =  getcwd();
    $newpath = getcwd() .'/modules/weirdmind/includes';
    chdir ($newpath);
    ob_start();

echo `cat README.html 2>&1`;
//end unix centric
    $return = ob_get_contents();
    ob_end_clean();
    flush();
    chdir($path);
    return $return;
}
?>