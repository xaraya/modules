<?php
/**
    Run the Mailbag
   
    @returns true if successful
             otherwise false
*/
function mailbag_adminapi_runmailbag()
{

    $mailbagdebug = true;

    $msgindex = array();

    // Get blacklists
    $sblacklist = xarModAPIFunc('mailbag', 'admin', 'getsblacklist');
    $rblacklist = xarModAPIFunc('mailbag', 'admin', 'getrblacklist');
    $ublacklist = xarModAPIFunc('mailbag', 'admin', 'getublacklist');

    // Get Mail Lists
    $maillist = xarModAPIFunc('mailbag', 'admin', 'getmaillist');
    
    // Get POP connection
    
   // ----------------------------------------------------------------------
   // Make the connection to the POP3 server
   // ----------------------------------------------------------------------
   if($mailbagdebug) echo "Connect to POP3 server<br>";

    // Setup connection to mail server
    if( ($i = strpos(xarModGetVar('mail', 'popserver'), ":" )) > 0 ) 
    {
        $h = substr(xarModGetVar('mailbag', 'popserver'), 0, $i );
        $p = intval(substr(xarModGetVar('mailbag', 'popserver'), $i + 1 ) );
    }
    else 
    {
        $h = xarModGetVar('mailbag', 'popserver');
        $p = 110;
    }

    // Open connection
    $mbsocket = fsockopen( $h, $p, $errno, $errstr, 15 );
    if( !$mbsocket ) 
    {
        die ( "POP3 server connection refused: " . $h . ":" . $p );
    }

    // Get POP banner. If it begins with a '+' things are ok, otherwise
    // there is an error.
    $line = fgets( $mbsocket, 500 );
    if( substr( $line, 0, 1 ) != "+" ) 
    {
        $errstr = substr( $line, 5 );
        die ("POP3 server connection error: ".$errstr);
    }

    // Send the username and test for a '+' result.
    fputs( $mbsocket, "USER " . xarModGetVar('mailbag', 'popuser') . "\r\n" );
    $line = fgets( $mbsocket, 500 );
    if( substr( $line, 0, 1 ) != "+" ) 
    {
        $errstr = substr( $line, 5 );
        die ("POP3 server username error: ".$errstr);
    }

    // Send the password and test for a '+' result.
    fputs( $mbsocket, "PASS " . xarModGetVar('mailbag', 'poppass') . "\r\n" );
    $line = fgets( $mbsocket, 500 );
    if( substr( $line, 0, 1 ) != "+" ) 
    {
        $errstr = substr( $line, 5 );
        die ("POP3 server password error: ".$errstr);
    }
    
    // ----------------------------------------------------------------------
    // Count the number of messages on the server
    // ----------------------------------------------------------------------
    fputs( $mbsocket, "STAT\r\n" );
    $stat = chop( fgets( $mbsocket, 500 ) );
    $msgcount = intval( substr( $stat, strpos( $stat, " " ) + 1 ) );
   
    if ($mailbagdebug) echo $msgcount." messages to be processed<br>";

    // ----------------------------------------------------------------------
    // Get the size of each message
    // ----------------------------------------------------------------------
    if ($mailbagdebug) echo "Load messages size information<br>";

    fputs( $mbsocket, "LIST\r\n" );
    $line = chop( fgets( $mbsocket, 500 ) );
    if( substr( $line, 0, 1 ) == "+" ) 
    {
        $i = 1;
        while( ($line = chop( fgets( $mbsocket, 500 ) )) != "." )
            $msgindex[$i++]['size'] = intval( substr( $line, strrpos( $line, " " ) ) );
    }

    // ----------------------------------------------------------------------
    // Get the required message headers
    // ----------------------------------------------------------------------

    for( $i = 1; $i <= $msgcount; $i++ ) 
    {
   
        if ($mailbagdebug) echo "Load header info of message ".$i."<br>";

        // Request the top of the message (the headers) plus 0 lines of body
        fputs( $mbsocket, "TOP $i 0\r\n" );
        $line = chop( fgets( $mbsocket, 500 ) );
        if( substr( $line, 0, 1 ) == "+" ) 
        {
            // Now we'll get back lines of text containing the headers.  We
            // read until we get a line containing nothing but a period.
            $headerlist = "";
            $lastheader="none";
            while( ($line = chop( fgets( $mbsocket, 1000 ) )) != "." ) 
            {
                $headerlist .= $line."\n";

                if (!empty($msgindex[$i][$lastheader]) && preg_match("/^\s/", $line)) 
                {
                    // This is a folded header line (like TO: often is)
                    // so it belongs to the lastheader we read
                    $msgindex[$i][$lastheader] .= " ".B64QPDecode(trim($line));
                }

                // FROM:
                else if( strcasecmp( substr( $line, 0, 5 ), "FROM:" ) == 0 ) 
                {
                    $f = B64QPDecode( substr( $line, 5 ) );
                    $msgindex[$i]['from'] = trim( $f );
                    $lastheader="from";
                }

                // REPLY-TO:
                else if( strcasecmp( substr( $line, 0, 9 ), "REPLY-TO:" ) == 0 ) 
                {
                    $r = B64QPDecode( substr( $line, 9 ) );
                    $msgindex[$i]['replyto'] = trim( $r );
                    $lastheader="replyto";
                }

                // TO:
                else if( strcasecmp( substr( $line, 0, 3 ), "TO:" ) == 0 ) 
                {
                    $t = B64QPDecode( substr( $line, 3 ) );
                    $msgindex[$i]['to'] = trim( $t );
                    $lastheader="to";
                }

                // CC:
                else if( strcasecmp( substr( $line, 0, 3 ), "CC:" ) == 0 ) 
                {
                    $c = B64QPDecode( substr( $line, 3 ) );
                    $msgindex[$i]['cc'] = trim( $c );
                    $lastheader="cc";
                }

                // BCC:
                else if( strcasecmp( substr( $line, 0, 4 ), "BCC:" ) == 0 ) 
                {
                    $b = B64QPDecode( substr( $line, 4 ) );
                    $msgindex[$i]['bcc'] = trim( $b );
                    $lastheader="bcc";
                }

                // SUBJECT:
                else if( strcasecmp( substr( $line, 0, 8 ), "SUBJECT:" ) == 0  ) 
                {
                    $s = B64QPDecode( substr( $line, 8 ) );
                    $msgindex[$i]['subject'] = trim( $s );
                }

                // DATE:
                else if( strcasecmp( substr( $line, 0, 5 ), "DATE:" ) == 0 ) 
                {
                    $d = substr( $line, 5 );
                    // Eliminate "GMT" if it's present.
                    $d = ereg_replace( "\"GMT\"", "", $d );

                    // If we find any number in the date like 100, 101, 102, etc.
                    // It's likely supposed to be the year but it was mangled by
                    // a non Y2K compliant email program, so we fix it.
                    if( ereg( " (1[0-9]{2}) ", $d, $r ) )
                        $d = ereg_replace( $r[1], strval( 1900 + intval( $r[1] ) ), $d );

                    // Parse the resulting string into a UNIX time format and
                    // store it in the index array.
                    $msgindex[$i]['date'] = strtotime( $d );
                }

                // STATUS:
                else if( strcasecmp( substr( $line, 0, 7 ), "STATUS:" ) == 0 )
                    $msgindex[$i]['status'] = trim( substr( $line, 7 ) );
            }

            // Store the full headers
            $msgindex[$i]['header'] = $headerlist;

            // Check for MIME attachments and set flag if applicable
            if( eregi( "boundary *= *[\"]*([^\r\n \"]+)", $headerlist ) )
              $msgindex[$i]['flags'] = "A";
            else
              $msgindex[$i]['flags'] = "";
        }
    }

    // ----------------------------------------------------------------------
    // Check MailBag error log for messages to be processed (errorcode = 99)
    // ----------------------------------------------------------------------
    $errcount = xarModAPIFunc('mailbag', 'admin', 'geterrors', 
                              array('i' => $i, 'msgindex' => &$msgindex, 'errorcode' => 99));

    if ($mailbagdebug) echo $errcount." messages loaded<br>";



    /**
        Loop through all messages
    */
    foreach($msgindex as $key => $msg)
    {
        $mailerror = '';
        // ----------------------------------------------------------------------
        // Message processing: Sender (FROM)
        // ----------------------------------------------------------------------

        // Get sender from from or reply-to fields
        $emailfrom = '';
        if (!empty($msg['replyto'])) 
        {
            $match = eregi("([[:alnum:]\._=-]+)@([[:alnum:]\._-]+)\.([[:alpha:]]+)", $msg['replyto'], $from);
            $emailfrom = $from[0];
        } 
        elseif(!empty($msg['from'])) 
        {
            $match = eregi("([[:alnum:]\._=-]+)@([[:alnum:]\._-]+)\.([[:alpha:]]+)", $msg['from'], $from);
            $emailfrom = $from[0];
        } 
        else 
        {
            $mailerror = 'There is no Sender.';
        }
        
        if ($mailbagdebug) echo $mailerror;
        
        // ----------------------------------------------------------------------
        // Check sender blacklist
        // ----------------------------------------------------------------------
        if(!empty($sblacklist[$emailfrom]))
        {
            $mailerror = "Mailbag Blacklisted" . ": " . $emailfrom;
            mailbagreply($emailfrom, $mailerror);
        }
        
        if(!empty($emailfrom))
            $user = xarModAPIFunc('roles', 'user', 'get', array('email' => $emailfrom));
            
        if(!empty($user) && is_array($user))
        {
            $from_userid = $user['uid'];
            $from_uname = $user['uname'];
        }
        else
        {
            $user = xarModAPIFunc('roles', 'user', 'get', array('uname' => 'Anonymous'));
            $from_userid = $user['uid'];
            $from_uname = $user['uname'];
            //echo var_dump($user);
        }
        
        // ----------------------------------------------------------------------
        // Message processing: Recipients (TO, CC, BCC)
        // ----------------------------------------------------------------------

        $emailto = array();
        $match = array();

        if(!empty($msg['cc']))
        {
            $to = explode(", ", $msg['to']);
            $j = 0;
            while( $j <= count($to) && count($emailto) < xarModGetVar('mailbag', 'maxrecip'))
            {
                if (!empty($to[$j]) && eregi("([[:alnum:]\._=-]+)@(".xarModGetVar('mailbag', 'emaildomain').")", $to[$j], $match)) 
                {
                    $emailto[] = $match[1];
                    $match = array();
                }
                
                $j++;
            }
        }
        
        if(!empty($msg['cc']))
        {
            $cc = explode(", ", $msg['cc']);
            $j = 0;
            while($j<=count($cc) && count($emailto)<=xarModGetVar('mailbag', 'maxrecip'))
            {
//            for($j=0; ($j<=count($cc) && count($emailto)<=xarModGetVar('mailbag', 'maxrecip')); $j++) {
                if (eregi("([[:alnum:]\._=-]+)@(".xarModGetVar('mailbag', 'emaildomain').")", $cc[$j], $match)) 
                {
                    $emailto[] = $match[1];
                    $match = array();
                }
            }
        }
        
        if(!empty($msg['bcc']))
        {        
            $bcc = explode(", ", $msg['bcc']);
            $j = 0;
            while($j<=count($cc) && count($emailto)<=xarModGetVar('mailbag', 'maxrecip'))
            {
                if(eregi("([[:alnum:]\._=-]+)@(".xarModGetVar('mailbag', 'emaildomain').")", $bcc[$j], $match)) 
                {
                    $emailto[] = $match[1];
                    $match = array();
                }
                $j++;
            }
        }
        if ($mailbagdebug) echo implode(", ", $emailto);

        // ----------------------------------------------------------------------
        // Message processing: Subject and size
        // ----------------------------------------------------------------------

        $subject = xarVarPrepForDisplay($msg['subject']);
        if ($msg['size'] > xarModGetVar('mailbag', 'maxsize'))
        {
            $mailerror=_MAILBAGMAILISLARGERTHAN." ".xarModGetVar('mailbag', 'maxsize')." bytes";
            mailbagreply($emailfrom, $mailerror);
        }
        
        /**
            Here we are getting every one this message should be send to
        */
        
        
        /**
            Lets decode the message
        */
        // ----------------------------------------------------------------------
        // Message processing: Get and decode body of passed messages
        // ----------------------------------------------------------------------

        if (!$mailerror && $key <= $msgcount) 
        {
            //$msg['text'] = '';
            fputs( $mbsocket, "RETR $key\r\n" );
            $line = fgets( $mbsocket, 500 );

            echo "<br>" . $key . " : ";
            echo $line . "<hr>";

            $boundary = array();
            if( substr( $line, 0, 1 ) == "+" ) 
            {
                // Read the header
                $headerlist = "";
                while( ($line = chop( fgets( $mbsocket, 1000 ) )) != "." && $line != "" ) 
                {
                    $headerlist .= $line . "\n";
                }

                // Get message structure
                $n = 0;
                if( eregi( "boundary *= *[\"]*([^\r\n\"]+)", $headerlist, $reg ) ) 
                {
                    $boundary[0] = "--" . $reg[1];
                    FindBoundary( $boundary[0], $mbsocket );
                    $headerlist = "";
                } 
                else
                {
                    $boundary[0] = "=_";
                }
                $b2 = $boundary[0] . "--";

                while( sizeof( $boundary ) > 0 && $line != "." && $line != $b2 ) 
                {
                    // Read the MIME headers for this part
                    if( $headerlist == "" ) 
                    {
                        $parthdr = "";
                        while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != "" )
                        {
                            $parthdr .= $line . "\n";
                        }
                    } 
                    else 
                    {
                        $parthdr = $headerlist;
                        $headerlist = "";
                    }

                    // Extract the content type for this part

                    if( eregi( "content-type: *([^; \n]+)", $parthdr, $reg ) )
                        $parttype = $reg[1];
                    else
                        $parttype = "text/plain";

                    if( stristr( $parttype, "multipart" ) && eregi( "boundary *= *[\"]*([^\r\n \"]+)", $parthdr, $reg ) ) 
                    {
                        array_unshift( $boundary, "--" . $reg[1] );
                        $b2 = $boundary[0] . "--";
                        FindBoundary( $boundary[0], $mbsocket );
                        continue;
                    } 
                    else
                    {
                        $n++;
                    }

                    // Extract the filename for this part

                    if( eregi( "name *= *[\"]([^\"]+)[\"]", $parthdr, $reg ) )  //"
                        $partname = $reg[1];
                    else
                        $partname = sprintf( "PART_%03d", $n );

                    // Extract the description for this part

                    if( eregi( "content-description: *([[:print:]]+)", $parthdr, $reg ) )
                        $partdesc = $reg[1];
                    else if( eregi( "subject: *([[:print:]]+)", $parthdr, $reg ) )
                        $partdesc = $reg[1];
                    else
                        $partdesc = "";

                    // Check for quoted printable content

                    if( eregi( "content-transfer-encoding: *quoted-printable", $parthdr ) )
                        $qp = true;
                    else
                        $qp = false;

                    // If the part is plain text, insert into $msgindex[$i][text].
                    if( strcasecmp( $parttype, "text/plain" ) == 0 ) 
                    {
                        if( stristr( $parthdr, "base64" ) )
                            $line = ExtractBASE64( $boundary[0], $key, $mbsocket, $msg['text'] );
                        else if( $qp )
                            $line = ExtractQP( $boundary[0], true, $key, $mbsocket, $msg['text'] );
                        else
                            $line = ExtractTEXT( $boundary[0], true, $key, $mbsocket, $msg['text'] );
                    }

                    // If the part is HTML, and HTML is allowed, insert into $msgindex[$i][text]
                    else if(strcasecmp( $parttype, "text/html" ) == 0 && xarModGetVar('mailbag', 'allowhtml') == '1') 
                    {
                        //if ($msg['text']) $msg['text'] = "";
                    
                        if( $qp ) {
                            $line = ExtractQP( $boundary[0], false, $key, $mbsocket, $msg['text'] );
                        }
                        else 
                        {
                            $line = ExtractTEXT( $boundary[0], false, $key, $mbsocket, $msg['text']);
                        }
                    }

                    // Otherwise we simply skip the data. Attachments are not supported by MailBag
                    else
                        $line = FindBoundary( $boundary[0], $mbsocket );

                    echo sizeOf($boundary) . $line. "<hr>";
                    
                    /*
                    while( $line == $b2 ) 
                    {
                        //echo var_dump($boundary) . " $key "  ."<hr>";
                        array_shift( $boundary );
                        if(!empty($boundary[0]))
                        {
                            $b2 = $boundary[0] . "--";
                            FindBoundary( $boundary[0], $mbsocket );
                        }
                        else
                        {
                            $b2 = '--';
                        }
                    }*/
                }
            }
            else
            {
                $mailerror = "There is a problem with the retrieval";
            }

            // Remove unwanted HTML tags from message body
            if(!empty($msg['text']))
            {
            $msg['text'] = eregi_replace('<html>', '', $msg['text']);
            $msg['text'] = eregi_replace('</html>', '', $msg['text']);
            $msg['text'] = eregi_replace('<head>.*</head>', '', $msg['text']);
            $msg['text'] = eregi_replace("<body\.*>", "", $msg['text']);
            $msg['text'] = eregi_replace('</body>', '', $msg['text']);
            }
            
            // Check if message body has been found
            if(empty($msg['text'])) 
            {
                $mailerror = 'Error in Mailbag'; //_MAILBAGNOHTMLORTEXT;
                $mailbagerrorcode = 2;
            }

        }
        
        echo "<hr>" . var_dump($msg) . "<hr>";
        
        /**
            If there was no error, then it is time to route the message
        */
        if(!$mailerror)
        {
            //echo var_dump($msg) . "<hr>";
        }
        /**
            Otherwise lets figure out the error and log it
            And/Or send an error message to mailer
        */
        else
        {
            //echo $mailerror;
        }
    }// End of Loop



    // ----------------------------------------------------------------------
    // Close connection to POP3 server and finish up MailBag
    // ----------------------------------------------------------------------
    if ($mailbagdebug) echo "Close POP3 connection<br>";

    fputs( $mbsocket, "QUIT\r\n" );
    fgets( $mbsocket, 500 );
    fclose( $mbsocket );

    // Log this run and the number of messages processed
    xarModSetVar('mailbag', 'lastrunlog', date("Y-m-d H:i").": ".$msgcount."+".$errcount);

    if ($mailbagdebug) echo "<b>MailBag is done</b><br>";

    

    return true;
}
?>