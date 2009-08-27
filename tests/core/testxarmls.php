<?php

    $suite = new xarTestSuite('MLS system tests');
    $suites[] = $suite;

    class testMLFunction extends xarTestCase 
    {

        function testEmptyMLString() 
        {
            $out = xarML('');
            $expected='';
            return $this->AssertSame($out,$expected,'Return empty string on empty input for xarML');
        }

    }
    $suite->AddTestCase('testMLFunction','xarML function tests');

    class testMLSSystemTags extends xarTestCase 
    {

        function setup() 
        {
            $GLOBALS['xarDebug'] = false;
            sys::import('blocklayout.compiler');
            $xslFile = 'blocklayout/xslt/xar2php.xsl';
            $this->myBLC = xarBLCompiler::instance();
        }

        function testGeneralTagClosedForm() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<xar:mlstring>foo</xar:mlstring>";
            $tplString .= '</xar:template>';
            $expected   = "<?php echo xarML('foo');?>";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
    //        echo "Expected: " . $expected;
    //        echo "Actual: " . $out;
            return $this->assertSame($out,$expected,"Text content inside the mlstring tag is translatable");
        }
    }
    $suite->AddTestCase('testMLSSystemTags','ML tag tests');

?>