<?php

    $suite = new xarTestSuite('Blocklayout compiler tests');
    $suites[] = $suite;

    # ---------------------------- Instantiation and Compilation

    class testBLCompiler extends xarTestCase 
    {
        var $myBLC;

        function setup() 
        {
            $GLOBALS['xarDebug'] = false;
            sys::import('xaraya.templating.compiler');
            $this->myBLC = XarayaCompiler::instance();
        }

        function precondition() 
        {
            // Abort on bogus file: must not exist
            if (file_exists(sys::code() . 'modules/xarayatesting/tests/core/doesntexist')) return false;
            // Testdata for BL
            if (!file_exists(sys::code() . 'modules/xarayatesting/tests/core/test.xt')) return false;
            return true;
        }

        function teardown () 
        {
            // not needed here
        }


        function testnotNull() 
        { 
            return $this->assertNotNull($this->myBLC,"BL Compiler Instantiation");
        }

        function testnoData() 
        {
            try{
                $this->expected = '[exception]';
                $this->actual   = $this->myBLC->compileFile('doesntexist');
                $res = $this->assertSame($this->actual,$this->expected,"A non-existent file throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"A non-existent file throws an exception");
            }
        }

        function testCompilenotnull() 
        {
            return $this->assertnotNull($this->myBLC->compileFile(sys::code() . 'modules/xarayatesting/tests/core/test.xt'),"Return not null on compile of a valid file");
        }

    }

    $suite->AddTestCase('testBLCompiler','Instantiation and file compiling');

    # ---------------------------- General Text Node Handling

    class testBLCompilerTextNodes extends xarTestCase 
    {
        var $myBLC;

        function setup() 
        {
            $GLOBALS['xarDebug'] = false;
            sys::import('blocklayout.compiler');
            $this->myBLC = XarayaCompiler::instance();
        }

        function precondition()
        {
            // not needed here
            return true;
        }

        function teardown () 
        {
            // not needed here
        }

        function testGeneralTagOpenForm() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<foo>bar</foo>";
            $tplString .= '</xar:template>';
            $expected   = "<foo><?php echo xarML('bar');?></foo>\n";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"The open form of a tag in general is untouched - nothing added here!");
        }

        function testGeneralTagClosedForm() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<foo/>";
            $tplString .= '</xar:template>';
            $expected   = "<foo/>\n";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"The closed form of a tag in general is untouched - nothing added here!");
        }

        function testHashVariable() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "#\$foo#";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML(\$foo);?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Variable inside hashes is resolved as PHP");
        }

        function testSimpleStringTextNode() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "foo";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('foo');?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"A non-numeric text node containing no special chars is translatable");
        }

        function testSimpleNumberTextNode() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "666";
            $tplString .= '</xar:template>';
            $this->expected   = "666\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"A text node representing a number is untouched");
        }

        function testTextBeforeHashVariable() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "foo#\$bar#";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('foo'.\$bar);?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Text + hashvar is translatable");
        }

        function testTextAfterHashVariable() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "#\$bar#foo";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML(\$bar.'foo');?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Hashvar + text is translatable");
        }

        function testTextBeforeAndAfterHashVariable() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "foo#\$bar#oo";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('foo'.\$bar.'oo');?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Text + hashvar + text is translatable");
        }

        function testHashVariableBeforeAndAfterText() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "#\$foo#bar#\$oo#";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML(\$foo.'bar'.\$oo);?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Hashvar + text + hashvar is translatable");
        }

        function testTextWithDoubleHash() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "foobar ##cccooo";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('foobar #cccooo');?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Double hash in text should return 1 hash back");
        }

        function testHashVarWithDoubleHash() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "#\$foobar###cccooo";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML(\$foobar.'#cccooo');?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Hashvar and double hash should resolve and return 1 hash back");
        }

        function testDoubleHashWithHashVar() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "##cccooo#\$foobar#";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('#cccooo'.\$foobar);?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Double hash and hashvar should resolve and return 1 hash back");
        }

        function xtestDoubleHash() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            // Test for bug 694 and 695
            $tplString .= "#\$foo##";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo \$foo; ?>#";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Double hash after variable should return 1 back");
        }

        function xtestTripleHash() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            // Test for bug 694 and 695
            $tplString .= "foo###";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('foo#'); ?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"Triple hash after variable should return 1 back");
        }

        function testMLSPlaceholders() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "foo #(1) bar #(2)";
            $tplString .= '</xar:template>';
            $this->expected   = "<?php echo xarML('foo #(1) bar #(2)');?>\n";
            $this->actual = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($this->expected);
            $this->actual   = "Hex ".bin2hex($this->actual);
            return $this->assertSame($this->actual,$this->expected,"MLS placeholders such as #(1) are untouched");
        }

    }

    $suite->AddTestCase('testBLCompilerTextNodes','General text node handling');

    class testBLCompilerComments extends xarTestCase 
    {
        var $myBLC;

        function setup() 
        {
            $GLOBALS['xarDebug'] = false;
            sys::import('blocklayout.compiler');
            $this->myBLC = XarayaCompiler::instance();
        }

        function precondition() 
        {
            // not needed here
            return true;
        }

        function teardown () 
        {
            // not needed here
        }

        function testHTMLComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<!-- \r\n sometext\r\n  -->";
            $tplString .= '</xar:template>';
            $expected   = "";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"HTML comments are removed in the transform");
        }

        function testxarComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<xar:comment> foo </xar:comment>";
            $tplString .= '</xar:template>';
            $expected   = "<!-- foo -->\n";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"xar:comment tags become html comments");
        }

        function testWinMultilineComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<xar:comment> \r\nfoo\r\n </xar:comment>";
            $tplString .= '</xar:template>';
            $expected   = "<!-- \rfoo\r -->\n";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"BL multiline comments windows CR");
        }

        function testMacMultilineComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .="<xar:comment> \rfoo\r </xar:comment>";
            $tplString .= '</xar:template>';
            $expected   = "<!-- \rfoo\r -->\n";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"BL multiline xar:comments mac CR");
        }

        function testUnixMultilineComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .="<xar:comment> \nsometext\n </xar:comment>";
            $tplString .= '</xar:template>';
            $expected   = "<!-- \nsometext\n -->";
            $out = $this->myBLC->compileString($tplString);
            $this->expected = "Hex ".bin2hex($expected);
            $this->actual   = "Hex ".bin2hex($out);
            return $this->assertSame($out,$expected,"BL multiline comments unix CR");
        }

        function xtestWinBLComments()
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .="<!-- \r\n#\$foo#\r\n  -->";
            $tplString .= '</xar:template>';
            $this->expected   = "<!-- \r\n#\$foo#\r\n  -->";
            $this->actual = $this->myBLC->compileString($tplString);
            return $this->assertSame($this->actual,$this->expected,"BL multiline comments with windows CR");
        }
        function xtestMacBLComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .="<!--\r#\$foo#\r  -->";
            $tplString .= '</xar:template>';
            $this->expected   = "<!--\r#\$foo#\r  -->";
            $this->actual = $this->myBLC->compileString($tplString);
            return $this->assertSame($this->actual,$this->expected,"BL multiline comments with mac CR");
        }
        function xtestUnixBLComments()
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<!-- \n#\$foo#\n  -->";
            $tplString .= '</xar:template>';
            $this->expected   = "<!-- \n#\$foo#\n  -->";
            $this->actual = $this->myBLC->compileString($tplString);
            return $this->assertSame($this->actual,$this->expected,"BL multiline comments with unix CR");
        }

        function testHashVariableInxarComments() 
        {
            $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplString .= "<xar:comment><xar:var name=\"foo\" value=\"666\"/></xar:comment>";
            $tplString .= '</xar:template>';
            $this->expected   = "<!-- 666 -->";
            $this->actual = $this->myBLC->compileString($tplString);
            return $this->assertSame($this->actual,$this->expected,"Hash variables in xar:comment tags are resolved");
        }

    }

    $suite->AddTestCase('testBLCompilerComments','Comment handling');
?>