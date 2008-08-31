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

define('UT_TESTSDIR','xartests'); // subdirectory holding tests
define('UT_FRAMWDIR','BitKeeper/custom/unittest/'); 

// Get the command line args in here, trus the callee to have parsed
// it properly into neatly space separated stuff
$args=$_SERVER['argv'];
array_shift($args);

// Rudimentary parsing of what we need
$testfilter=array();
$testfilter['output']='text';
$show_results=true;
$do_run=true;
for($index=0;$index<count($args);$index++) {
    switch($args[$index]) {
    case '-c':
    case'--cases':
        $cases=explode(",",trim($args[$index+1],"'"));
        foreach($cases as $case) $testfilter['cases'][]=$case;
        break;
    case '-s':
    case '--suites':
        $suites=explode(",",trim($args[$index+1],"'"));
        foreach($suites as $suite) $testfilter['suites'][]=$suite;
        break;
    case '-o': 
    case '--output':
        $testfilter['output'] = $args[$index+1];
        break;
    case '-n':
    case '--noresults':
        $show_results=false;
        break;
    case '-l':
    case '--list':
        $show_results=false;
        $do_run=false;
        break;
    }
}

sys::import('modules.xarayatesting.class.xarUnitTest');

function runtests()
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

// Traverse the subtree from the current directory downwards
// For each tests directory include the tests found
$findcmd='bk sfiles -gd | grep ' .UT_TESTSDIR;
$dirs=array('modules);
exec($findcmd,$dirs,$return_status);

while (list($key, $dir) = each($dirs)) {
    // In this dir, check for the existance of the special tests folder UT_TESTSDIR
    if (is_dir($dir)) {
        // Open the dir and include the testfiles
        if ($testsdir = opendir($dir)) {
            exec("bk get -Sq $dir");
            while ($file = readdir($testsdir) ) {
                // Now, we get also ., .. and subdirs, let's filter out some stuff
                // the testfiles are php scripts, so let's require them to have the
                // php extension
                
                if (preg_match('/\.php$/',$file)) {
                    if ($file !='') {
                        // The chdir juggling is necessary to set the 
                        // property _basedir of each testcase.
                        $savedir = getcwd();
                        chdir($savedir."/".$dir);
                        exec("bk get -Sq $file");
                        include_once($file);
                        chdir($savedir);
                    }
                }
            }
        } else {
            // No tests found, skip this directory
            // Do not die, as we might have other tests in 
            // other directories
            //die("No tests found\n");
        }
    }
}

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

?>