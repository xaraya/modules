<?php
function weirdmind_user_weirdmind($arg = NULL)
{
    $path =  getcwd();
    $newpath = getcwd() .'/modules/weirdmind/includes';
    chdir ($newpath);
    ob_start();
//    include('modules/weirdmind/includes/weirdmind.html'};
    echo `cat weirdmind.html 2>&1`;
    $return = ob_get_contents();
    ob_end_clean();
    flush();
    chdir($path);
    return $return;
}
?>