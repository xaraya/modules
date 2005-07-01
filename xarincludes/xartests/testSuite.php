<?php

/*
 * Settings to run the tests under
 *
 */
 
$LOCALSERVER="xartest.hsdev.com";
$URI="/ws.php?type=xmlrpc";
$HTTPSSERVER="xmlrpc.usefulinc.com";
$DEBUG=0;
$ERRORBASE=800;

/* Include the client classes so we can play */
include('../xmlrpc.inc');

// play nice to modern PHP installations with register globals OFF
// note: php 3 does not have ini_get()
if(phpversion() >= 4) {
    if(!ini_get('register_globals') && function_exists('import_request_variables')) {
        @import_request_variables('GP');
    }
}

// Validate the settings
if(!isset($DEBUG)) $DEBUG = 0;
if(!isset($ERRORBASE)) $ERRORBASE = 0;
if(!isset($LOCALSERVER)) {
    if(isset($HTTP_HOST)) {
        $LOCALSERVER = $HTTP_HOST;
    }
    elseif(isset($_SERVER['HTTP_HOST'])) {
        $LOCALSERVER = $_SERVER['HTTP_HOST'];
    } else {
        $LOCALSERVER = 'localhost';
    }
}

if(!isset($HTTPSSERVER)) $HTTPSSERVER = 'xmlrpc.usefulinc.com';

if(!isset($URI)) {
    // play nice to php 3 and 4-5 in retrieving URL of server.php
    if(isset($REQUEST_URI)) {
        $URI = str_replace('testsuite.php', 'server.php', $REQUEST_URI);
    }
    elseif(isset($_SERVER['PHP_SELF'])) {
        $URI = str_replace('testsuite.php', 'server.php', $_SERVER['PHP_SELF']);
    } else {
        $URI = '/server.php';
    }
}

// Assume testdata is in the same dir as this file 
if(!isset($LOCALPATH))  $LOCALPATH = dirname(__FILE__);

/** End of preparation start the testcases */

/** Override some functions, easier than loading the whole core **/
function xarLogMessage() 
{}

class LocalhostTests extends xarTestCase
{
        function setUp()
        {   
            global $DEBUG, $LOCALSERVER, $URI;
            $this->client=&new xmlrpc_client($URI, $LOCALSERVER, 80);
            if ($DEBUG) $this->client->setDebug(1);
        }

        // Warming up, irrelevant test :-)
        function testCountEntities()
        {
                $sendstring = "h'fd>onc>>l>>rw&bpu>q>e<v&gxs<ytjzkami<";
                $f = new xmlrpcmsg('validator1.countTheEntities',array(
                        new xmlrpcval($sendstring, 'string')
                ));
                $r = $this->client->send($f);
                $v = $r->value();

                $got = '';
                $expected = '37210';
                $expect_array = array('ctLeftAngleBrackets','ctRightAngleBrackets','ctAmpersands','ctApostrophes','ctQuotes');

                while(list(,$val) = each($expect_array))
                {
                        $b = $v->structmem($val);
                        $got .= $b->me['int'];
                }

                return $this->assertEquals($got, $expected, 0,'validator1.countTheEntities');
        }

        function _multicall_msg($method, $params)
        {
                $struct['methodName'] = new xmlrpcval($method, 'string');
                $struct['params'] = new xmlrpcval($params, 'array');
                return new xmlrpcval($struct, 'struct');
        }

        // We dont have multicall, so dont run this test
        function _testServerMulticall()
        {
                // We manually construct a system.multicall() call to ensure
                // that the server supports it.

                // Based on http://xmlrpc-c.sourceforge.net/hacks/test_multicall.py
                        $good1 = $this->_multicall_msg(
                                'system.methodHelp',
                                array(php_xmlrpc_encode('system.listMethods')));
                        $bad = $this->_multicall_msg(
                                'test.nosuch',
                                array(php_xmlrpc_encode(1), php_xmlrpc_encode(2)));
                        $recursive = $this->_multicall_msg(
                                'system.multicall',
                                array(new xmlrpcval(array(), 'array')));
                        $good2 = $this->_multicall_msg(
                                'system.methodSignature',
                                array(php_xmlrpc_encode('system.listMethods')));
                        $arg = new xmlrpcval(
                                array($good1, $bad, $recursive, $good2),
                                'array'
                        );

                $f = new xmlrpcmsg('system.multicall', array($arg));
                $r = $this->client->send($f);
                $this->assert($r->faultCode() == 0, "fault from system.multicall");

                $v = $r->value();
                $this->assert($v->arraysize() == 4, "bad number of return values");

                $r1 = $v->arraymem(0);
                $this->assert(
                        $r1->kindOf() == 'array' && $r1->arraysize() == 1,
                        "did not get array of size 1 from good1");

                $r2 = $v->arraymem(1);
                $this->assert(
                        $r2->kindOf() == 'struct',
                        "no fault from bad");

                $r3 = $v->arraymem(2);
                $this->assert(
                        $r3->kindOf() == 'struct',
                        "recursive system.multicall did not fail");

                $r4 = $v->arraymem(3);
                $this->assert(
                        $r4->kindOf() == 'array' && $r4->arraysize() == 1,
                        "did not get array of size 1 from good2");
        }

        function _testClientMulticall()
        {
                // This test will NOT pass if server does not support system.multicall.
                // We should either fix it or build a new test for it...

                        $good1 = new xmlrpcmsg('system.methodHelp',
                                array(php_xmlrpc_encode('system.listMethods')));
                        $bad = new xmlrpcmsg('test.nosuch',
                                array(php_xmlrpc_encode(1), php_xmlrpc_encode(2)));
                        $recursive = new xmlrpcmsg('system.multicall',
                                array(new xmlrpcval(array(), 'array')));
                        $good2 = new xmlrpcmsg('system.methodSignature',
                                array(php_xmlrpc_encode('system.listMethods'))
                        );

                $r = $this->client->send(array($good1, $bad, $recursive, $good2));

                $this->assert(count($r) == 4, "wrong number of return values");

                $this->assert($r[0]->faultCode() == 0, "fault from good1");
                $val = $r[0]->value();
                $this->assert(
                        $val->kindOf() == 'scalar' && $val->scalartyp() == 'string',
                        "good1 did not return string");
                $this->assert($r[1]->faultCode() != 0, "no fault from bad");
                $this->assert($r[2]->faultCode() != 0, "no fault from recursive system.multicall");
                $this->assert($r[3]->faultCode() == 0, "fault from good2");
                $val = $r[3]->value();
                $this->assert($val->kindOf() == 'array', "good2 did not return array");


                // This is the only assert in this test which should fail
                // if the test server does not support system.multicall.
                $this->assert($this->client->no_multicall == false,
                        "server does not support system.multicall");
        }

        function testZeroParams()
        {
                $f = new xmlrpcmsg('system.listMethods');
                $r = $this->client->send($f);
                $v = $r->faultCode();
                return $this->assertEquals($v, 0,0,"Zero parameters in system.listMethods");
        }

        function testCodeInjectionServerSide ()
        {
                global $ERRORBASE;
                
                $f = new xmlrpcmsg('system.MethodHelp');
                $f->payload = "<?xml version=\"1.0\"?><methodCall><methodName>system.MethodHelp</methodName><params><param><value><name>','')); echo('gotcha!'); die(); //</name></value></param></params></methodCall>";
                $r = $this->client->send($f);
                $v = $r->faultCode();
                return $this->assertEquals($v, $ERRORBASE + 3, 0, "Server side code injection returns error 3");
        }
}

class FileCasesTests extends xarTestCase
{
        function setup()
        {
                global $DEBUG; global $LOCALPATH;
                
                $this->msg = new xmlrpcmsg('dummy');
                if ($DEBUG) $this->msg->debug = true;
                $this->root=$LOCALPATH;
        }

        // No clue what this is, dont run it.
        function _testStringBug ()
        {
                $fp=fopen($this->root.'/bug_string.xml', 'r');
                $r=$this->msg->parseResponseFile($fp);
                $v=$r->value();
                fclose($fp);
                $s=$v->structmem('sessionID');
                return $this->assertEquals( $s->scalarval(),'S300510007I',0,'Character data outside tag');
        }

        function testWhiteSpace ()
        {
                $fp=fopen($this->root.'/bug_whitespace.xml', 'r');
                $r=$this->msg->parseResponseFile($fp);
                $v=$r->value();
                fclose($fp);
                $s=$v->structmem('content');
                return $this->assertEquals($s->scalarval(),"hello world. 2 newlines follow\n\n\nand there they were.", 0,"Parse with newlines in data");
        }

        function _testWeirdHTTP ()
        {
                $fp=fopen($this->root.'/bug_http.xml', 'r');
                $r=$this->msg->parseResponseFile($fp);
                $v=$r->value();
                fclose($fp);
                ///var_dump($r);die();
                $s=$v->structmem('content');
                return $this->assertEquals($s->scalarval(), "hello world. 2 newlines follow\n\n\nand there they were.", 0, "Parsing weird HTTP");
        }

        function testCodeInjection ()
        {
                $fp=fopen($this->root.'/bug_inject.xml', 'r');
                $r=$this->msg->parseResponseFile($fp);
                $v=$r->value();
                fclose($fp);
                return $this->assertEquals($v->structsize(),6,0,'Code injection bug');
        }

}

class ParsingBugsTests extends xarTestCase
{
        function setup()
        {
            $this->numberxml = 
'<?xml version="1.0"?>
<methodResponse>
<params>
<param>
<value>
<struct>
<member> 
<name>integer1</name>
<value><int>01</int></value>
</member>
<member> 
<name>float1</name>
<value><double>01.10</double></value>
</member>
<member> 
<name>integer2</name>
<value><int>+1</int></value>
</member>
<member> 
<name>float2</name>
<value><double>+1.10</double></value>
</member>
<member>
<name>float3</name>
<value><double>-1.10e2</double></value>
</member>
</struct>
</value>
</param>
</params>
</methodResponse>';
        }
        
        function testMinusOneString()
        {
                $v=new xmlrpcval('-1');
                $u=new xmlrpcval('-1', 'string');
                return $this->assertEquals($u->scalarval(), $v->scalarval(),0,'Minus 1 (-1) as string');
        }

        function testUnicodeInErrorString()
        {
                $response = utf8_encode(
'<?xml version="1.0"?>
<!-- found by G. giunta, covers what happens when lib receives
  UTF8 chars in reponse text and comments -->
<!-- àüè&#224;&#252;&#232; -->
<methodResponse>
<fault>
<value>
<struct>
<member>
<name>faultCode</name>
<value><int>888</int></value>
</member>
<member>
<name>faultString</name>
<value><string>àüè&#224;&#252;&#232;</string></value>
</member>
</struct>
</value>
</fault>
</methodResponse>');
                $m=new xmlrpcmsg('dummy');
                $r=$m->parseResponse($response);
                $v=$r->faultString();
                $str='àüèàüè';
                return $this->assertEquals($v, $str,0,'Unicode in error string');
        }

        function testInteger1 ()
        {
            $m = new xmlrpcmsg('dummy');
            $r = $m->parseResponse($this->numberxml);
            $v=$r->value();
            $s=$v->structmem('integer1');
            return $this->assertEquals(1, $s->scalarval(),0,'Data type tests (integer1)');
        }
        
        function testInteger2 ()
        {
            $m = new xmlrpcmsg('dummy');
            $r = $m->parseResponse($this->numberxml);
            $v=$r->value();
            $s=$v->structmem('integer2');
            return $this->assertEquals($s->scalarval(),1,0,'Data type tests (integer2)');
        }
        
        function testFloat1 ()
        {
            $m = new xmlrpcmsg('dummy');
            $r = $m->parseResponse($this->numberxml);
            $v=$r->value();
            $s=$v->structmem('float1');
            return $this->assertEquals($s->scalarval(),1.1,0,'Data type tests (float1)');
        }
        
        function testFloat2 ()
        {
            $m = new xmlrpcmsg('dummy');
            $r = $m->parseResponse($this->numberxml);
            $v=$r->value();
            $s=$v->structmem('float2');
            return $this->assertEquals($s->scalarval(),1.1,0,'Data type tests (float2)');
        }
        
        function testFloat3 ()
        {
                $m=new xmlrpcmsg('dummy');
                $r=$m->parseResponse($this->numberxml);
                $v=$r->value();

                $x=$v->structmem('float3');
                return  $this->assertEquals($x->scalarval(),-110,0,'Data type tests (float3)');
        }

        function testAddScalarToStruct()
        {
                $v=new xmlrpcval(array('a' => 'b'), 'struct');
                $r=$v->addscalar('c');
                return $this->assertEquals($r, 0, 0, 'Add scalar to struct');
        }

        function testAddStructToStruct()
        {
                $v=new xmlrpcval(array('a' => new xmlrpcval('b')), 'struct');
                $r=$v->addstruct(array('b' => new xmlrpcval('c')));
                $res = $this->assertEquals($v->structsize(),2,0,'Initializing struct with 2 elements');
                if(!$res['value']) return $res; // We dont even get this far.
                $r=$v->addstruct(array('b' => new xmlrpcval('b')));
                return $this->assertEquals($v->structsize(),2,0,'Add struct to a struct');
        }

        function testAddArrayToArray()
        {
                $v=new xmlrpcval(array(new xmlrpcval('a'), new xmlrpcval('b')), 'array');
                $r=$v->addarray(array(new xmlrpcval('b'), new xmlrpcval('c')));
                return $this->assertEquals($v->arraysize(),4,0,'Add array to array');
        }

        // We cant run it like this, we dont use xmlrpc_encode
        function _testEncodeArray()
        {
                $r=range(1, 100);
                $v = php_xmlrpc_encode($r);
                var_dump($v);die();
                return $this->assertEquals($v->kindof(),'array',0,'Encode array');
        }

        // We cant run it like this, we dont use xmlrpc_encode
        function _testEncodeRecursive()
        {
                $v = php_xmlrpc_encode(php_xmlrpc_encode('a simple string'));
                return $this->assertEquals($v->kindof(),'scalar',0,'Encdoe recursive');
        }
}

class InvalidHostTests extends xarTestCase
{
        function setUp()
        {
                global $DEBUG,$LOCALSERVER;
                $this->client=new xmlrpc_client('/NOTEXIST.php', $LOCALSERVER, 80);
                if($DEBUG)
                {
                        $this->client->setDebug(1);
                }
        }

        function test404()
        {
                $f=new xmlrpcmsg('examples.echo',array(
                        new xmlrpcval('hello', 'string')
                ));
                $r=$this->client->send($f);
                return $this->assertEquals($r->faultCode(),5,0,'Invalid host test');
        }
}


$tmp = new xarTestSuite('XML-RPC tests');
$tmp->AddTestCase('LocalhostTests','Local host tests');
$tmp->AddTestCase('FileCasesTests', 'File Cases tests');
$tmp->AddTestCase('ParsingBugsTests','Parsing bugs tests');
$tmp->AddTestCase('InvalidHostTests','Invalid host tests');
$suites[] = $tmp;
?>