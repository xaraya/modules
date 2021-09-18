<?php

sys::import('modules.xarayatesting.class.xarUnitTest');

class testBLCompiler1 extends xarTestCase
{
    public $myBLC;

    public function setup()
    {
        $GLOBALS['xarDebug'] = false;
        include_once 'xarCore.php';
        include_once 'xarVar.php';
        include_once 'xarException.php';
        include_once 'XarayaCompiler.php';
        $this->myBLC = new xarTpl__Compiler();
    }

    public function precondition()
    {
        // Abort on bogus file: must not exist
        if (file_exists('xartests/doesntexist')) {
            return false;
        }
        // Testdata for BL
        if (!file_exists('xartests/test.xt')) {
            return false;
        }
        return true;
    }

    public function teardown()
    {
        // not needed here
    }


    public function testnotNull()
    {
        return $this->assertNotNull($this->myBLC, "BL Compiler Instantiation");
    }

    public function testnoData()
    {
        return $this->assertNull($this->myBLC->compileFile('doesntexist'), "Don't compile on bogus file");
    }

    public function testCompilenotnull()
    {
        return $this->assertnotNull($this->myBLC->compileFile('xartests/test.xt'), "Return not null on compile of a valid file");
    }

    public function testWinHTMLComments()
    {
        $tplString="<!-- \r\n#\$foo#\r\n  -->";
        $expected="<!-- \r\n#\$foo#\r\n  --><?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "HTML multiline comments windows CR");
    }

    public function testMacHTMLComments()
    {
        $tplString="<!-- \r#\$foo#\r  -->";
        $expected="<!-- \r#\$foo#\r  --><?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "HTML multiline comments mac CR");
    }

    public function testUnixHTMLComments()
    {
        $tplString="<!-- \n#\$foo#\n  -->";
        $expected="<!-- \n#\$foo#\n  --><?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "HTML multiline comments unix CR");
    }

    public function testWinBLComments()
    {
        $tplString="<!--- \r\n#\$foo#\r\n  --->";
        $expected="<?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "BL multiline comments windows CR");
    }
    public function testMacBLComments()
    {
        $tplString="<!--- \r#\$foo#\r  --->";
        $expected="<?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "BL multiline comments mac CR");
    }
    public function testUnixBLComments()
    {
        $tplString="<!--- \n#\$foo#\n  --->";
        $expected="<?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "BL multiline comments unix CR");
    }

    public function testDoubleHash()
    {
        // Test for bug 694 and 695
        $tplString="#\$foo##";
        $expected="<?php echo \$foo; ?>#<?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "Double hash after variable should return 1 back");
    }

    public function testTripleHash()
    {
        // Test for bug 694 and 695
        $tplString="#\$foo###";
        $expected="<?php echo \$foo; ?>#<?php return true;?>";
        $out = $this->myBLC->compile($tplString);
        return $this->assertSame($out, $expected, "Triple hash after variable should return 1 back");
    }
}

$tmp = new xarTestSuite('Blocklayout compiler tests');
$tmp->AddTestCase('testBLCompiler', 'Instantiation and file compiling');
$suites[] = $tmp;
