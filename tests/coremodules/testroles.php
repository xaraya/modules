<?php

    $suite = new xarTestSuite('Roles module tests');
    $suites[] = $suite;

    class testRolesAdminAddmember extends xarTestCase {

        function testAddmemberNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'addmember');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

    }
    $suite->AddTestCase('testRolesAdminAddmember','addmember (admin) function tests');

    class testRolesAdminClearsessions extends xarTestCase {

        function testClearsessions() {
            try{
                $this->expected = true;
                $this->actual = xarModAPIFunc('roles', 'admin', 'clearsessions');
                return $this->assertTrue($this->actual,"Call with no params returns true");
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

        // Note: this function needs looking at

    }
    $suite->AddTestCase('testRolesAdminClearsessions','clearsessions (admin) function tests');

    class testRolesAdminGetgroupmenulinks extends xarTestCase {

        function testGetgroupmenulinks() {
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('roles','admin','getgroupmenulinks');
            return $this->AssertTrue(is_array($this->actual),'Call with no params returns an array');
        }

        function testGetgroupmenulinksWithParams() {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('roles','admin','getgroupmenulinks');
            $this->actual =xarModAPIFunc('roles','admin','getgroupmenulinks', $args);
            return $this->AssertSame($this->actual,$this->expected,'Call with params returns the same array as without');
        }

    }
    $suite->AddTestCase('testRolesAdminGetgroupmenulinks','getgroupmenulinks (admin) function tests');

    class testRolesAdminGetmenulinks extends xarTestCase {

        function testGetmenulinks() {
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('roles','admin','getmenulinks');
            return $this->AssertTrue(is_array($this->actual),'Call with no params returns an array');
        }

        function testGetmenulinksWithParams() {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('roles','admin','getmenulinks');
            $this->actual = xarModAPIFunc('roles','admin','getmenulinks', $args);
            return $this->AssertSame($this->actual,$this->expected,'Call with params returns the same array as without');
        }

    }
    $suite->AddTestCase('testRolesAdminGetmenulinks','getmenulinks (admin) function tests');

    class testRolesAdminGetmessageincludestring extends xarTestCase {

        function testGetmessageincludestringNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'getmessageincludestring');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

        function testGetmessageincludestringBadTemplateParam() {
            try{
                $args = array('template' => 'someinvalidtemplate');
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'getmessageincludestring',$args);
                $res =  $this->AssertSame($this->actual,$this->expected,'Call with an invalid template param throws an exception');
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,'Call with an invalid template param throws an exception');
            }
        }

        function testGetmessageincludestringBadModuleParam() {
            try{
                $args = array('module' => 'someinvalidmodule', 'template' => 'message-vars');
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'getmessageincludestring',$args);
                $res =  $this->AssertSame($this->actual,$this->expected,'Call with an invalid module param throws an exception');
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,'Call with an invalid module param throws an exception');
            }
        }

        function testGetmessageincludestring() {
            $args = array('module' => 'mail', 'template' => 'message-vars');
            $this->expected = '[string]';
            $this->actual = xarModAPIFunc('roles', 'admin', 'getmessageincludestring',$args);
            return $this->AssertTrue(is_string($this->actual),'Call with valid module name and template params returns a string');
        }

    }
    $suite->AddTestCase('testRolesAdminGetmessageincludestring','getmessageincludestring (admin) function tests');

    class testRolesAdminGetmessagestrings extends xarTestCase {

        function testGetmessagestringsNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'getmessagestrings');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

        function testGetmessagestringsBadTemplateParam() {
            try{
                $args = array('template' => 'someinvalidtemplate');
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'getmessagestrings',$args);
                $res =  $this->AssertSame($this->actual,$this->expected,'Call with an invalid template param throws an exception');
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,'Call with an invalid template param throws an exception');
            }
        }

        function testGetmessagestringsBadModuleParam() {
            try{
                $args = array('module' => 'someinvalidmodule', 'template' => 'welcome');
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'getmessagestrings',$args);
                $res =  $this->AssertSame($this->actual,$this->expected,'Call with an invalid module param throws an exception');
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,'Call with an invalid module param throws an exception');
            }
        }

        function testGetmessagestrings() {
            $args = array('module' => 'roles', 'template' => 'welcome');
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('roles', 'admin', 'getmessagestrings',$args);
            return $this->AssertTrue(is_array($this->actual),'Call with valid module name and template params returns an array');
        }

    }
    $suite->AddTestCase('testRolesAdminGetmessagestrings','getmessagestrings (admin) function tests');

    class testRolesAdminMenu extends xarTestCase {

        function testMenu() {
            $this->expected = '[array]';
            $this->actual = xarModAPIFunc('roles','admin','menu');
            return $this->AssertTrue(is_array($this->actual),'Call with no params returns an array');
        }

        function testMenuWithParams() {
            $args = array('foo' => 'bar');
            $this->expected = xarModAPIFunc('roles','admin','menu');
            $this->actual = xarModAPIFunc('roles','admin','menu', $args);
            return $this->AssertSame($this->actual,$this->expected,'Call with params returns the same array as without');
        }

    }
    $suite->AddTestCase('testRolesAdminMenu','menu (admin) function tests');

    class testRolesAdminPurge extends xarTestCase {

        function testPurgeNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'purge');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }
        /*
        function testPurgeWithActiveStateParams() {
            try{
                $this->expected = '[exception]';
                $args = array('state' => ROLES_STATE_ACTIVE);
                $this->actual = xarModAPIFunc('roles', 'admin', 'purge', $args);
                $res = $this->assertSame($this->actual,$this->expected,"Call with bad state param throws exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with bad state param throws exception");
            }
        }
        */
    }
    $suite->AddTestCase('testRolesAdminPurge','purge (admin) function tests');

    class testRolesAdminRecall extends xarTestCase {

        function testRecallNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'recall');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

    }
    $suite->AddTestCase('testRolesAdminRecall','recall (admin) function tests');

    class testRolesAdminSendusermail extends xarTestCase {

        function testSendusermailNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'sendusermail');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

    }
    $suite->AddTestCase('testRolesAdminSendusermail','sendusermail (admin) function tests');

    class testRolesAdminStateupdate extends xarTestCase {
        function testStateupdateNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'admin', 'stateupdate');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }
        function testStateupdateBadIDParam() {
            try{
                $this->expected = '[exception]';
                $args = array('state' => ROLES_STATE_ACTIVE, 'id' => 12345678);
                $this->actual = xarModAPIFunc('roles', 'admin', 'stateupdate', $args);
                $res = $this->assertSame($this->actual,$this->expected,"Call with bad id param throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with bad id param throws an exception");
            }
        }
    }
    $suite->AddTestCase('testRolesAdminStateupdate','stateupdate (admin) function tests');


    /* user api */

    class testRolesUserAddmember extends xarTestCase {

        function testAddmemberNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'user', 'addmember');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

    }
    $suite->AddTestCase('testRolesUserAddmember','addmember (user) function tests');

    class testRolesUserCheckprivilege extends xarTestCase {

        function testCheckprivilegeNoParams() {
            try{
                $this->expected = '[exception]';
                $this->actual = xarModAPIFunc('roles', 'user', 'checkprivilege');
                $res = $this->assertSame($this->actual,$this->expected,"Call with no params throws an exception");
                return $res;
            } catch(Exception $e) {
                return $this->assertTrue(true,"Call with no params throws an exception");
            }
        }

    }
    $suite->AddTestCase('testRolesUserCheckprivilege','checkprivilege (user) function tests');

?>