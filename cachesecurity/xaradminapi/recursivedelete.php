<?php

/**
 * Returns the filename and path of the config file 
 */
function cachesecurity_adminapi_recursivedelete ($args)
{
    $directory = $args['directory'];
    
    if (file_exists($directory) && is_dir($directory)) {
        if ($handle = opendir($directory)) {
            while (($file = readdir($handle)) !== false) {
                if ($file == '..' || $file == '.' ) continue(1);
                $cache_file = $directory . '/' .$file;
                if (is_file($cache_file) || is_link($cache_file)) {
                    unlink($cache_file);
                } elseif (is_dir($cache_file)) {
                    if (!cachesecurity_adminapi_recursivedelete (
                        array('directory'=>$cache_file))) return false;
                }
            }
            closedir($handle);
        }

        rmdir($directory);
    }
    
    return true;
}

?>