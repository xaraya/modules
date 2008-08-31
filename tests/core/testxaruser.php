<?php

$suite = new xarTestSuite('User system tests');
$suites[] = $suite;

class testxarUserLogin extends xarTestCase {
               
    function testxarUserLoginWithValidParams() {
        $this->expected = true;
        $this->actual   = xarUserLogin('admin','marc');
        $res = $this->assertSame($this->actual,$this->expected,"A call with correct parameters returns true");
        return $res;
    }
    function testgetNameWithMissingParams() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarUserLogin('admin');
            $res = $this->assertSame($this->actual,$this->expected,"A call with fewer than 2 params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"A call with fewer than 2 params throws an exception");
        }
    }
    function testgetNameWithInvalidUser() {
        $this->expected = false;
        $this->actual   = xarUserLogin('admin1','marc');
        $res = $this->assertSame($this->actual,$this->expected,"A call with an invalid username returns false");
        return $res;
    }
    function testgetNameWithBadPassword() {
        $this->expected = false;
        $this->actual   = xarUserLogin('admin','dork');
        $res = $this->assertSame($this->actual,$this->expected,"A call with an incorrect password returns false");
        return $res;
    }

}
$suite->AddTestCase('testxarUserLogin','xarUserLogin($username,$password)');

class testxarUserGetVar extends xarTestCase {
               
    function testxarUserGetVarWithNoParams() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarUserGetVar();
            $res = $this->assertSame($this->actual,$this->expected,"A call with no params throws an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"A call with no params throws an exception");
        }
    }
    function testxarUserGetVarWithNameIsID() {
        $this->expected = xarSession::getVar('role_id');
        $this->actual   = xarUserGetVar('id');
        $res = $this->assertSame($this->actual,$this->expected,"xarUserGetVar('id') returns the id of the current user");
        return $res;
    }
    function testxarUserGetVarWithNameIsName() {
        $this->expected = 'Administrator';
        $this->actual   = xarUserGetVar('name');
        $res = $this->assertSame($this->actual,$this->expected,"xarUserGetVar('name') returns the name of the current user");
        return $res;
    }
    function testxarUserGetVarWithNameIsUName() {
        $this->expected = 'admin';
        $this->actual   = xarUserGetVar('uname');
        $res = $this->assertSame($this->actual,$this->expected,"xarUserGetVar('uname') returns the user name of the current user");
        return $res;
    }
    function testxarUserGetVarWithNameIsEmail() {
        $this->expected = 'none@none.com';
        $this->actual   = xarUserGetVar('email');
        $res = $this->assertSame($this->actual,$this->expected,"xarUserGetVar('email') returns the email of the current user");
        return $res;
    }
    function testxarUserGetVarWithNameIsPass() {
        try{
            $this->expected = '[exception]';
            $this->actual   = xarUserGetVar('pass');
            $res = $this->assertSame($this->actual,$this->expected,"xarUserGetVar('pass') returns an exception");
            return $res;
        } catch(Exception $e) {
            return $this->assertTrue(true,"xarUserGetVar('pass') returns an exception");
        }
    }

}
$suite->AddTestCase('testxarUserGetVar','testxarUserGetVar($name,$id)');

?>