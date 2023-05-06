<?php

    function xarayatesting_scans_theme_template_validity_check()
    {
        if (!xarVar::fetch('item','str',$item,0,xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('confirm','int',$data['confirm'],0,xarVar::NOT_REQUIRED)) return;

        $items = xarMod::apiFunc('themes', 'admin', 'getlist', array('filter' => array('State' => xarMod::STATE_ACTIVE)));
        
        if (!$data['confirm']) {
            $data['items'] = array();
            foreach ($items as $item) {
                $data['items'][] = array('id' => $item['regid'], 'name' => $item['name']);
            }
        } else {
            if ($item != 0) {
                $items = array(xarTheme::getInfo($item));
            }
            $reader = new XMLReader();
            $checked_themes = array();
            foreach ($items as $item) {
                $basedir = 'themes/' . $item['name'];
                $files = get_theme_files($basedir,'xt');
                foreach ($files as $file) {
                    parse_theme_template($file,$reader);
                }
                $checked_themes[] = $item;
            }
            $reader->close();
            $data['items'] = $checked_themes;
        }
        
        // We want to use the admin page template
        xarTpl::setPageTemplateName('admin');
        return $data; 
    }

    function get_theme_files($directory, $filter=FALSE)
    {
        $directory_tree = array();

        // if the path has a slash at the end we remove it here
         if(substr($directory,-1) == '/') $directory = substr($directory,0,-1);

         // if the path is not valid or is not a directory ...
         if(!file_exists($directory) || !is_dir($directory)) return array();

         // Directories called abeyance are to be ignored
         if(basename($directory) == 'abeyance') return array();

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
                             $dirs = get_theme_files($path, $filter);
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

    function parse_theme_template($filename,$reader)
    {
        if (!file_exists($filename)) return;
        $fd = fopen($filename, 'r');
        if (!$fd) {
            $msg = xarML('Cannot open the file #(1)',$filename);
            throw new Exception($msg);
        }
        
        $filestring = file_get_contents($filename);
        $filestring = preg_replace("/&xar([\-A-Za-z\d.]{2,41});/","xar-entity",$filestring);
//        try {
            $reader->xml($filestring);
            while ($reader->read()) continue;
//        } catch (Exception $e) {
//            $e->tostring();
//        }

    }
?>