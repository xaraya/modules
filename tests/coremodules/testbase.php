<?php

    $suite = new xarTestSuite('Base module tests');
    $suites[] = $suite;

    class testBaseAdminGetmenulinks extends xarTestCase
    {
        public function testGetmenulinks()
        {
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('base', 'admin', 'getmenulinks');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testGetmenulinksWithParams()
        {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('base', 'admin', 'getmenulinks');
            $this->actual = xarModAPIFunc('base', 'admin', 'getmenulinks', $args);
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
                $this->actual   = xarModAPIFunc('base', 'admin', 'getmodulesettings');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        public function testGetmodulesettingsValidModuleParam()
        {
            $args = array('module' => 'roles');
            $this->expected = '[object]';
            $this->actual = xarModAPIFunc('base', 'admin', 'getmodulesettings', $args);
            $res = $this->assertTrue(is_object($this->actual), "Call with valid module param returns object");
            return $res;
        }

        public function testGetmodulesettingsBadModuleParam()
        {
            $args = array('module' => 'nosuchname');
            $this->expected = '[object]';
            $this->actual = xarModAPIFunc('base', 'admin', 'getmodulesettings', $args);
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
                $this->actual   = xarModAPIFunc('base', 'admin', 'getusersettings');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        public function testGetusersettingsNoItemidParam()
        {
            try {
                $args = array('module' => 'roles');
                $this->expected = '[exception]';
                $this->actual   = xarModAPIFunc('base', 'admin', 'getusersettings', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with module param and no itemid param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with module param and no itemid param throws an exception");
            }
        }

        public function testGetusersettingsNoModuleParam()
        {
            try {
                $args = array('itemid' => 1);
                $this->expected = '[exception]';
                $this->actual   = xarModAPIFunc('base', 'admin', 'getusersettings', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with itemid param and no module param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with itemid param and no module param throws an exception");
            }
        }

        public function testGetusersettingsValidParams()
        {
            $args = array('module' => 'roles', 'itemid' => 1);
            $this->expected = '[object]';
            $this->actual = xarModAPIFunc('base', 'admin', 'getusersettings', $args);
            $res = $this->assertTrue(is_object($this->actual), "Call with valid module and itemid params returns object");
            return $res;
        }

        public function testGetusersettingsBadModuleParam()
        {
            $args = array('module' => 'nosuchname', 'itemid' => 1);
            $this->expected = '[null]';
            $this->actual   = xarModAPIFunc('base', 'admin', 'getusersettings', $args);
            $res = $this->assertTrue(is_null($this->actual), "Call with valid itemid param and bad module param returns null");
            return $res;
        }

        /* @CHECKME: This test causes an infinite loop somewhere...
        function testGetusersettingsBadItemidParam()
        {
            try{
                $args = array('module' => 'roles', 'itemid' => 'wrong');
                $this->expected = '[exception]';
                $this->actual   = xarModAPIFunc('base','admin','getusersettings', $args);
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
            $this->actual = xarModAPIFunc('base', 'admin', 'loadadminmenuarray');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testLoadadminmenuarrayWithParams()
        {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('base', 'admin', 'loadadminmenuarray');
            $this->actual = xarModAPIFunc('base', 'admin', 'loadadminmenuarray', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminLoadadminmenuarray', 'loadadminmenuarray (admin) function tests');

    class testBaseAdminMenuarray extends xarTestCase
    {
        public function testMenuarray()
        {
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('base', 'admin', 'menuarray');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testMenuarrayWithParams()
        {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('base', 'admin', 'menuarray');
            $this->actual = xarModAPIFunc('base', 'admin', 'menuarray', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminMenuarray', 'menuarray (admin) function tests');

    class testBaseAdminWaitingcontent extends xarTestCase
    {
        public function testWaitingcontent()
        {
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('base', 'admin', 'waitingcontent');
            return $this->AssertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testWaitingcontentWithParams()
        {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('base', 'admin', 'waitingcontent');
            $this->actual = xarModAPIFunc('base', 'admin', 'waitingcontent', $args);
            return $this->AssertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testBaseAdminWaitingcontent', 'waitingcontent (admin) function tests');

    class testBaseJSGeteventattributes extends xarTestCase
    {
        public function testGeteventattributes()
        {
            $this->expected = '';
            $this->actual = xarModAPIFunc('base', 'javascript', 'geteventattributes');
            return $this->AssertSame($this->actual, $this->expected, 'Call with no params returns empty string');
        }
        /*
        function testGeteventattributesWithparams()
        {
            $args = array('position' => 'body', 'type' => 'onload');
            $this->expected = '[string]';
            $this->actual = xarModAPIFunc('base','javascript','geteventattributes', $args);
            return $this->AssertTrue(is_string($this->actual),'Call with no params returns string');
        }
        function testGeteventattributesBadparams()
        {
            $args = array('position' => '', 'type' => 'nosuchevent');
            $this->expected = '[string]';
            $this->actual = xarModAPIFunc('base','javascript','geteventattributes', $args);
            return $this->AssertTrue(is_string($this->actual),'Call with bad params returns string');
        }
        */
    }
    $suite->AddTestCase('testBaseJSGeteventattributes', 'geteventattributes (javascript) function tests');
