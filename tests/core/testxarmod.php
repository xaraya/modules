<?php

$suite = new xarTestSuite('Module system tests');
$suites[] = $suite;

class testxarModgetID extends xarTestCase {
               
    function testgetID() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getID();
            $res = $this->assertSame($this->actual,$this->expected,"A call with no param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"A call with no param throws an exception");
        }
    }

    function testgetIDWithValidParam() {
        $this->expected = '[systemID]';
        $this->actual   = xarMod::getID('xarayatesting');
        $res = $this->assertTrue(is_numeric($this->actual),"A module name param returns the system ID of a module");
        return $res;
    }

    function testgetIDWithBadParam() {
        $this->expected = '[null]';
        $this->actual   = xarMod::getID('foobar');
        $res = $this->assertNull($this->actual,"An invalid module name param returns null");
        return $res;
    }
}
$suite->AddTestCase('testxarModgetID','xarMod::getID($modulename)');

class testxarModgetName extends xarTestCase {
               
    function testgetName() {
        $this->expected = 'xarayatesting';
        $this->actual   = xarMod::getName();
        $res = $this->assertSame($this->actual,$this->expected,"No param returns the name of the current module");
        return $res;
    }

    function testgetNameWithValidParam() {
        $this->expected = 'xarayatesting';
        $this->actual   = xarMod::getName(30073);
        $res = $this->assertSame($this->actual,$this->expected,"A regid param returns the name of a module");
        return $res;
    }

    function testgetNameWithBadNumberParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getName(1111111111);
            $res = $this->assertSame($this->actual,$this->expected,"An invalid regid number param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"An invalid regid number param throws an exception");
        }
    }

    function testgetNameWithBadStringParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getName("foobar");
            $res = $this->assertSame($this->actual,$this->expected,"A non-numeric param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"A non-numeric param throws an exception");
        }
    }

}
$suite->AddTestCase('testxarModgetName','xarMod::getName($regid)');

class testxarModgetDisplayName extends xarTestCase {
               
    function testgetDisplayName() {
        $this->expected = xarML('Xaraya Testing');
        $this->actual   = xarMod::getDisplayName();
        $res = $this->assertSame($this->actual,$this->expected,"No param returns the displayable name of the current module");
        return $res;
    }

    function testgetDisplayNameWithValidParam() {
        $this->expected = xarML('Xaraya Testing');
        $this->actual   = xarMod::getDisplayName('xarayatesting');
        $res = $this->assertSame($this->actual,$this->expected,"A module name param returns the name of a module");
        return $res;
    }

/*    function testgetDisplayNameWithBadModuleParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getDisplayName("foobar");
            $res = $this->assertSame($this->actual,$this->expected,"Bad module name param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Bad module name param throws an exception");
        }
    }
*/
    // CHECKME: really shouldn't this throw an exception? (see note in getFileInfo code)
    function testgetDisplayNameWithBadModuleParam() {
        $this->expected = '';
        $this->actual   = xarMod::getDisplayName("foobar");
        return $this->assertSame($this->expected, $this->actual,"Bad module name returns empty string");
    }
    function testgetDisplayNameWithValidParams() {
        $this->expected = xarML('Xaraya Testing');
        $this->actual   = xarMod::getDisplayName('xarayatesting','module');
        $res = $this->assertSame($this->actual,$this->expected,"Module name, type params returns the name of a module");
        return $res;
    }

    function testgetDisplayNameWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getDisplayName('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }

}
$suite->AddTestCase('testxarModgetDisplayName','xarMod::getDisplayName($modulename, $type)');

class testxarModgetDisplayDescription extends xarTestCase {
               
    function testgetDisplayDescription() {
        $this->expected = xarML('Module with QA checks and unit tests');
        $this->actual   = xarMod::getDisplayDescription();
        $res = $this->assertSame($this->actual,$this->expected,"No param returns the description of the current module");
        return $res;
    }

    function testgetDisplayDescriptionWithValidParam() {
        $this->expected = xarML('Home Page');
        $this->actual   = xarMod::getDisplayDescription('base');
        $res = $this->assertSame($this->actual,$this->expected,"A module name param returns the description of a module");
        return $res;
    }

/*    function testgetDisplayNameWithBadModuleParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getDisplayName("foobar");
            $res = $this->assertSame($this->actual,$this->expected,"Bad module name param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Bad module name param throws an exception");
        }
    }
*/
    // CHECKME: really shouldn't this throw an exception? (see note in getFileInfo code)
    function testgetDisplayDescriptionWithBadModuleParam() {
        $this->expected = '';
        $this->actual   = xarMod::getDisplayDescription("foobar");
        return $this->assertSame($this->expected, $this->actual,"Bad module name returns empty string");
    }
    function testgetDisplayDescriptionWithValidParams() {
        $this->expected = xarML('Module with QA checks and unit tests');
        $this->actual   = xarMod::getDisplayDescription('xarayatesting','module');
        $res = $this->assertSame($this->actual,$this->expected,"Module name, type params returns the description of a module");
        return $res;
    }

    function testgetDisplayDescriptionWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getDisplayDescription('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }

}
$suite->AddTestCase('testxarModgetDisplayDescription','xarMod::getDisplayDescription($modulename, $type)');

class testxarModgetRegid extends xarTestCase {
               
    function testgetRegid() {
        $this->expected = 30073;
        $this->actual   = xarMod::getRegID('xarayatesting');
        $res = $this->assertSame($this->actual,$this->expected,"No param returns the regid of the current module");
        return $res;
    }
    function testgetRegidWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getRegID();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testgetRegidWithBadParam() {
        $this->expected = '[null]';
        $this->actual   = xarMod::getRegID('foobar');
        $res = $this->assertNull($this->actual,"Call with invalid param returns a null");
        return $res;
    }
}
$suite->AddTestCase('testxarModgetRegid','xarMod::getRegID($modulename)');

class testxarModgetInfo extends xarTestCase {
               
    function testgetInfo() {
        $this->expected = '[array]';
        $this->actual   = xarMod::getInfo(30073);
        $res = $this->assertTrue(is_array($this->actual),"Call with regid param returns an array");
        return $res;
    }
    function testgetInfoWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getInfo();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testgetInfoWithBadModuleParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getInfo(111111111);
            $res = $this->assertSame($this->actual,$this->expected,"Call with invalid regid param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with invalid regid param throws an exception");
        }
    }
    function testgetInfoWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getInfo(30073,'foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Regid param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Regid param, bad type param throws an exception");
        }
    }
    function testgetInfoWithTypeParam1() {
        $this->expected = '[array]';
        $this->actual   = xarMod::getInfo(30073,'module');
        $res = $this->assertTrue(is_array($this->actual) && (count($this->actual) == 30),"Call with regid param and type = 'module' returns an array of 30 elements");
        return $res;
    }
    function testgetInfoWithTypeParam2() {
        $this->expected = '[array]';
        $this->actual   = xarMod::getInfo(21,'theme');
        $res = $this->assertTrue(is_array($this->actual) && (count($this->actual) == 30),"Call with theme id param and type = 'theme' returns an array of 30 elements");
        return $res;
    }
}
$suite->AddTestCase('testxarModgetInfo','xarMod::getInfo($regid, $type)');

class testxarModgetBaseInfo extends xarTestCase {
               
    function testgetBaseInfo() {
        $this->expected = '[array]';
        $this->actual   = xarMod::getBaseInfo('xarayatesting');
        $res = $this->assertTrue(is_array($this->actual),"Call with module name param returns an array");
        return $res;
    }
    function testgetBaseInfoWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getBaseInfo();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testgetBaseInfoWithBadModuleParam() {
        $this->expected = '[null]';
        $this->actual   = xarMod::getBaseInfo(111111111);
        return $this->assertNull($this->actual,"Call with invalid module name param returns null");
        return $res;
    }
    function testgetBaseInfoWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getBaseInfo('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }
    function testgetBaseInfoWithTypeParam1() {
        $this->expected = '[array]';
        $this->actual   = xarMod::getBaseInfo('xarayatesting','module');
        $res = $this->assertTrue(is_array($this->actual) && (count($this->actual) == 10),"Call with module name param and type = 'module' returns an array of 10 elements");
        return $res;
    }
    function testgetBaseInfoWithTypeParam2() {
        $this->expected = '[array]'; 
        $this->actual   = xarMod::getBaseInfo('default','theme');
        $res = $this->assertTrue(is_array($this->actual) && (count($this->actual) == 8),"Call with theme name param and type = 'theme' returns an array of 8 elements");
        return $res;
    }
}
$suite->AddTestCase('testxarModgetBaseInfo','xarMod::getBaseInfo($modulename, $type)');

class testxarModgetFileInfo extends xarTestCase {
               
    function testgetFileInfo() {
        $this->expected = '[array]';
        $this->actual   = xarMod::GetFileInfo('xarayatesting');
        $res = $this->assertTrue(is_array($this->actual),"Call with module name param returns an array");
        return $res;
    }
    function testgetFileInfoWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::GetFileInfo();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testgetFileInfoWithBadModuleParam() {
        $this->expected = '[null]';
        $this->actual   = xarMod::GetFileInfo('foobar');
        $res = $this->assertNull($this->actual,"Call with invalid module param returns a null");
        return $res;
    }
    function testgetFileInfoWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::GetFileInfo('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }
    function testgetFileInfoWithTypeParam1() {
        $this->expected = '[array]';
        $this->actual   = xarMod::GetFileInfo('xarayatesting','module');
        $res = $this->assertTrue(is_array($this->actual) && (count($this->actual) == 26),"Call with module name param and type = 'module' returns an array of 30 elements");
        return $res;
    }
    function testgetFileInfoWithTypeParam2() {
        $this->expected = '[array]';
        $this->actual   = xarMod::GetFileInfo('default','theme');
        $res = $this->assertTrue(is_array($this->actual) && (count($this->actual) == 26),"Call with theme name param and type = 'theme' returns an array of 30 elements");
        return $res;
    }
}
$suite->AddTestCase('testxarModgetFileInfo','xarMod::getFileInfo($modulename, $type)');

class testxarModloadDbInfoInfo extends xarTestCase {
               
    function testloadDbInfo() {
        $this->expected = true;
        $this->actual   = xarMod::loadDbInfo('xarayatesting');
        $res = $this->assertTrue($this->actual,"Call with module name param returns true");
        return $res;
    }
    function testgetFileInfoWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::loadDbInfo();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testgetFileInfoWithBadModuleParam() {
        $this->expected = '[null]';
        $this->actual   = xarMod::loadDbInfo('foobar');
        $res = $this->assertNull($this->actual,"Call with invalid module param returns a null");
        return $res;
    }
    function testloadDbInfoWithBadDirParam() {
        $this->expected = true;
        $this->actual   = xarMod::loadDbInfo('xarayatesting','foobar');
        $res = $this->assertTrue($this->actual,"Call with invalid module dir param returns true?");
        return $res;
    }
    function testgetFileInfoWithBadTypeParam() {
        $this->expected = true;
        $this->actual   = xarMod::loadDbInfo('xarayatesting','xarayatesting','bar');
        $res = $this->assertTrue($this->actual,"Call with invalid type param returns true?");
        return $res;
    }
}
$suite->AddTestCase('testxarModloadDbInfoInfo','xarMod::loadDbInfo($modulename, $moduledir, $type)');

class testxarModgetState extends xarTestCase {
               
    function testgetState() {
        $this->expected = 3;
        $this->actual   = xarMod::getState(30073);
        $res = $this->assertSame($this->actual,$this->expected,"Call with module name param returns a state value");
        return $res;
    }
    function testgetStateWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getState();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testgetStateWithBadModuleParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getState(1111111111);
            $res = $this->assertSame($this->actual,$this->expected,"Call with invalid module param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with invalid module param throws an exception");
        }
    }
    function testgetStateWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::getState(30073,'foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }
    function testgetStateWithTypeParam1() {
        $this->expected = 3;
        $this->actual   = xarMod::getState(30073,'module');
        $res = $this->assertSame($this->actual,$this->expected,"Call with module name param and type = 'module' returns returns a state value");
        return $res;
    }
    function testgetStateWithTypeParam2() {
        $this->expected = 3;
        $this->actual   = xarMod::getState(21,'theme');
        $res = $this->assertSame($this->actual,$this->expected,"Call with theme name param and type = 'theme' returns returns a state value");
        return $res;
    }
}
$suite->AddTestCase('testxarModgetState','xarMod::getState($regid, $type)');

class testxarModisAvailable extends xarTestCase {
               
    function testisAvailable() {
        $this->expected = true;
        $this->actual   = xarMod::isAvailable('xarayatesting');
        $res = $this->assertSame($this->actual,$this->expected,"Call with module name param returns a boolean");
        return $res;
    }
    function testisAvailableWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::isAvailable();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testisAvailableWithBadModuleParam() {
        $this->expected = '[null]';
        $this->actual   = xarMod::isAvailable('foobar');
        $res = $this->assertFalse($this->actual,"Call with invalid module param returns a null");
        return $res;
    }
    function testisAvailableWithBadTypeParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::isAvailable('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }
    function testisAvailableWithTypeParam1() {
        $this->expected = true;
        $this->actual   = xarMod::isAvailable('xarayatesting','module');
        $res = $this->assertSame($this->actual,$this->expected,"Call with module name param and type = 'module' returns returns a boolean");
        return $res;
    }
    function testisAvailableWithTypeParam2() {
        $this->expected = true;
        $this->actual   = xarMod::isAvailable('default','theme');
        $res = $this->assertSame($this->actual,$this->expected,"Call with theme name param and type = 'theme' returns returns a boolean");
        return $res;
    }
}
$suite->AddTestCase('testxarModisAvailable','xarMod::isAvailable($modulename, $type)');

class testxarModload extends xarTestCase {
               
    function testload() {
        $this->expected = true;
        $this->actual   = xarMod::load('xarayatesting');
        $res = $this->assertSame($this->actual,$this->expected,"Call with module name param returns a boolean");
        return $res;
    }
    function testloadWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::load();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testloadWithBadModuleParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::load('foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with invalid module param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with invalid module param throws an exception");
        }
    }
    function testloadWithBadTypeParam() {
            $this->expected = true;
            $this->actual   = xarMod::load('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param returns true");
            return $res;
        try{
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }
}
$suite->AddTestCase('testxarModload','xarMod::load($modulename, $type)');

class testxarModapiload extends xarTestCase {
               
    function testapiload() {
        $this->expected = true;
        $this->actual   = xarMod::apiload('xarayatesting');
        $res = $this->assertSame($this->actual,$this->expected,"Call with module name param returns a boolean");
        return $res;
    }
    function testapiloadWithNoParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::apiload();
            $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with no params throws an exception");
        }
    }
    function testapiloadWithBadModuleParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarMod::apiload('foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with invalid module param throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with invalid module param throws an exception");
        }
    }
    function testapiloadWithBadTypeParam() {
            $this->expected = true;
            $this->actual   = xarMod::apiload('xarayatesting','foobar');
            $res = $this->assertSame($this->actual,$this->expected,"Call with module name param, bad type param returns true");
            return $res;
        try{
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with module name param, bad type param throws an exception");
        }
    }
}
$suite->AddTestCase('testxarModapiload','xarMod::apiload($modulename, $type)');

class testxarModRegisterHook extends xarTestCase {
               
    function testRegisterHook() {
        $this->expected = true;
        $this->actual   = xarModRegisterHook('aaa','bbb','ccc','xarayatesting','eee','fff');
        $res = $this->assertSame($this->actual,$this->expected,"Call with valid module name and 6 params returns a boolean");
        return $res;
    }
    function testRegisterHookWithSixBadParam() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarModRegisterHook('aaa','bbb','ccc','ddd','eee','fff');
            $res = $this->assertSame($this->actual,$this->expected,"Call with invalid module name and 6 params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with invalid module name and 6 params throws an exception");
        }
    }
    function testRegisterHookWith5Param() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarModRegisterHook('aaa','bbb','ccc','xarayatesting','eee');
            $res = $this->assertSame($this->actual,$this->expected,"Call with less than 6 params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with less than 6 params throws an exception");
        }
    }
}
$suite->AddTestCase('testxarModRegisterHook','xarModRegisterHook($hookObject, $hookAction, $hookArea, $hookModName, $hookModType, $hookFuncName)');

class testxarModUnregisterHook extends xarTestCase {
               
    function testRegisterHook() {
        $this->expected = true;
        $this->actual   = xarModUnregisterHook('aaa','bbb','ccc','xarayatesting','eee','fff');
        $res = $this->assertSame($this->actual,$this->expected,"Call with valid module name and 6 params returns true");
        return $res;
    }
    function testUnregisterHookWithSixBadParam() {
        $this->expected = true;
        $this->actual   = xarModUnregisterHook('aaa','bbb','ccc','xarayatesting','eee','fff');
        $res = $this->assertSame($this->actual,$this->expected,"Call with invalid 6 params returns true");
        return $res;
    }
    function testUnregisterHookWith5Param() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarModUnregisterHook('aaa','bbb','ccc','xarayatesting','eee');
            $res = $this->assertSame($this->actual,$this->expected,"Call with less than 6 params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"Call with less than 6 params throws an exception");
        }
    }
}
$suite->AddTestCase('testxarModUnregisterHook','xarModUnegisterHook($hookObject, $hookAction, $hookArea,$hookModName, $hookModType, $hookFuncName)');
?>