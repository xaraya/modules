<?php
function weirdmind_admin_sshreadme($arg = NULL)
{
    $path =  getcwd();
    $newpath = getcwd() .'/modules/weirdmind/includes';
    chdir ($newpath);
    ob_start();
//    @include('include-me'};
echo'<pre>';
echo htmlspecialchars(`cat mindbright/FAQ 2>&1`);
echo'</pre>';
    $return = ob_get_contents();
    ob_end_clean();
    flush();
    chdir($path);
    return $return;
}
?>