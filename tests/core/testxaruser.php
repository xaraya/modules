<?php

$suite = new xarTestSuite('User system tests');
$suites[] = $suite;

class testxarUserLogIn extends xarTestCase
{
    public function testxarUserLogInWithValidParams()
    {
        $this->expected = true;
        $this->actual   = xarUser::logIn('admin', 'marc');
        $res = $this->assertSame($this->actual, $this->expected, "A call with correct parameters returns true");
        return $res;
    }
    public function testgetNameWithMissingParams()
    {
        try {
            $this->expected = '[exception]';
            $this->actual   = xarUser::logIn('admin');
            $res = $this->assertSame($this->actual, $this->expected, "A call with fewer than 2 params throws an exception");
            return $res;
        } catch (Exception $e) {
            return $this->assertTrue(true, "A call with fewer than 2 params throws an exception");
        }
    }
    public function testgetNameWithInvalidUser()
    {
        $this->expected = false;
        $this->actual   = xarUser::logIn('admin1', 'marc');
        $res = $this->assertSame($this->actual, $this->expected, "A call with an invalid username returns false");
        return $res;
    }
    public function testgetNameWithBadPassword()
    {
        $this->expected = false;
        $this->actual   = xarUser::logIn('admin', 'dork');
        $res = $this->assertSame($this->actual, $this->expected, "A call with an incorrect password returns false");
        return $res;
    }
}
$suite->AddTestCase('testxarUserLogIn', 'xarUser::logIn($username,$password)');

class testxarUserGetVar extends xarTestCase
{
    public function testxarUserGetVarWithNoParams()
    {
        try {
            $this->expected = '[exception]';
            $this->actual   = xarUser::getVar();
            $res = $this->assertSame($this->actual, $this->expected, "A call with no params throws an exception");
            return $res;
        } catch (Exception $e) {
            return $this->assertTrue(true, "A call with no params throws an exception");
        }
    }
    public function testxarUserGetVarWithNameIsID()
    {
        $this->expected = xarSession::getVar('role_id');
        $this->actual   = xarUser::getVar('id');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('id') returns the id of the current user");
        return $res;
    }
    public function testxarUserGetVarWithNameIsName()
    {
        $this->expected = 'Administrator';
        $this->actual   = xarUser::getVar('name');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('name') returns the name of the current user");
        return $res;
    }
    public function testxarUserGetVarWithNameIsUName()
    {
        $this->expected = 'admin';
        $this->actual   = xarUser::getVar('uname');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('uname') returns the user name of the current user");
        return $res;
    }
    public function testxarUserGetVarWithNameIsEmail()
    {
        $this->expected = 'none@none.com';
        $this->actual   = xarUser::getVar('email');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('email') returns the email of the current user");
        return $res;
    }
    public function testxarUserGetVarWithNameIsPass()
    {
        try {
            $this->expected = '[exception]';
            $this->actual   = xarUser::getVar('pass');
            $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('pass') returns an exception");
            return $res;
        } catch (Exception $e) {
            return $this->assertTrue(true, "xarUser::getVar('pass') returns an exception");
        }
    }
}
$suite->AddTestCase('testxarUserGetVar', 'testxarUserGetVar($name,$id)');
