<?php
function weirdmind_user_main($arg = NULL)
{
    $path =  getcwd();
    $newpath = getcwd() .'/modules/weirdmind/includes';
    chdir ($newpath);
    ob_start();
//    @include('include-me'};
echo `cat README.html 2>&1`;
    $return = ob_get_contents();
    ob_end_clean();
    flush();
    chdir($path);
    return $return;
}
?>