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

    define('UT_TESTSDIR',sys::code() . 'modules/xarayatesting/lib/xartests'); // subdirectory holding tests

    function xarayatesting_admin_run() 
    {

        /** 
         * Define the array which holds the testsuites
         *
         * Define a default suite which normally holds tests which are not 
         * put into another testsuite explicitly
         */
        $suites= array();
        $suites[] = new xarTestSuite();
        $suite=&$suites[0];

        /**
         * Include all files found in the UT_TESTSDIR directories 
         * it is assumed they are conforming files.
         *
         */

        $paths = get_files(UT_TESTSDIR);
        echo "<pre>";var_dump($paths);
//        exit;

        /**
         * Cycle through all suites found 
         *
         * The foreach loop gathers the results from all testsuites
         * 
         */
        foreach ($suites as $torun) {
            // Run the testsuite
            // if it's not run only the suites specified
            if(empty($testfilter['suites'])) {
                if($do_run) $torun->run();
                $torun->report($testfiler['output'],$show_results);
            } else {
                // Only run the testsuite if it's mentions
                if(in_array(str_replace(' ','_',$torun->_name),$testfilter['suites'])) {
                    if($do_run) $torun->run();
                    $torun->report($testfilter['output'],$show_results);
                }
            }
        }
    }
    function get_files($start_path) 
    {
        static $paths = array();
        $rdi = new RecursiveDirectoryIterator($start_path);
        foreach (new RecursiveIteratorIterator($rdi) as $path) {
            array_push($paths, (string) $path);
        }
        return $paths;
    }

?>