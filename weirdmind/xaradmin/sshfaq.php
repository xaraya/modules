<?php
function weirdmind_admin_sshfaq($arg = NULL)
{
    $path =  getcwd();
    $newpath = getcwd() .'/modules/weirdmind/includes';
    chdir ($newpath);
    ob_start();
    echo '<div style="margin-left: 1em; margin-right: 1em; text-align:left;">';
    echo '<pre>';
//    include('mindbright/README'};
echo htmlspecialchars(`cat mindbright/README 2>&1`);
    echo '</div>';
    echo '</pre>';
    $return = ob_get_contents();
    ob_end_clean();
    flush();
    chdir($path);
    return $return;
}
?>