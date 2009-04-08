<?php

    function xarayatesting_scans_module_php_validity_check()
    {
        $items = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_ACTIVE)));
        $checked_modules = array();
        foreach ($items as $item) {
            $basedir = 'modules/' . $item['name'];
            $files = get_php_files($basedir,'php');
            foreach ($files as $file) {
                include_once($file);
            }
            $checked_modules[] = $item;
        }
        $data['items'] = $checked_modules;
        return $data; 
    }

    function get_php_files($directory, $filter=FALSE)
    {
        $directory_tree = array();

        // if the path has a slash at the end we remove it here
         if(substr($directory,-1) == '/') $directory = substr($directory,0,-1);

         // if the path is not valid or is not a directory ...
         if(!file_exists($directory) || !is_dir($directory)) return array();

         if(is_readable($directory)) {
             // we open the directory
             $directory_list = opendir($directory);

             // and scan through the items inside
             while (FALSE !== ($file = readdir($directory_list))) {
                 // if the filepointer is not the current directory
                 // or the parent directory
                 if($file != '.' && $file != '..')
                 {
                     // we build the new path to scan
                     $path = $directory.'/'.$file;

                     // if the path is readable
                     if(is_readable($path)) {
                         // we split the new path by directories
                         $subdirectories = explode('/',$path);

                         // if the new path is a directory
                         if(is_dir($path)) {
                             // add the directory details to the file list
                             $dirs = get_php_files($path, $filter);
                             $directory_tree = array_merge($directory_tree, $dirs);  

                         // if the new path is a file
                         } elseif(is_file($path)) {
                             // get the file extension by taking everything after the last dot
                             $f = explode('.',end($subdirectories));
                             $extension = end($f);

                             // if there is no filter set or the filter is set and matches
                             if($filter === FALSE || $filter == $extension) {
                                 // add the file details to the file list
                                 $directory_tree[] = $path;
                             }
                         }
                     }
                 }
             }
             // close the directory
             closedir($directory_list); 

             // return file list
             return $directory_tree;

         // if the path is not readable ...
         } else {
             return array();    
         }
    }
?>