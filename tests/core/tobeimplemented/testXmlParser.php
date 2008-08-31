<?php

class testXmlMisc extends xarTestCase {
    var $xarXml;
    var $savedir;

    function setup() {
        $this->savedir=getcwd();
        chdir('..');
        include_once 'includes/xarXML.php';
        $this->xarXml = new xarXmlParser();
    }

    function teardown() {
        chdir($this->savedir);
    }

    function testDifficult() {
        $result = $this->xarXml->parseFile('includes/xartests/test.xml');
        //print_r($this->xarXml->tree);
        if (!$result) echo $this->xarXml->lastmsg."\n";
        return $this->AssertTrue($result,'Parse an ugly, yet valid document');
    }
}    

class testW3TestSuite extends xarTestCase {
    var $xmlconf;
    var $xmltest;
    var $savedir;
    var $errors=array();
    var $testcases;
    var $xmltestbase;

    function setup() {
        $this->savedir=getcwd();
        chdir('..');
        include_once 'includes/xarXML.php';
        $this->xmltest = new xarXmlParser();
        $this->xmlconf = new xarXmlParser();
        // The test-suite has an xml file which contains all the tests
        // located in ./xmltestsuite/xmlconf/xmltests/xmltest.xml
        // First let's parse that file, kind of a prerequisite
        $this->xmltestbase='includes/xartests/xmltestsuite/xmlconf/';
        if($this->xmlconf->parseFile($this->xmltestbase . 'xmlconf.xml')) {
            // First get a list of the testcases and their locations. so we can test them
            $testcases = $this->xmlconf->getElementsByName('TESTCASES');
            foreach($testcases as $testcase) {
                if(array_key_exists('http://www.w3.org/XML/1998/namespace:base',$testcase['attributes'])) {
                    $this->testcases[] = $testcase;
                }
            }
        } else {            
            echo $this->xmlconf->lastmsg;
            return false;
        }
    }

    function teardown() {
        chdir($this->savedir);
    }

  
    function testParseValidFromW3TestSuite() {
        $this->errors=array();
        $testcounter=0;
        foreach($this->testcases as $testcase) {
            $dir = $testcase['attributes']['http://www.w3.org/XML/1998/namespace:base'];
            $testTree = $this->xmlconf->getSubTree($testcase[XARXML_ATTR_TAGINDEX]);
            $tests = $this->xmlconf->getElementsByName('TEST',$testTree);
            foreach($tests as $test) {
                $testcounter++;
                if($test['attributes']['TYPE'] =='valid') {
                    // Valid documents should at least be parseable ;-)
                    $testfile = $this->xmltestbase . $dir . $test['attributes']['URI'];
                    //echo $testfile."\n";
                    if(!$this->xmltest->parseFile($testfile)) {
                        //echo "Directly parsed: $testfile\n";
                        //echo $this->xmltest->lastmsg;
                        //print_r($this->xmltest->tree);
                        $this->errors[]= $test['attributes']['URI'].":".$this->xmltest->lastmsg;
                    }
                }
            }
        }
        $msgtoreturn="Valid documents should parse without errors (".count($this->errors)."/$testcounter)";
        //if(!empty($this->errors)) $msgtoreturn .= "\n" . implode("\n",$this->errors);
        
        return $this->AssertTrue(empty($this->errors),$msgtoreturn);
    }





    function _testParseNotWellFormedFromW3TestSuite() {
        $this->errors=array();
        $testcounter=0; $errorcounter=0;
        foreach($this->testcases as $test) {
            if($test['attributes']['TYPE'] =='not-wf') {
                // Not wellformed document *should* produce errors
                $testfile = $this->xmltestbase . $test['attributes']['URI'];
                if($this->xarXml->parseFile($testfile)) {
                    $errorcounter++;
                    $this->errors[]= $test['attributes']['URI'].": parsed ok, but is not well-formed\n"
                        . $test['content'];
//                     echo "$testfile\n";
//                     echo $test['attributes']['URI'].": parsed ok, but is not well-formed\n"
//                         . $test['content'];
//                     print_r($this->xarXml->tree);
                } else {
                    $testcounter++;
                }
                
            }
        }
        
        $msgtoreturn="Not well formed documents should give errors ($errorcounter/$testcounter)";
        //if(!empty($this->errors)) $msgtoreturn .= "\n" . implode("\n",$this->errors);
        return $this->AssertTrue(empty($this->errors),$msgtoreturn);
    }

    function _testParseInvalidFromW3TestSuite() {
        $this->errors=array();
        $testcounter=0; $errorcounter=0;
        foreach($this->testcases as $test) {
            if($test['attributes']['TYPE'] =='invalid') {
                // Invalid documents *should* produce errors
                $testfile = $this->xmltestbase . $test['attributes']['URI'];
                $testcounter++;
                if($this->xarXml->parseFile($testfile)) {
                    $errorcounter++;
                    $this->errors[]= $test['attributes']['URI'].": parsed ok, but is invalid\n"
                        . $test['content'];
//                     echo "$testfile\n";
//                     echo $test['attributes']['URI'].": parsed ok, but is invalid\n"
//                         . $test['content'];
//                     print_r($this->xarXml->tree);

                }
            }
        }
        
        $msgtoreturn="Invalid documents should give errors ($errorcounter/".$testcounter.")";
        //if(!empty($this->errors)) $msgtoreturn .= "\n" . implode("\n",$this->errors);
        return $this->AssertTrue(empty($this->errors),$msgtoreturn);
    }

}

$tmp = new xarTestSuite('XML parser tests');
$tmp->AddTestCase('testXmlMisc','Weird XML documents');
$tmp->AddTestCase('testW3TestSuite','W3 XML Test suite');
$suites[] = $tmp;
?>