<?php
// $Id$

// by Edd Dumbill (C) 1999-2001
// <edd@usefulinc.com>
// xmlrpcs.inc,v 1.1.1.1.2.1 2001/11/29 13:40:34 edd

// Copyright (c) 1999,2000,2001 Edd Dumbill.
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions
// are met:
//
//    * Redistributions of source code must retain the above copyright
//      notice, this list of conditions and the following disclaimer.
//
//    * Redistributions in binary form must reproduce the above
//      copyright notice, this list of conditions and the following
//      disclaimer in the documentation and/or other materials provided
//      with the distribution.
//
//    * Neither the name of the "XML-RPC for PHP" nor the names of its
//      contributors may be used to endorse or promote products derived
//      from this software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
// "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
// LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
// FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
// REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
// INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
// HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
// STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
// OF THE POSSIBILITY OF SUCH DAMAGE.

// XML RPC Server class
// requires: xmlrpc.inc


/**
 * Main XML-RPC server class
 *
 * Provides an instance of an XML-RPC server implementation
 * and optionally services a request directly
 *
 * @package services
 */
class xmlrpc_server {
    var $dmap=array();

    function xmlrpc_server($dispMap, $serviceNow=1) {
        global $HTTP_RAW_POST_DATA;
        // dispMap is a despatch array of methods
        // mapped to function names and signatures
        // if a method
        // doesn't appear in the map then an unknown
        // method error is generated
        $this->dmap=$dispMap;
        if ($serviceNow) {
            $this->service();
        }
    }


    function service() {
        $r=$this->parseRequest();
        $payload="<?xml version=\"1.0\"?>\n";
        // XARAYA : allow string return, preparation to let blocklayout do all the answers :-)))
        if(!is_string($r)) {
            $payload .=    $r->serialize();
        } else {
            $payload .= $r;
        }
        Header("Content-type: text/xml");
        Header("Content-length: " . strlen($payload));
        // even in debugging this might be too much
        //xarLogMessage("PAYLOAD:" . $payload, XARLOG_LEVEL_WARNING);
        print $payload;
    }

    function verifySignature($in, $sig) {
        for($i=0; $i<sizeof($sig); $i++) {
            // check each possible signature in turn
            $cursig=$sig[$i];
            if (sizeof($cursig)==$in->getNumParams()+1) {
                $itsOK=1;
                for($n=0; $n<$in->getNumParams(); $n++) {
                    $p=$in->getParam($n);
                    // print "<!-- $p -->\n";
                    if ($p->kindOf() == "scalar") {
                        $pt=$p->scalartyp();
                    } else {
                        $pt=$p->kindOf();
                    }
                    // $n+1 as first type of sig is return type
                    if ($pt != $cursig[$n+1]) {
                        $itsOK=0;
                        $pno=$n+1; $wanted=$cursig[$n+1]; $got=$pt;
                        break;
                    }
                }
                if ($itsOK)
                    return array(1);
            } else {
                $wanted=sizeof($cursig) . " parameters";
                $got = $in->getNumParams()+1;
                $pno = $got;
            }
        }
        return array(0, "Wanted ${wanted}, got ${got} at param ${pno})");
    }

    function parseRequest($data="") {
        global $_xh,$HTTP_RAW_POST_DATA;
        global $xmlrpcerr;
        global $xmlrpcstr, $xmlrpcerrxml, $xmlrpc_defencoding, $_xmlrpcs_dmap;


        if ($data=="") {
            $data=$HTTP_RAW_POST_DATA;
        }
        $parser = xml_parser_create($xmlrpc_defencoding);

        $parserasint = (int)$parser;
        $_xh[$parserasint]=array();
        $_xh[$parserasint]['st']="";
        $_xh[$parserasint]['cm']=0;
        $_xh[$parserasint]['isf']=0;
        $_xh[$parserasint]['params']=array();
        $_xh[$parserasint]['method']="";

        // decompose incoming XML into request structure
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
        xml_set_element_handler($parser, "xmlrpc_se", "xmlrpc_ee");
        xml_set_character_data_handler($parser, "xmlrpc_cd");
        xml_set_default_handler($parser, "xmlrpc_dh");
        if (!xml_parse($parser, $data, 1)) {
            // return XML error as a faultCode
            $r=new xmlrpcresp(0,
                              $xmlrpcerrxml+xml_get_error_code($parser),
                              sprintf("XML error: %s at line %d",
                                      xml_error_string(xml_get_error_code($parser)),
                                      xml_get_current_line_number($parser)));
            xml_parser_free($parser);
        } else {
            xml_parser_free($parser);
            $m=new xmlrpcmsg($_xh[$parserasint]['method']);
            // now add parameters in
            $plist="";
            for($i=0; $i<sizeof($_xh[$parserasint]['params']); $i++) {
                //print "<!-- " . $_xh[$parserasint]['params'][$i]. "-->\n";
                $plist.="$i - " .  $_xh[$parserasint]['params'][$i]. " \n";
                $result=null;
                $result = @eval('$m->addParam(' . $_xh[$parserasint]['params'][$i]. ");");
                if($result === false) {
                    $r=new xmlrpcresp(0,
                                    $xmlrpcerr["incorrect_params"],
                                    $xmlrpcstr["incorrect_params"]);
                    return $r;
                }
            }
            // uncomment this to really see what the server's getting!
            //xarLogMessage($plist,XARLOG_LEVEL_WARNING);
            // now to deal with the method
            $methName=$_xh[$parserasint]['method'];
            if (ereg("^system\.", $methName)) {
                $sysCall=1;
            } else {
                $sysCall=0;
            }
            $dmap=$this->dmap;

            if (isset($dmap[$methName]['function'])) {
                // dispatch if exists
                if (isset($dmap[$methName]['signature'])) {
                    $sr=$this->verifySignature($m,$dmap[$methName]['signature'] );
                }
                if ( (!isset($dmap[$methName]['signature']))
                     || $sr[0]) {
                    // if no signature or correct signature
                    if ($sysCall) {
                        xarLogMessage("calling syscall: " . $dmap[$methName]['function']);
                        $xarayafunc = __constructxarfunc($dmap[$methName]['function'],'array(\'server\' => $this, \'msg\' => $m)');
                    } else {
                        xarLogMessage("calling " . $dmap[$methName]['function']);
                        // we need to split the function in three parts here to be 'split independent'
                        // for xaraya and to allow the implicit loading to work
                        // module_type_function -> xarModFunc(module,type,function)
                        $xarayafunc = __constructxarfunc($dmap[$methName]['function'],'$m');
                    }
                    try {
                        eval('$r=' . $xarayafunc);
                    } catch (Exception $e) {}
                } else {
                    xarLogMessage("Signature is wrong");
                    $r=new xmlrpcresp(0,
                                      $xmlrpcerr["incorrect_params"],
                                      $xmlrpcstr["incorrect_params"].": ". $sr[1]);
                }
            } else {
                // else prepare error response
                xarLogMessage("Client dispatched unknown method ($methName)", XARLOG_LEVEL_WARNING);
                $r=new xmlrpcresp(0,
                                  $xmlrpcerr["unknown_method"],
                                  $xmlrpcstr["unknown_method"]);
            }
        }
        //xarLogMessage("Going to return", XARLOG_LEVEL_WARNING);
        //xarLogMessage(print_r($r,true),XARLOG_LEVEL_WARNING);
        return $r;
    }

}

/**
 * Private helper function
 *
 * Construct a xaraya function based on a long function
 * name as in module_type_func and return the proper
 * xarModFunc(module,type,func) or
 * xarModAPIFunc(module,type,func) call
 *
 * @param $funcname Name of the function in 'normal' form
 * @param $args Arguments to pass to the function
 */
function __constructxarfunc($funcname,$args) {
    $parts = explode("_",$funcname);
    if(substr($parts[1],-3,3)=='api') {
        // Api function
        $type = substr($parts[1],0,-3);
        $xarfunc = "xarModApiFunc('$parts[0]','$type','$parts[2]',array('msg' => $args));";
        return $xarfunc;
    } else {
        $xarfunc = "xarModFunc('$parts[0]','$parts[1]','$parts[2]',array('msg' => $args));";
        return $xarfunc;
    }
}
?>
