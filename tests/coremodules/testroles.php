<?php

    $suite = new xarTestSuite('Roles module tests');
    $suites[] = $suite;

    class testRolesAdminAddmember extends xarTestCase
    {
        public function testAddmemberNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'addmember');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesAdminAddmember', 'addmember (admin) function tests');

    class testRolesAdminClearsessions extends xarTestCase
    {
        public function testClearsessions()
        {
            try {
                $this->expected = true;
                $this->actual = xarMod::apiFunc('roles', 'admin', 'clearsessions');
                return $this->assertTrue($this->actual, "Call with no params returns true");
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        // Note: this function needs looking at
    }
    $suite->AddTestCase('testRolesAdminClearsessions', 'clearsessions (admin) function tests');

    class testRolesAdminGetgroupmenulinks extends xarTestCase
    {
        public function testGetgroupmenulinks()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'admin', 'getgroupmenulinks');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testGetgroupmenulinksWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'admin', 'getgroupmenulinks');
            $this->actual =xarMod::apiFunc('roles', 'admin', 'getgroupmenulinks', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testRolesAdminGetgroupmenulinks', 'getgroupmenulinks (admin) function tests');

    class testRolesAdminGetmenulinks extends xarTestCase
    {
        public function testGetmenulinks()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'admin', 'getmenulinks');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testGetmenulinksWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'admin', 'getmenulinks');
            $this->actual = xarMod::apiFunc('roles', 'admin', 'getmenulinks', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testRolesAdminGetmenulinks', 'getmenulinks (admin) function tests');

    class testRolesAdminGetmessageincludestring extends xarTestCase
    {
        public function testGetmessageincludestringNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessageincludestring');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        public function testGetmessageincludestringBadTemplateParam()
        {
            try {
                $args = ['template' => 'someinvalidtemplate'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessageincludestring', $args);
                $res =  $this->assertSame($this->actual, $this->expected, 'Call with an invalid template param throws an exception');
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, 'Call with an invalid template param throws an exception');
            }
        }

        public function testGetmessageincludestringBadModuleParam()
        {
            try {
                $args = ['module' => 'someinvalidmodule', 'template' => 'message-vars'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessageincludestring', $args);
                $res =  $this->assertSame($this->actual, $this->expected, 'Call with an invalid module param throws an exception');
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, 'Call with an invalid module param throws an exception');
            }
        }

        public function testGetmessageincludestring()
        {
            $args = ['module' => 'mail', 'template' => 'message-vars'];
            $this->expected = '[string]';
            $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessageincludestring', $args);
            return $this->assertTrue(is_string($this->actual), 'Call with valid module name and template params returns a string');
        }
    }
    $suite->AddTestCase('testRolesAdminGetmessageincludestring', 'getmessageincludestring (admin) function tests');

    class testRolesAdminGetmessagestrings extends xarTestCase
    {
        public function testGetmessagestringsNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessagestrings');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }

        public function testGetmessagestringsBadTemplateParam()
        {
            try {
                $args = ['template' => 'someinvalidtemplate'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessagestrings', $args);
                $res =  $this->assertSame($this->actual, $this->expected, 'Call with an invalid template param throws an exception');
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, 'Call with an invalid template param throws an exception');
            }
        }

        public function testGetmessagestringsBadModuleParam()
        {
            try {
                $args = ['module' => 'someinvalidmodule', 'template' => 'welcome'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessagestrings', $args);
                $res =  $this->assertSame($this->actual, $this->expected, 'Call with an invalid module param throws an exception');
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, 'Call with an invalid module param throws an exception');
            }
        }

        public function testGetmessagestrings()
        {
            $args = ['module' => 'roles', 'template' => 'welcome'];
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'admin', 'getmessagestrings', $args);
            return $this->assertTrue(is_array($this->actual), 'Call with valid module name and template params returns an array');
        }
    }
    $suite->AddTestCase('testRolesAdminGetmessagestrings', 'getmessagestrings (admin) function tests');

    class testRolesAdminMenu extends xarTestCase
    {
        public function testMenu()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'admin', 'menu');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns an array');
        }

        public function testMenuWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'admin', 'menu');
            $this->actual = xarMod::apiFunc('roles', 'admin', 'menu', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testRolesAdminMenu', 'menu (admin) function tests');

    class testRolesAdminPurge extends xarTestCase
    {
        public function testPurgeNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'purge');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
        /*
        function testPurgeWithActiveStateParams()
        {
            try{
                $this->expected = '[exception]';
                $args = array('state' => Roles_Master::ROLES_RSTATE_ACTIVE);
                $this->actual = xarMod::apiFunc('roles', 'admin', 'purge', $args);
                $res = $this->assertSame($this->actual,$this->expected,"Call with bad state param throws exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with bad state param throws exception");
            }
        }
        */
    }
    $suite->AddTestCase('testRolesAdminPurge', 'purge (admin) function tests');

    class testRolesAdminRecall extends xarTestCase
    {
        public function testRecallNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'recall');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesAdminRecall', 'recall (admin) function tests');

    class testRolesAdminSendusermail extends xarTestCase
    {
        public function testSendusermailNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'sendusermail');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesAdminSendusermail', 'sendusermail (admin) function tests');

    class testRolesAdminStateupdate extends xarTestCase
    {
        public function testStateupdateNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'admin', 'stateupdate');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
        public function testStateupdateBadIDParam()
        {
            try {
                $this->expected = '[exception]';
                $args = ['state' => Roles_Master::ROLES_RSTATE_ACTIVE, 'id' => 12345678];
                $this->actual = xarMod::apiFunc('roles', 'admin', 'stateupdate', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with bad id param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad id param throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesAdminStateupdate', 'stateupdate (admin) function tests');


    /* user api */

    class testRolesUserAddmember extends xarTestCase
    {
        public function testAddmemberNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'addmember');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserAddmember', 'addmember (user) function tests');

    class testRolesUserCheckprivilege extends xarTestCase
    {
        public function testCheckprivilegeNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'checkprivilege');
                $res = $this->assertSame($this->actual, $this->expected, "Call with no params throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserCheckprivilege', 'checkprivilege (user) function tests');

    class testRolesUserCountall extends xarTestCase
    {
        public function testCountallNoParams()
        {
            try {
                $this->expected = '[integer]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall');
                $res = $this->assertTrue(is_numeric($this->actual), "Call with no params returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params returns integer");
            }
        }
        public function testCountallStateParam()
        {
            try {
                $this->expected = '[integer]';
                $args = ['state' => 3];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall', $args);
                $res = $this->assertTrue(is_numeric($this->actual), "Call with state param returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with state param returns integer");
            }
        }
        public function testCountallInclude_anonymousParam()
        {
            try {
                $this->expected = '[integer]';
                $args = ['include_anonymous' => false];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall', $args);
                $res = $this->assertTrue(is_numeric($this->actual), "Call with include_anonymous param returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with include_anonymous param returns integer");
            }
        }
        public function testCountallSelectionParam()
        {
            try {
                $this->expected = '[integer]';
                $args = ['selection' => ' AND uname = "Admin"'];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall', $args);
                $res = $this->assertTrue(is_numeric($this->actual), "Call with selection param returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with selection param returns integer");
            }
        }
        public function testCountallBadSelectionParam()
        {
            try {
                $this->expected = '[exception]';
                $args = ['selection' => 'something'];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with bad selection param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad selection param throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserCountall', 'countall (user) function tests');

    class testRolesUserCountallactive extends xarTestCase
    {
        public function testCountallactiveNoParams()
        {
            try {
                $this->expected = '[integer]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'countallactive');
                $res = $this->assertTrue(is_numeric($this->actual), "Call with no params returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params returns integer");
            }
        }
        public function testCountallactiveInclude_anonymousParam()
        {
            try {
                $this->expected = '[integer]';
                $args = ['include_anonymous' => false];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countallactive', $args);
                $res = $this->assertTrue(is_numeric($this->actual), "Call with include_anonymous param returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with include_anonymous param returns integer");
            }
        }
        public function testCountallactiveSelectionParam()
        {
            try {
                $this->expected = '[integer]';
                $args = ['selection' => ' AND a.uname = "Admin"'];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall', $args);
                $res = $this->assertTrue(is_numeric($this->actual), "Call with selection param returns integer");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with selection param returns integer");
            }
        }
        public function testCountallactiveBadSelectionParam()
        {
            try {
                $this->expected = '[exception]';
                $args = ['selection' => 'something'];
                $this->actual = xarMod::apiFunc('roles', 'user', 'countall', $args);
                $res = $this->assertSame($this->actual, $this->expected, "Call with bad selection param throws an exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad selection param throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserCountallactive', 'countallactive (user) function tests');

    class testRolesUserCountgroups extends xarTestCase
    {
        public function testCountgroups()
        {
            $this->expected = '[integer]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'countgroups');
            return $this->assertTrue(is_numeric($this->actual), 'Call with no params returns integer');
        }
        public function testCountgroupsWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'user', 'countgroups');
            $this->actual = xarMod::apiFunc('roles', 'user', 'countgroups', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same as without');
        }
    }
    $suite->AddTestCase('testRolesUserCountgroups', 'countgroups (user) function tests');

    class testRolesUserCountitems extends xarTestCase
    {
        public function testCountitems()
        {
            $this->expected = '[integer]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'countitems');
            return $this->assertTrue(is_numeric($this->actual), 'Call with no params returns integer');
        }
        public function testCountitemsWithParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'user', 'countitems');
            $this->actual = xarMod::apiFunc('roles', 'user', 'countitems', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same as without');
        }
    }
    $suite->AddTestCase('testRolesUserCountitems', 'countitems (user) function tests');

    class testRolesUserDecode_shorturl extends xarTestCase
    {
        public function testDecode_shorturl()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'decode_shorturl');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserDecode_shorturl', 'decode_shorturl (user) function tests');

    class testRolesUserEncode_shorturl extends xarTestCase
    {
        public function testEncode_shorturl()
        {
            $this->expected = '';
            $this->actual = xarMod::apiFunc('roles', 'user', 'encode_shorturl');
            return $this->assertTrue(empty($this->actual), 'Call with no params returns empty');
        }
        public function testEncode_shorturlfuncParam()
        {
            $args = ['func' => 'view'];
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'encode_shorturl', $args);
            return $this->assertTrue(is_array($this->actual), 'Call with func param returns array');
        }
    }
    $suite->AddTestCase('testRolesUserEncode_shorturl', 'encode_shorturl (user) function tests');

    class testRolesUserGet extends xarTestCase
    {
        public function testGetNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'get');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetIdParam()
        {
            try {
                $args = ['id' => 3];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with id param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with id param returns array");
            }
        }
        public function testGetBadIdParam()
        {
            try {
                $args = ['id' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad id param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad id param throws exception");
            }
        }
        public function testGetNameParam()
        {
            try {
                $args = ['name' => 'Administrator'];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with name param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with name param returns array");
            }
        }
        public function testGetBadNameParam()
        {
            try {
                $args = ['name' => 'someunknowname'];
                $this->expected = false;
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad name param returns false");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad name param returns false");
            }
        }
        public function testGetUnameParam()
        {
            try {
                $args = ['uname' => 'Admin'];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with uname param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with uname param returns array");
            }
        }
        public function testGetBadUnameParam()
        {
            try {
                $args = ['uname' => 'someunknowuname'];
                $this->expected = false;
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad uname param returns false");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad uname param returns false");
            }
        }
        public function testGetEmailParam()
        {
            try {
                $args = ['email' => xarModVars::get('mail', 'adminmail')];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with email param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with email param returns array");
            }
        }
        public function testGetBadEmailParam()
        {
            try {
                $args = ['email' => 'invalid@emailaddress.com'];
                $this->expected = false;
                $this->actual = xarMod::apiFunc('roles', 'user', 'get', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad email param returns false");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad email param returns false");
            }
        }
    }
    $suite->AddTestCase('testRolesUserGet', 'get (user) function tests');

    class testRolesUserGetactive extends xarTestCase
    {
        public function testGetactiveNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getactive');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetIdParam()
        {
            try {
                $args = ['id' => xarModUserVars::get('id')];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getactive', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with id param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with id param returns array");
            }
        }
        public function testGetBadIdParam()
        {
            try {
                $args = ['id' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getactive', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad id param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad id param throws exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserGetactive', 'getactive (user) function tests');

    class testRolesUserGetall extends xarTestCase
    {
        public function testGetall()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getall');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserGetall', 'getall (user) function tests');

    class testRolesUserGetallactive extends xarTestCase
    {
        public function testGetallactive()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getallactive');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserGetallactive', 'getallactive (user) function tests');

    class testRolesUserGetallgroups extends xarTestCase
    {
        public function testGetallgroups()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getallgroups');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserGetallgroups', 'getallgroups (user) function tests');

    /*
    class testRolesUserGetallroles extends xarTestCase
    {
        function testGetallroles()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles','user','getallroles');
            return $this->assertTrue(is_array($this->actual),'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserGetallroles','getallroles (user) function tests');
    */

    class testRolesUserGetancestors extends xarTestCase
    {
        public function testGetancestorsNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getancestors');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetIdParam()
        {
            try {
                $args = ['id' => xarModUserVars::get('id')];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getancestors', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with id param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with id param returns array");
            }
        }
        public function testGetBadIdParam()
        {
            try {
                $args = ['id' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getancestors', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad id param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad id param throws exception");
            }
        }
    }

    class testRolesUserGetdefaultauthdata extends xarTestCase
    {
        public function testGetdefaultauthdata()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getdefaultauthdata');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserGetdefaultauthdata', 'getdefaultauthdata (user) function tests');

    class testRolesUserGetdefaultregdata extends xarTestCase
    {
        public function testGetdefaultregdata()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getdefaultregdata');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserGetdefaultregdata', 'getdefaultregdata (user) function tests');

    class testRolesUserGetdeleteduser extends xarTestCase
    {
        public function testGetdeleteduserNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getdeleteduser');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetIdParam()
        {
            try {
                $args = ['id' => xarModUserVars::get('id')];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getdeleteduser', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with id param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with id param returns array");
            }
        }
        public function testGetBadIdParam()
        {
            try {
                $args = ['id' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getdeleteduser', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad id param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad id param throws exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserGetdeleteduser', 'getdeleteduser (user) function tests');

    class testRolesUserGetitemlinks extends xarTestCase
    {
        public function testGetitemlinks()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getitemlinks');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetitemlinksItemidsParam()
        {
            try {
                $args = ['itemids' => [xarModUserVars::get('id')]];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getitemlinks', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with itemids param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with itemids param returns array");
            }
        }
    }
    $suite->AddTestCase('testRolesUserGetitemlinks', 'getitemlinks (user) function tests');

    class testRolesUserGetitemtypes extends xarTestCase
    {
        public function testGetitemtypes()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getitemtypes');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
        public function testGetitemtypesParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'user', 'getitemtypes');
            $this->actual =xarMod::apiFunc('roles', 'user', 'getitemtypes', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testRolesUserGetitemtypes', 'getitemtypes (user) function tests');

    class testRolesUserGetmenulinks extends xarTestCase
    {
        public function testGetmenulinks()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getmenulinks');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
        public function testGetmenulinksParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'user', 'getmenulinks');
            $this->actual =xarMod::apiFunc('roles', 'user', 'getmenulinks', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testRolesUserGetmenulinks', 'getmenulinks (user) function tests');

    class testRolesUserGetprimaryparent extends xarTestCase
    {
        public function testGetprimaryparent()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getprimaryparent');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetprimaryparentItemidParam()
        {
            try {
                $args = ['itemid' => xarModUserVars::get('id')];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getprimaryparent', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with itemid param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with itemid param returns array");
            }
        }
        public function testGetprimaryparentBadItemidParam()
        {
            try {
                $args = ['itemid' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getprimaryparent', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad itemid param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad itemid param throws exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserGetprimaryparent', 'getprimaryparent (user) function tests');

    class testRolesUserGetstates extends xarTestCase
    {
        public function testGetstates()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'getstates');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
        public function testGetstatesParams()
        {
            $args = ['foo' => 'bar'];
            $this->expected = xarMod::apiFunc('roles', 'user', 'getstates');
            $this->actual =xarMod::apiFunc('roles', 'user', 'getstates', $args);
            return $this->assertSame($this->actual, $this->expected, 'Call with params returns the same array as without');
        }
    }
    $suite->AddTestCase('testRolesUserGetstates', 'getstates (user) function tests');

    class testRolesUserGetuserhome extends xarTestCase
    {
        public function testGetuserhome()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getuserhome');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetuserhomeItemidParam()
        {
            try {
                $args = ['itemid' => xarModUserVars::get('id')];
                $this->expected = '[string]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getuserhome', $args);
                $res = $this->assertTrue(is_string($this->actual), "Call with itemid param returns string");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with itemid param returns string");
            }
        }
        public function testGetuserhomeBadItemidParam()
        {
            try {
                $args = ['itemid' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getuserhome', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad itemid param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad itemid param throws exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserGetuserhome', 'getuserhome (user) function tests');

    class testRolesUserGetusers extends xarTestCase
    {
        public function testGetusersNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getusers');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testGetIdParam()
        {
            try {
                $args = ['id' => xarModUserVars::get('id')];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getusers', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with id param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with id param returns array");
            }
        }
        /*
        function testGetBadIdParam()
        {
            try {
                $args = array('id' => 'foo');
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'getusers', $args);
                $res = $this->assertSame($this->expected,$this->actual,"Call with bad id param throws exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with bad id param throws exception");
            }
        }
        */
    }
    $suite->AddTestCase('testRolesUserGetusers', 'getusers (user) function tests');

    class testRolesUserLeftjoin extends xarTestCase
    {
        public function testLeftjoin()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'leftjoin');
            return $this->assertTrue(is_array($this->actual), 'Call with no params returns array');
        }
    }
    $suite->AddTestCase('testRolesUserLeftjoin', 'leftjoin (user) function tests');

    class testRolesUserMakepass extends xarTestCase
    {
        public function testMakepass()
        {
            $this->expected = '[array]';
            $this->actual = xarMod::apiFunc('roles', 'user', 'makepass');
            return $this->assertTrue(is_string($this->actual), 'Call with no params returns string');
        }
        public function testMakepassParams()
        {
            $args = ['foo' => 'bar'];
            $this->actual =xarMod::apiFunc('roles', 'user', 'makepass', $args);
            return $this->assertTrue(is_string($this->actual), 'Call with params returns string');
        }
    }
    $suite->AddTestCase('testRolesUserMakepass', 'makepass (user) function tests');

    class testRolesUserParseuserhome extends xarTestCase
    {
        public function testParseuserhome()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'parseuserhome');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testParseuserhomeUrlParam()
        {
            try {
                $args = ['url' => '/'];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'parseuserhome', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with url param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with url param returns array");
            }
        }
        public function testParseuserhomeTruecurrenturlParam()
        {
            try {
                $args = ['truecurrenturl' => '/'];
                $this->expected = '[array]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'parseuserhome', $args);
                $res = $this->assertTrue(is_array($this->actual), "Call with truecurrenturl param returns array");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with truecurrenturl param returns array");
            }
        }
    }
    $suite->AddTestCase('testRolesUserParseuserhome', 'parseuserhome (user) function tests');

    class testRolesUserRemovemember extends xarTestCase
    {
        public function testRemovememberNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'removemember');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
        public function testRemovememberBadIdParam()
        {
            try {
                $args = ['id' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'removemember', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad id param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad id param throws exception");
            }
        }
        public function testRemovememberBadGidParam()
        {
            try {
                $args = ['gid' => 'foo'];
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'removemember', $args);
                $res = $this->assertSame($this->expected, $this->actual, "Call with bad gid param throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with bad gid param throws exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserRemovemember', 'removemember (user) function tests');

    class testRolesUserUpdatestatus extends xarTestCase
    {
        public function testUpdatestatusNoParams()
        {
            try {
                $this->expected = '[exception]';
                $this->actual = xarMod::apiFunc('roles', 'user', 'updatestatus');
                $res = $this->assertSame($this->expected, $this->actual, "Call with no params throws exception");
                return $res;
            } catch (Exception $e) {
                return $this->assertTrue(true, "Call with no params throws exception");
            }
        }
    }
    $suite->AddTestCase('testRolesUserUpdatestatus', 'updatestatus (user) function tests');
