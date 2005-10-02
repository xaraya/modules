<?php
function magpie_user_main()
{
    // The user API function is called
    $links = xarModAPIFunc('magpie',
                           'user',
                           'process',
                           array('url' => 'http://www.wyome.com/?theme=atom'));

    $test = var_export($links); return "<pre>$test</pre>";

    return $links;
}
?>