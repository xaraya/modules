<?php

    $suite = new xarTestSuite('Base module tests');
    $suites[] = $suite;

    class testBaseAdminGetmenulinks extends xarTestCase
    {
        public function testGetmenulinks()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'getmenulinks');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testGetmenulinksWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('base', 'admin', 'getmenulinks');
            $this->actual = xarMod::apiFunc('base', 'admin', 'getmenulinks', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminGetmenulinks', 'getMenuLinks (admin) function tests');

    class testBaseAdminGetmodulesettings extends xarTestCase
    {
        public function testGetmodulesettings()
        {
            try {
                $this->expected = '[exception]';
                $this->actual   = xarMod::apiFunc('base', 'admin', 'getmodulesettings');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        public function testGetmodulesettingsValidModuleParam()
        {
            $args = ['module' => 'roles'];
            $this->expected = '[object]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'getmodulesettings', $args);
            $res = $this->assertTrue(is_object($this->actual), "Call with valid module param returns object");
            return $res;
        }

        public function testGetmodulesettingsBadModuleParam()
        {
            $args = ['module' => 'nosuchname'];
            $this->expected = '[object]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'getmodulesettings', $args);
            $res = $this->assertTrue(is_object($this->actual), "Call with invalid module param returns object");
            return $res;
        }
    }
    $suite->AddTestCase('testBaseAdminGetmodulesettings', 'getmodulesettings (admin) function tests');

    class testBaseAdminGetusersettings extends xarTestCase
    {
        public function testGetusersettings()
        {
            try {
                $this->expected = '[exception]';
                $this->actual   = xarMod::apiFunc('base', 'admin', 'getusersettings');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        public function testGetusersettingsNoItemidParam()
        {
            try {
                $args = ['module' => 'roles'];
                $this->expected = '[exception]';
                $this->actual   = xarMod::apiFunc('base', 'admin', 'getusersettings', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with module param and no itemid param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with module param and no itemid param throws an exception");
            }
        }

        public function testGetusersettingsNoModuleParam()
        {
            try {
                $args = ['itemid' => 1];
                $this->expected = '[exception]';
                $this->actual   = xarMod::apiFunc('base', 'admin', 'getusersettings', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with itemid param and no module param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with itemid param and no module param throws an exception");
            }
        }

        public function testGetusersettingsValidParams()
        {
            $args = ['module' => 'roles', 'itemid' => 1];
            $this->expected = '[object]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'getusersettings', $args);
            $res = $this->assertTrue(is_object($this->actual), "Call with valid module and itemid params returns object");
            return $res;
        }

        public function testGetusersettingsBadModuleParam()
        {
            $args = ['module' => 'nosuchname', 'itemid' => 1];
            $this->expected = '[null]';
            $this->actual   = xarMod::apiFunc('base', 'admin', 'getusersettings', $args);
            $res = $this->assertTrue(is_null($this->actual), "Call with valid itemid param and bad module param returns null");
            return $res;
        }

        /* @CHECKME: This test causes an infinite loop somewhere...
        function testGetusersettingsBadItemidParam()
        {
            try{
                $args = array('module' => 'roles', 'itemid' => 'wrong');
                $this->expected = '[exception]';
                $this->actual   = xarMod::apiFunc('base','admin','getusersettings', $args);
                $res = $this->assertSame($this->actual,$this->expected,"Call with valid module param and bad itemid param throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with valid module param and bad itemid param throws an exception");
            }
        }
        */
    }
    $suite->AddTestCase('testBaseAdminGetusersettings', 'getusersettings (admin) function tests');

    class testBaseAdminLoadadminmenuarray extends xarTestCase
    {
        public function testLoadadminmenuarray()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'loadadminmenuarray');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testLoadadminmenuarrayWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('base', 'admin', 'loadadminmenuarray');
            $this->actual = xarMod::apiFunc('base', 'admin', 'loadadminmenuarray', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminLoadadminmenuarray', 'loadadminmenuarray (admin) function tests');

    class testBaseAdminMenuarray extends xarTestCase
    {
        public function testMenuarray()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'menuarray');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testMenuarrayWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('base', 'admin', 'menuarray');
            $this->actual = xarMod::apiFunc('base', 'admin', 'menuarray', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminMenuarray', 'menuarray (admin) function tests');

    class testBaseAdminWaitingcontent extends xarTestCase
    {
        public function testWaitingcontent()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('base', 'admin', 'waitingcontent');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testWaitingcontentWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('base', 'admin', 'waitingcontent');
            $this->actual = xarMod::apiFunc('base', 'admin', 'waitingcontent', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminWaitingcontent', 'waitingcontent (admin) function tests');

    class testBaseJSGeteventattributes extends xarTestCase
    {
        public function testGeteventattributes()
        {
            $this->expected = '';
            $this->actual = xarMod::apiFunc('base', 'javascript', 'geteventattributes');
            return $this->AssertSame($this->actual, $this->expected, 'Call with no params returns empty string');
        }
        /*
        function testGeteventattributesWithparams()
        {
            $args = array('position' => 'body', 'type' => 'onload');
            $this->expected = '[string]';
            $this->actual = xarMod::apiFunc('base','javascript','geteventattributes', $args);
            return $this->AssertTrue(is_string($this->actual),'Call with no params returns string');
        }
        function testGeteventattributesBadparams()
        {
            $args = array('position' => '', 'type' => 'nosuchevent');
            $this->expected = '[string]';
            $this->actual = xarMod::apiFunc('base','javascript','geteventattributes', $args);
            return $this->AssertTrue(is_string($this->actual),'Call with bad params returns string');
        }
        */
    }
    $suite->AddTestCase('testBaseJSGeteventattributes', 'geteventattributes (javascript) function tests');
