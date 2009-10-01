<?php

/**
 * File: $Id$
 *
 * Runner for the unit testing framework
 *
 * It is assumed that this file runs with the 
 * current directory set to the directory which
 * contains the 'xartests' subdirectory
 *
 * @package quality
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage testing
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @author Jan Schrage <jan@xaraya.com>
 * @author Frank Besler <besfred@xaraya.com>
*/
    sys::import('modules.xarayatesting.class.xarUnitTest');

    function xarayatesting_user_testpage()
    {
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'base', XARVAR_NOT_REQUIRED)) return;

        // Get the modules we can test
        $installed = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_INSTALLED)));
        $data['coremodules'] = array();
        $data['modules'] = array();
        foreach ($installed as $module) {
            if (!isset($module['class'])) {
//                if (file_exists(sys::code() . 'modules/'. $module['name'] . '/xartest.php')) 
                    $data['modules']['modules.test'.$module['name']] = $module;
            } elseif (substr($module['class'],0,4) == 'Core' || $module['name'] == 'authsystem') {
                $data['coremodules']['coremodules.test'.$module['name']] = $module;
            } else {
                if (file_exists(sys::code() . 'modules/'. $module['name'] . '/xartest.php')) 
                    $data['modules']['modules.test'.$module['name']] = $module;
            }
        }
        $suites = array();    
        try {
            if (substr($data['tab'],0,7) == 'modules') {
                $modulename = substr($data['tab'],12);
                $location = sys::code() . 'modules/'. $modulename . '/xartest.php';
                include_once($location);
            } else {
                $location = sys::code() . 'modules/xarayatesting/tests/'. str_replace('.','/',$data['tab']) .'.php';
                include_once($location);
            }
        } catch (Exception $e) {$data['message'] = xarML('No test file found at #(1)',$location);}

        $data['suites'] = array();
        foreach ($suites as $torun) {
            $torun->run();
            $data['suites'][] = $torun;            
        }
        return $data;
        
        $testfilter['output']='html';
        $show_results = true;

        /** 
         * Define the array which holds the testsuites
         *
         * Define a default suite which normally holds tests which are not 
         * put into another testsuite explicitly
         */
        $suites= array();

        /**
         * Include all files found in the UT_TESTSDIR directories 
         * it is assumed they are conforming files.
         *
         */

/*        
        // Traverse the subtree from the current directory downwards
        // For each tests directory include the tests found
        $filematch = "";
        $filetype = "php";
        $basedir = sys::code() . "modules/xarayatesting/tests/core";
        $suites = array();
        $basedir = realpath($basedir);
        $filelist = array();

        sys::import('xaraya.structures.relativedirectoryiterator');
        $dir = new RelativeDirectoryIterator($basedir);

        $extensions = array('php');
        for($dir->rewind();$dir->valid();$dir->next()) {
            if($dir->isDir()) continue; // no dirs
            if(!in_array($dir->getExtension(),$extensions)) continue;
            if($dir->isDot()) continue; // temp for emacs insanity and skip hidden files while we're at it
            include_once($basedir."/".$dir->getFileName());
        }

        $data['suites'] = array();
        foreach ($suites as $torun) {
            // Run the testsuite
            // if it's not run only the suites specified
            if(empty($testfilter['suites'])) {
                $torun->run();
                //$torun->report($testfilter['output'],$show_results);
            } else {
                // Only run the testsuite if it's mentioned
                if(in_array(str_replace(' ','_',$torun->_name),$testfilter['suites'])) {
                    $torun->run();
//                    $torun->report($testfilter['output'],$show_results);
                }
            }
            $data['suites'][] = $torun;            
        }
*/

        return $data;
    }

?>