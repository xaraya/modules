<?php

$suite = new xarTestSuite('User system tests');
$suites[] = $suite;

class testxarUser::logIn extends xarTestCase
{
    public function testxarUser::logInWithValidParams()
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
$suite->AddTestCase('testxarUser::logIn', 'xarUser::logIn($username,$password)');

class testxarUser::getVar extends xarTestCase
{
    public function testxarUser::getVarWithNoParams()
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
    public function testxarUser::getVarWithNameIsID()
    {
        $this->expected = xarSession::getVar('role_id');
        $this->actual   = xarUser::getVar('id');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('id') returns the id of the current user");
        return $res;
    }
    public function testxarUser::getVarWithNameIsName()
    {
        $this->expected = 'Administrator';
        $this->actual   = xarUser::getVar('name');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('name') returns the name of the current user");
        return $res;
    }
    public function testxarUser::getVarWithNameIsUName()
    {
        $this->expected = 'admin';
        $this->actual   = xarUser::getVar('uname');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('uname') returns the user name of the current user");
        return $res;
    }
    public function testxarUser::getVarWithNameIsEmail()
    {
        $this->expected = 'none@none.com';
        $this->actual   = xarUser::getVar('email');
        $res = $this->assertSame($this->actual, $this->expected, "xarUser::getVar('email') returns the email of the current user");
        return $res;
    }
    public function testxarUser::getVarWithNameIsPass()
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
$suite->AddTestCase('testxarUser::getVar', 'testxarUser::getVar($name,$id)');
