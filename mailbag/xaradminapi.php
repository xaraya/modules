<?php

// Run MailBag to get and process the mail

function mailbag_admin_run()
{
   global $mbsocket, $msgindex;

   // Debug option, set to 1 if MailBag hangs and you want to find out what's going on
   $mailbagdebug = 0;
   
   if ($mailbagdebug) echo "<b>MailBag started</b><br>";

   // Check for Admin authorisation or execute-password (for cron job access)
   // If script is executed manually by an Admin turn on visuals, for cron-job keep it turned off
   // Check if background processing is turned on
   if ($mailbagdebug) echo "Authorisation check<br>";
   if (pnSecAuthAction(0, 'MailBag::', '::', ACCESS_ADMIN)) {
       $mailvis="on";
   } else {
     if (pnVarCleanFromInput('exepw') <> pnModGetVar('MailBag', 'exepassword')) {
       $output = new pnHTML();
       $output->Text(_MAILBAGNOAUTH);
       return $output->GetOutput();
     }
     if (pnModGetVar('MailBag', 'bgswitch')==0){
       $output = new pnHTML();
       $output->Text(_MAILBAGBGOFF);
       return $output->GetOutput();
     }
   }
   // Get information about the MailBag tables
   if ($mailbagdebug) echo "Read DB table information<br>";
   list($dbconn) = pnDBGetConn();
   $pntable = pnDBGetTables();

   // ----------------------------------------------------------------------
   // Get blacklist data from tables
   // ----------------------------------------------------------------------
   if ($mailbagdebug) echo "Load black list information<br>";

   $sblacklist = array();
   $rblacklist = array();
   $ublacklist = array();

   $sblacklisttable = $pntable['mb_sblacklist'];
   $sblacklistcolumn = &$pntable['mb_sblacklist_column'];
   $rblacklisttable = $pntable['mb_rblacklist'];
   $rblacklistcolumn = &$pntable['mb_rblacklist_column'];
   $ublacklisttable = $pntable['mb_ublacklist'];
   $ublacklistcolumn = &$pntable['mb_ublacklist_column'];

   // Get SENDER blacklists settings
   $sql = "SELECT $sblacklistcolumn[sb_id],
                  $sblacklistcolumn[from]
           FROM $sblacklisttable
           ORDER BY $sblacklistcolumn[from]";
   $result = $dbconn->Execute($sql);

   // Check for an error with the database code, and if so set an appropriate
   // error message and return
   if ($dbconn->ErrorNo() != 0) {
       pnSessionSetVar('errormsg', _GETFAILED);
       return false;
   }

   // Put sender blacklist items into result array
   for (; !$result->EOF; $result->MoveNext()) {
       list($sb_id, $from) = $result->fields;
       $sblacklist[] = $from;
   }
   $result->Close();

   // Get RECIPIENT blacklists settings
   $sql = "SELECT $rblacklistcolumn[rb_id],
                  $rblacklistcolumn[to]
           FROM $rblacklisttable
           ORDER BY $rblacklistcolumn[to]";
   $result = $dbconn->Execute($sql);

   // Check for an error with the database code, and if so set an appropriate
   // error message and return
   if ($dbconn->ErrorNo() != 0) {
       pnSessionSetVar('errormsg', _GETFAILED);
       return false;
   }

   // Put recipient blacklist items into result array
   for (; !$result->EOF; $result->MoveNext()) {
       list($rb_id, $to) = $result->fields;
       $rblacklist[] = $to;
   }
   $result->Close();

   // Get USER blacklists settings
   $sql = "SELECT $ublacklistcolumn[ub_id],
                  $ublacklistcolumn[user_id]
           FROM $ublacklisttable
           ORDER BY $ublacklistcolumn[user_id]";
   $result = $dbconn->Execute($sql);

   // Check for an error with the database code, and if so set an appropriate
   // error message and return
   if ($dbconn->ErrorNo() != 0) {
       pnSessionSetVar('errormsg', _GETFAILED);
       return false;
   }

   // Put user blacklist items into result array
   for (; !$result->EOF; $result->MoveNext()) {
       list($ub_id, $user_id) = $result->fields;
       $ublacklist[] = $user_id;
   }
   $result->Close();


   // ----------------------------------------------------------------------
   // Get mail list data from tables
   // ----------------------------------------------------------------------
   if ($mailbagdebug) echo "Load mail list information<br>";

   $maillist= array();
   $maillisttable = $pntable['mb_maillists'];
   $maillistcolumn = &$pntable['mb_maillists_column'];

   // Get current maillists settings
   $result = $dbconn->Execute("SELECT * FROM $maillisttable");

   // Check for an error with the database code, and if so set an appropriate
   // error message and return
   if ($dbconn->ErrorNo() != 0) {
       pnSessionSetVar('errormsg', _GETFAILED);
       return false;
   }

   // Put mail list items into result array
   for (; !$result->EOF; $result->MoveNext()) {
     list($list_id, $from_email, $to_email, $in_subject, $description, $to_topic, $cat_id, $admin_user_id, $no_comments, $no_homepage) = $result->fields;
     $maillist[] = array('list_id'    => $list_id,
                         'from_email'    => $from_email,
                         'to_email'      => $to_email,
                         'in_subject'    => $in_subject,
                         'description'   => $description,
                         'to_topic'      => $to_topic,
                         'cat_id'        => $cat_id,
                         'admin_user_id' => $admin_user_id,
                         'no_comments'   => $no_comments,
                         'no_homepage'   => $no_homepage);
   }
   $result->Close();

   // ----------------------------------------------------------------------
   // Make the connection to the POP3 server
   // ----------------------------------------------------------------------
   if ($mailbagdebug) echo "Connect to POP3 server<br>";

   // Setup connection to mail server
   if( ($i = strpos(pnModGetVar('MailBag', 'popserver'), ":" )) > 0 ) {
     $h = substr(pnModGetVar('MailBag', 'popserver'), 0, $i );
     $p = intval(substr(pnModGetVar('MailBag', 'popserver'), $i + 1 ) );
   }
   else {
     $h = pnModGetVar('MailBag', 'popserver');
     $p = 110;
   }

   // Open connection
   $mbsocket = fsockopen( $h, $p, $errno, $errstr, 15 );
   if( ! $mbsocket ) {
     die ("POP3 server connection refused: ".$h.":".$p);
   }

   // Get POP banner. If it begins with a '+' things are ok, otherwise
   // there is an error.
   $line = fgets( $mbsocket, 500 );
   if( substr( $line, 0, 1 ) != "+" ) {
     $errstr = substr( $line, 5 );
     die ("POP3 server connection error: ".$errstr);
   }

   // Send the username and test for a '+' result.
   fputs( $mbsocket, "USER " . pnModGetVar('MailBag', 'popuser') . "\r\n" );
   $line = fgets( $mbsocket, 500 );
   if( substr( $line, 0, 1 ) != "+" ) {
     $errstr = substr( $line, 5 );
     die ("POP3 server username error: ".$errstr);
   }

   // Send the password and test for a '+' result.
   fputs( $mbsocket, "PASS " . pnModGetVar('MailBag', 'poppass') . "\r\n" );
   $line = fgets( $mbsocket, 500 );
   if( substr( $line, 0, 1 ) != "+" ) {
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
   if ($mailbagdebug) echo"Load messages size information<br>";

   fputs( $mbsocket, "LIST\r\n" );
   $line = chop( fgets( $mbsocket, 500 ) );
   if( substr( $line, 0, 1 ) == "+" ) {
     $i = 1;
     while( ($line = chop( fgets( $mbsocket, 500 ) )) != "." )
     $msgindex[$i++][size] = intval( substr( $line, strrpos( $line, " " ) ) );
   }

   // ----------------------------------------------------------------------
   // Get the required message headers
   // ----------------------------------------------------------------------

   for( $i = 1; $i <= $msgcount; $i++ ) {
   
     if ($mailbagdebug) echo "Load header info of message ".$i."<br>";

     // Request the top of the message (the headers) plus 0 lines of body
     fputs( $mbsocket, "TOP $i 0\r\n" );
     $line = chop( fgets( $mbsocket, 500 ) );
     if( substr( $line, 0, 1 ) == "+" ) {
        // Now we'll get back lines of text containing the headers.  We
        // read until we get a line containing nothing but a period.
        $headerlist = "";
        $lastheader="none";
        while( ($line = chop( fgets( $mbsocket, 1000 ) )) != "." ) {
          $headerlist .= $line."\n";

          if (preg_match("/^\s/", $line)) {
            // This is a folded header line (like TO: often is)
            // so it belongs to the lastheader we read
            $msgindex[$i][$lastheader] .= " ".B64QPDecode(trim($line));
          }

          // FROM:
          else if( strcasecmp( substr( $line, 0, 5 ), "FROM:" ) == 0 ) {
            $f = B64QPDecode( substr( $line, 5 ) );
            $msgindex[$i][from] = trim( $f );
            $lastheader="from";
          }

          // REPLY-TO:
          else if( strcasecmp( substr( $line, 0, 9 ), "REPLY-TO:" ) == 0 ) {
            $r = B64QPDecode( substr( $line, 9 ) );
            $msgindex[$i][replyto] = trim( $r );
            $lastheader="replyto";
          }

          // TO:
          else if( strcasecmp( substr( $line, 0, 3 ), "TO:" ) == 0 ) {
            $t = B64QPDecode( substr( $line, 3 ) );
            $msgindex[$i][to] = trim( $t );
            $lastheader="to";
          }

          // CC:
          else if( strcasecmp( substr( $line, 0, 3 ), "CC:" ) == 0 ) {
            $c = B64QPDecode( substr( $line, 3 ) );
            $msgindex[$i][cc] = trim( $c );
            $lastheader="cc";
          }

          // BCC:
          else if( strcasecmp( substr( $line, 0, 4 ), "BCC:" ) == 0 ) {
            $b = B64QPDecode( substr( $line, 4 ) );
            $msgindex[$i][bcc] = trim( $b );
            $lastheader="bcc";
          }

          // SUBJECT:
          else if( strcasecmp( substr( $line, 0, 8 ), "SUBJECT:" ) == 0  ) {
            $s = B64QPDecode( substr( $line, 8 ) );
            $msgindex[$i][subject] = trim( $s );
          }

          // DATE:
          else if( strcasecmp( substr( $line, 0, 5 ), "DATE:" ) == 0 ) {
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
            $msgindex[$i][date] = strtotime( $d );
          }

          // STATUS:
          else if( strcasecmp( substr( $line, 0, 7 ), "STATUS:" ) == 0 )
                $msgindex[$i][status] = trim( substr( $line, 7 ) );
        }

	// Store the full headers
        $msgindex[$i][header] = $headerlist;

	// Check for MIME attachments and set flag if applicable
        if( eregi( "boundary *= *[\"]*([^\r\n \"]+)", $headerlist ) )
          $msgindex[$i][flags] = "A";
        else
          $msgindex[$i][flags] = "";
     }
   }

   // ----------------------------------------------------------------------
   // Check MailBag error log for messages to be processed (errorcode = 99)
   // ----------------------------------------------------------------------
   if ($mailbagdebug) echo "Check MailBag error log for re-processing, ";

   $mb_errors_table = $pntable['mb_errors'];
   $mb_errors_column = &$pntable['mb_errors_column'];
   $errcount = 0;

   // Get error log items with errorcode = 99
   $sql = "SELECT $mb_errors_column[msg_id],
                  $mb_errors_column[subject],
                  $mb_errors_column[from],
                  $mb_errors_column[to],
                  $mb_errors_column[msg_time],
                  $mb_errors_column[msg_text],
                  $mb_errors_column[header],
                  $mb_errors_column[errorcode]
           FROM $mb_errors_table
           WHERE $mb_errors_column[errorcode] = 99";
   $result = $dbconn->Execute($sql);

   // Check for an error with the database code, and if so set an appropriate
   // error message and return
   if ($dbconn->ErrorNo() != 0) {
     return false;
   }

  // Put error log items (if any) into result array.
  for (; !$result->EOF; $result->MoveNext()) {
    list($msg_id, $subject, $from, $to, $msg_time, $msg_text, $header, $errorcode) = $result->fields;

    $msgindex[$i][err_id] = $msg_id;
    $msgindex[$i][from] = $from;
    $msgindex[$i][to] = $to."@".pnModGetVar('MailBag', 'emaildomain');
    $msgindex[$i][subject] = $subject;
    $msgindex[$i][date] = strtotime($msg_time);
    $msgindex[$i][text] = $msg_text;
    $msgindex[$i][header] = $header;
    $i++;
    $errcount++;
  }

  $result->Close();
  
  if ($mailbagdebug) echo $errcount." messages loaded<br>";

  // ----------------------------------------------------------------------
  // Start up visual processing if MailBag is run through admin section
  // ----------------------------------------------------------------------

  if ($mailvis) {
    if ($mailbagdebug) echo "Start visual processing<br>";

    // Create output object
    $output = new pnHTML();

    // Add menu to output
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(mailbag_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Title
    $output->Linebreak(1);
    $output->Title(_MAILBAGRUN);
    $output->Text(_MAILBAGNUMBERMSGS.": ".$msgcount).$output->Linebreak(1);
    if ($errcount>0) $output->Text(_MAILBAGNUMBERERRLOGMSGS.": ".$errcount);

  }

  // ----------------------------------------------------------------------
  // Loop through all messages
  // ----------------------------------------------------------------------

  for($i=1; $i<=($msgcount+$errcount); $i++) {
    if ($mailbagdebug) echo "Process message ".$i."<br>";
    $mailbagerrorcode = "";
    $maillistid = "";
    if ($mailvis) $output->Linebreak(2).$output->BoldText(_MAILBAGMESSAGE.": $i").$output->Text(($i>$msgcount ? " ("._MAILBAGFROMERRORLOG.")" : "")).$output->Linebreak(1);

    // ----------------------------------------------------------------------
    // Message processing: Sender (FROM)
    // ----------------------------------------------------------------------

    // Get sender from from or reply-to fields
    if ($msgindex[$i][replyto]) {
      $match = eregi("([[:alnum:]\._=-]+)@([[:alnum:]\._-]+)\.([[:alpha:]]+)", $msgindex[$i][replyto], $from);
    } else {
      $match = eregi("([[:alnum:]\._=-]+)@([[:alnum:]\._-]+)\.([[:alpha:]]+)", $msgindex[$i][from], $from);
    }
    $emailfrom = $from[0];

    if ($mailvis) $output->BoldText(_MAILBAGFROM.": ").$output->Text($emailfrom);

    // ----------------------------------------------------------------------
    // Check sender blacklist
    // ----------------------------------------------------------------------
    if (in_array($emailfrom, $sblacklist)) {
      if ($mailvis) $output->BoldText(" "._MAILBAGSBLACKLIST);
      $mailerror=_MAILBAGSBLACKLIST.": ".$emailfrom;
      mailbagreply($emailfrom, $mailerror);
    }

    // Check if sender is a user, if not make sender anonymous user (user nr. 1)
    $column = &$pntable['users_column'];
    $result = $dbconn->Execute("SELECT $column[uid], $column[uname]
                                FROM $pntable[users]
                                WHERE $column[email]='$emailfrom'");
    list($from_userid, $from_uname) = $result->fields;
    if (!$from_userid) {
      $from_userid = 1;
      $from_uname = _MAILBAGANONYMOUS;
    }
    if ($mailvis) $output->Text(" ("._MAILBAGUSER.": ".$from_uname.")").$output->Linebreak(1);

    // ----------------------------------------------------------------------
    // Message processing: Recipients (TO, CC, BCC)
    // ----------------------------------------------------------------------

    $emailto = array();
    $match = array();

    $to = explode(", ", $msgindex[$i][to]);
    for($j=0; ($j<=count($to) && count($emailto)<pnModGetVar('MailBag', 'maxrecip')); $j++) {
      if (eregi("([[:alnum:]\._=-]+)@(".pnModGetVar('MailBag', 'emaildomain').")", $to[$j], $match)) {
        $emailto[] = $match[1];
        $match = array();
      } else {
        // ----------------------------------------------------------------------
        // Check for mail list "to e-mail address" settings
        // ----------------------------------------------------------------------
        for ($a=0; $a<count($maillist); $a++) {
          if ($maillist[$a][to_email] != "") {
            if (eregi($maillist[$a][to_email], $to[$j]) != "") {
              $maillistid = $a+1;
              $column = &$pntable['topics_column'];
              $res = $dbconn->Execute("SELECT $column[topicname]
                                       FROM $pntable[topics]
                                       WHERE $column[tid]='".$maillist[$a][to_topic]."'");
              list($to_tname) = $res->fields;
              $res->close();
              $emailto = array(); // reset array. Mail List is only sent to one recipient
              $emailto[] = $to_tname;
              if ($mailvis) $output->BoldText(_MAILBAGMAILLIST.": ").$output->Text($maillist[$a][description]).$output->Text(" ("._MAILBAGTO.")").$output->Linebreak(1);
            }
          }
        }
      }
    };
    $cc = explode(", ", $msgindex[$i][cc]);
    for($j=0; ($j<=count($cc) && count($emailto)<=pnModGetVar('MailBag', 'maxrecip')); $j++) {
      if (eregi("([[:alnum:]\._=-]+)@(".pnModGetVar('MailBag', 'emaildomain').")", $cc[$j], $match)) {
        $emailto[] = $match[1];
        $match = array();
      }
    };
    $bcc = explode(", ", $msgindex[$i][bcc]);
    for($j=0; ($j<=count($bcc) && count($emailto)<=pnModGetVar('MailBag', 'maxrecip')); $j++) {
      if(eregi("([[:alnum:]\._=-]+)@(".pnModGetVar('MailBag', 'emaildomain').")", $bcc[$j], $match)) {
        $emailto[] = $match[1];
        $match = array();
      }
    };
    if ($mailvis) $output->BoldText(_MAILBAGTO.": ").$output->Text(implode(", ", $emailto)).$output->Linebreak(1);

    // ----------------------------------------------------------------------
    // Message processing: Subject and size
    // ----------------------------------------------------------------------

    $subject = pnVarPrepForDisplay($msgindex[$i][subject]);
    if ($mailvis) $output->BoldText(_MAILBAGSUBJECT.": ").$output->Text($subject).$output->Linebreak(1);
    if ($msgindex[$i][size] > pnModGetVar('MailBag', 'maxsize')) {
      $mailerror=_MAILBAGMAILISLARGERTHAN." ".pnModGetVar('MailBag', 'maxsize')." bytes";
      mailbagreply($emailfrom, $mailerror);
    }
    if ($mailvis) $output->BoldText(_MAILBAGSIZE.": ").$output->Text($msgindex[$i][size]).$output->Linebreak(1);

    // ----------------------------------------------------------------------
    // Message processing: Mail lists by sender e-mail and subject
    // ----------------------------------------------------------------------

    // Check sender e-mail address
    for ($a=0; $a<count($maillist); $a++) {
      if ($maillist[$a][from_email] != "") {
        if (eregi($maillist[$a][from_email], $emailfrom) != "") {
          $maillistid = $a+1;
          $column = &$pntable['topics_column'];
          $res = $dbconn->Execute("SELECT $column[topicname]
                                   FROM $pntable[topics]
                                   WHERE $column[tid]='".$maillist[$a][to_topic]."'");
          list($to_tname) = $res->fields;
          $res->close();
          $emailto = array(); // reset array
          $emailto[] = $to_tname;
          if ($mailvis) $output->BoldText(_MAILBAGMAILLIST.": ").$output->Text($maillist[$a][description]).$output->Text(" ("._MAILBAGFROM.")").$output->Linebreak(1);
        }
      }
    }
    // Check text in subject
    for ($a=0; $a<count($maillist); $a++) {
      if ($maillist[$a][in_subject] != "") {
        if (eregi($maillist[$a][in_subject], $subject) != "") {
          $maillistid = $a+1;
          $column = &$pntable['topics_column'];
          $res = $dbconn->Execute("SELECT $column[topicname]
                                   FROM $pntable[topics]
                                   WHERE $column[tid]='".$maillist[$a][to_topic]."'");
          list($to_tname) = $res->fields;
          $res->close();
          $emailto = array(); // reset array
          $emailto[] = $to_tname;
          if ($mailvis) $output->BoldText(_MAILBAGMAILLIST.": ").$output->Text($maillist[$a][description]).$output->Text(" ("._MAILBAGSUBJECT.")").$output->Linebreak(1);
        }
      }
    }

    // ----------------------------------------------------------------------
    // Message processing: Get and decode body of passed messages
    // ----------------------------------------------------------------------

    if (!$mailerror && $i<=$msgcount) {
      fputs( $mbsocket, "RETR $i\r\n" );
      $line = fgets( $mbsocket, 500 );
      if( substr( $line, 0, 1 ) == "+" ) {

        // Read the header
        $headerlist = "";
        while( ($line = chop( fgets( $mbsocket, 1000 ) )) != "." && $line != "" ) {
          $headerlist .= $line . "\n";
        }

        // Get message structure
        $n = 0;
        if( eregi( "boundary *= *[\"]*([^\r\n\"]+)", $headerlist, $reg ) ) {
          $boundary[0] = "--" . $reg[1];
          FindBoundary( $boundary[0] );
          $headerlist = "";
        } else
          $boundary[0] = "=_";
        $b2 = $boundary[0] . "--";

        while( sizeof( $boundary ) > 0 && $line != "." && $line != $b2 ) {
          // Read the MIME headers for this part
          if( $headerlist == "" ) {
            $parthdr = "";
            while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != "" )
              $parthdr .= $line . "\n";
          } else {
            $parthdr = $headerlist;
            $headerlist = "";
          }

          // Extract the content type for this part

          if( eregi( "content-type: *([^; \n]+)", $parthdr, $reg ) )
            $parttype = $reg[1];
          else
            $parttype = "text/plain";

          if( stristr( $parttype, "multipart" ) && eregi( "boundary *= *[\"]*([^\r\n \"]+)", $parthdr, $reg ) ) {
            array_unshift( $boundary, "--" . $reg[1] );
            $b2 = $boundary[0] . "--";
            FindBoundary( $boundary[0] );
            continue;
          } else
            $n++;

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

	  if( strcasecmp( $parttype, "text/plain" ) == 0 ) {
            if( stristr( $parthdr, "base64" ) )
              $line = ExtractBASE64( $boundary[0], $i );
            else if( $qp )
              $line = ExtractQP( $boundary[0], true, $i );
            else
              $line = ExtractTEXT( $boundary[0], true, $i );
          }

          // If the part is HTML, and HTML is allowed, insert into $msgindex[$i][text]

          else if(strcasecmp( $parttype, "text/html" ) == 0 && pnModGetVar('MailBag', 'allowhtml') == '1') {
            if ($msgindex[$i][text]) $msgindex[$i][text] = "";
	    if( $qp ) {
              $line = ExtractQP( $boundary[0], false, $i );
            }
            else {
              $line = ExtractTEXT( $boundary[0], false, $i );
            }
          }

          // Otherwise we simply skip the data. Attachments are not supported by MailBag

          else
            $line = FindBoundary( $boundary[0] );

          while( $line == $b2 ) {
            array_shift( $boundary );
            $b2 = $boundary[0] . "--";
            FindBoundary( $boundary[0] );
          }
        }
      }

      // Remove unwanted HTML tags from message body
      $msgindex[$i][text] = eregi_replace('<html>', '', $msgindex[$i][text]);
      $msgindex[$i][text] = eregi_replace('</html>', '', $msgindex[$i][text]);
      $msgindex[$i][text] = eregi_replace('<head>.*</head>', '', $msgindex[$i][text]);
      $msgindex[$i][text] = eregi_replace("<body\.*>", "", $msgindex[$i][text]);
      $msgindex[$i][text] = eregi_replace('</body>', '', $msgindex[$i][text]);

      // Check if message body has been found
      if(!$msgindex[$i][text]) {
        $mailerror=_MAILBAGNOHTMLORTEXT;
        $mailbagerrorcode = 2;
      }

    }

    // Continue if the above did not produce an error
    if (!$mailerror) {
      if ($mailbagdebug) echo "Route message ".$i."<br>";
      if (pnModGetVar('MailBag', 'senderemail') == '1' && $from_userid == '1' && $i <= $msgcount && $maillistid == "") $msgindex[$i][text] = _MAILBAGFROM.": ".$emailfrom."<br>\n<br>\n".$msgindex[$i][text];
      $time = date("Y-m-d H:i", $msgindex[$i][date]);
      if(!$subject) $subject = _MAILBAGNOSUBJECT;
      if(!$msgindex[$i][text]) $msgindex[$i][text] = _MAILBAGNOBODY;
      if ($mailvis) $output->BoldText(_MAILBAGTIME.": ").$output->Text($time).$output->Linebreak(1).$output->BoldText(_MAILBAGBODY.": ").$output->Text($msgindex[$i][text]).$output->Linebreak(1);

      // ----------------------------------------------------------------------
      // Message routing: Determine where the message should go in PostNuke
      // In the following order: FAQ, news topics, users
      // ----------------------------------------------------------------------

      // Loop through all recipients
      if (count($emailto)==0 && $mailvis) {
        $output->BoldText(_MAILBAGNORECIPIENT).$output->Linebreak(1);
        $mailerror = _MAILBAGNORECIPIENT;
        $mailbagerrorcode = 3;
      }
      for($j=0; $j<count($emailto); $j++) {
        $to = $emailto[$j];
        if ($mailvis) $output->BoldText(_MAILBAGROUTEDTO.": ").$output->Text($to).$output->Linebreak(1);

        // ----------------------------------------------------------------------
        // Check recipient blacklist
        // ----------------------------------------------------------------------
        if (in_array($to, $rblacklist)) {
          if ($mailvis) $output->BoldText(_MAILBAGRBLACKLIST).$output->Linebreak(1);
          $mailerror = _MAILBAGRBLACKLIST.": ".$to."@".pnModGetVar('MailBag', 'emaildomain');
          mailbagreply($emailfrom, $mailerror);
          continue;
        }

        // ----------------------------------------------------------------------
        // Message routing: FAQ
        // ----------------------------------------------------------------------

        if ($to == pnModGetVar('MailBag', 'faqaddr')) {
          if ($mailvis) $output->Text(" "._MAILBAGFAQSUBMISSION).$output->Linebreak(1);
 	  $column = &$pntable['faqanswer_column'];
          $nextid = $dbconn->GenId($pntable['faqanswer']);
          $res = $dbconn->Execute("INSERT INTO $pntable[faqanswer]
                 ($column[id], $column[id_cat], $column[question], $column[submittedby], $column[answer])
                 VALUES ($nextid, '0', '".trim(strip_tags(ereg_replace("([ \f\r\t\n\v])+", " ", $msgindex[$i][text])))."', '$emailfrom', '')");
          if($dbconn->ErrorNo()<>0) {
            $mailerror="PN database connection error: ".$dbconn->ErrorMsg().". SQL query:".$sql;
            $mailbagerrorcode = 1;
          }
          continue;
        }

        // ----------------------------------------------------------------------
        // Message routing: NEWS TOPIC
        // ----------------------------------------------------------------------

        $column = &$pntable['topics_column'];
        $res = $dbconn->Execute("SELECT $column[tid], $column[topicname]
                                 FROM $pntable[topics]
                                 WHERE $column[topicname]='$to'");
        list($to_tid, $to_tname) = $res->fields;

        if ($to_tid != "") {
          // ----------------------------------------------------------------------
          // If this is to a registered mail list, bypass the submission process
          // ----------------------------------------------------------------------
          if ($maillistid != "") {
            $a = $maillistid-1;
            $catid = $maillist[$a][cat_id];
            $aid = $maillist[$a][admin_user_id];
            $ihome = $maillist[$a][no_homepage];
            $withcomm = $maillist[$a][no_comments];
            $maillistid = "";

            $column = &$pntable['stories_column'];
            $nextid = $dbconn->GenId($pntable['stories']);
            $res = $dbconn->Execute("INSERT INTO $pntable[stories] ($column[sid],
                             $column[catid], $column[aid], $column[title],
                             $column[time], $column[hometext], $column[bodytext],
                             $column[comments], $column[counter], $column[topic],
                             $column[informant], $column[notes], $column[ihome],
                             $column[themeoverride], $column[alanguage],
                             $column[withcomm])
                             VALUES ($nextid, '$catid', '$aid', '".pnVarPrepForStore($subject)."', now(),
                             '".pnVarPrepForStore($msgindex[$i][text])."', '', '0', '0', '$to_tid',
                             '".pnVarPrepForStore($from_uname)."', '', '$ihome', '', '',
                             '$withcomm')");
          } else {

            // ----------------------------------------------------------------------
            // This is an e-mail to a topic, so put it in the news submission queue
            // ----------------------------------------------------------------------
            $column = &$pntable['queue_column'];
            $newid = $dbconn->GenId($pntable['queue']);
            $sql = "INSERT INTO $pntable[queue] ($column[qid], $column[uid], $column[arcd], $column[uname], $column[subject], $column[story], $column[timestamp], $column[topic], $column[alanguage], $column[bodytext])
                    VALUES ($newid, '$from_userid', '0', '".pnVarPrepForStore($from_uname)."', '".pnVarPrepForStore($subject)."', '".pnVarPrepForStore($msgindex[$i][text])."', now(), '$to_tid', '', '')";
            $res = $dbconn->Execute($sql);
          }

          if($dbconn->ErrorNo()<>0) {
            $mailerror="PN database connection error: ".$dbconn->ErrorMsg().". SQL query:".$sql;
            $mailbagerrorcode = 1;
          }
          if ($mailvis) $output->BoldText(_MAILBAGNEWSTOPIC.": ").$output->Text($to_tname).$output->Linebreak(1);
          continue;
        }

        // ----------------------------------------------------------------------
        // Message routing: PRIVATE MESSAGE TO USER
        // ----------------------------------------------------------------------

        $column = &$pntable['users_column'];
        $res = $dbconn->Execute("SELECT $column[uid], $column[email]
                                 FROM $pntable[users]
                                 WHERE $column[uname]='$to'");
        list($to_userid, $to_user_email) = $res->fields;

        // ----------------------------------------------------------------------
        // Check user id blacklist
        // ----------------------------------------------------------------------
        if ($to_userid && (in_array($to_userid, $ublacklist))) {
          if ($mailvis) $output->BoldText(_MAILBAGUBLACKLIST).$output->Linebreak(1);
          $mailerror = _MAILBAGUBLACKLIST.": ".$to_userid." (".$to."@".pnModGetVar('MailBag', 'emaildomain').")";
          mailbagreply($emailfrom, $mailerror);
          continue;
        }
        if ($to_userid <> "") {
          $column = &$pntable['priv_msgs_column'];
          // Load message in priv_msgs table
          $nextid = $dbconn->GenId($pntable['priv_msgs_column']);
          $sql = "INSERT INTO $pntable[priv_msgs] ($column[msg_id], $column[msg_image], $column[subject], $column[from_userid], $column[to_userid], $column[msg_time], $column[msg_text])
                  VALUES ($nextid, '', '".pnVarPrepForStore($subject)."', $from_userid, $to_userid, '$time', '".pnVarPrepForStore($msgindex[$i][text])."')";
          $res = $dbconn->Execute($sql);
          if($dbconn->ErrorNo()<>0) {
            $mailerror="PN database connection error: ".$dbconn->ErrorMsg().". SQL query:".$sql;
            $mailbagerrorcode = 1;
          }
          if ($mailvis) $output->BoldText(_MAILBAGUSER.": ").$output->Text($to).$output->Linebreak(1);

          // Send user notification if configuration option is set
          if (pnModGetVar('MailBag', 'notifyuser') == 1) {
            // Prevent sending mail to own domain, send warning to postmaster
            if (eregi(pnModGetVar('MailBag', 'emaildomain'), $to_user_email)) mail(pnModGetVar('MailBag', 'postmaster'), "Mail loop warning", "Prevented mail loop to ".$to_user_email."\n\nMail Notification\n", "From: ".pnModGetVar('MailBag', 'postmaster'));
            else mail($to_user_email, pnConfigGetVar('sitename')." "._MAILBAGNOTIFICATION,
              _MAILBAGNOTIFHEADER."\n\n".pnGetBaseURL()."modules.php?op=modload&name=Messages&file=index"."\n\n"._MAILBAGNOTIFFOOTER,
              "From: ".pnConfigGetVar('sitename')." <".pnModGetVar('MailBag', 'postmaster').">");
          }

          continue;
        }

        // ----------------------------------------------------------------------
        // Message routing: UNKNOWN RECIPIENT
        // ----------------------------------------------------------------------

        if ($mailvis) $output->BoldText(_MAILBAGUNKNOWNRECIPIENT).$output->Linebreak(1);
        if (pnModGetVar('MailBag', 'unknowntolog')) { // Load message in error log table
          $column = &$pntable['mb_errors_column'];
          $nextid = $dbconn->GenId($pntable['mb_errors_column']);
          $sql = "INSERT INTO $pntable[mb_errors] ($column[msg_id], $column[subject], $column[from], $column[from_user_id], $column[to], $column[to_user_id], $column[msg_time], $column[msg_text], $column[header], $column[errorcode], $column[error])
                  VALUES ($nextid, '".pnVarPrepForStore($subject)."', '".pnVarPrepForStore($emailfrom)."', '$from_userid', '".pnVarPrepForStore($to)."', '$to_userid', '$time', '".pnVarPrepForStore($msgindex[$i][text])."', '".pnVarPrepForStore($msgindex[$i][header])."', 10, '".pnVarPrepForStore(_MAILBAGUNKNOWNRECIPIENT)."')";
          $res = $dbconn->Execute($sql);
          if($dbconn->ErrorNo()<>0) {
            $mailerror="PN database connection error: ".$dbconn->ErrorMsg().". SQL query:".$sql;
            $mailbagerrorcode = 1;
          }
        } else {
          $mailerror = _MAILBAGUNKNOWNRECIPIENT.": ".$to;
          mailbagreply($emailfrom, $mailerror);
        }
      } // End recipient loop
    } // End error free message processing

    // ----------------------------------------------------------------------
    // Message routing: MAILBAG LOG
    // ----------------------------------------------------------------------
    if ($mailerror && $mailbagerrorcode) {
      // Load message in error table
      $column = &$pntable['mb_errors'];
      $nextid = $dbconn->GenId($pntable['mb_errors_column']);
      $sql = "INSERT INTO $pntable[mb_errors] ($column[msg_id], $column[subject], $column[from], $column[from_user_id], $column[to], $column[to_user_id], $column[msg_time], $column[msg_text], $column[header], $column[errorcode], $column[error])
              VALUES ($nextid, '".pnVarPrepForStore($subject)."', '".pnVarPrepForStore($emailfrom)."', '$from_userid', '".pnVarPrepForStore($to)."', '$to_userid', '$time', '".pnVarPrepForStore($msgindex[$i][text])."', '".pnVarPrepForStore($msgindex[$i][header])."', $mailbagerrorcode, '".pnVarPrepForStore($mailerror)."')";
      $res = $dbconn->Execute($sql);
      if($dbconn->ErrorNo()<>0) {
        $logmessage="PN database connection error: ".$dbconn->ErrorMsg().". SQL query:".$sql;
        $errorcode = 1;
      }
    }
    if ($mailerror && $mailvis) {
      $output->BoldText(_MAILBAGMAILERROR.": ".$mailerror).$output->Linebreak(1);
      $mailerror = "";
    }
    // ----------------------------------------------------------------------
    // Delete message
    // ----------------------------------------------------------------------

    if($i<=$msgcount) {
      if ($mailbagdebug) echo "Delete message ".$i."<br>";
      fputs( $mbsocket, "DELE $i\r\n" );
      $line = fgets( $mbsocket, 500 );
    } else {
      if ($mailbagdebug) echo "Delete (error log) message ".$i."<br>";
      $mb_errors_table = $pntable['mb_errors'];
      $mb_errors_column = &$pntable['mb_errors_column'];

      // Delete the item from the error log
      $sql = "DELETE FROM $mb_errors_table
              WHERE $mb_errors_column[msg_id] = " . $msgindex[$i][err_id];
      $dbconn->Execute($sql);

      // Check for an error with the database code, and if so set an
      // appropriate error message and return
      if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
      return false;
      }

    }

    $mailbagerrorcode = "";

  } // End of message loop

  // ----------------------------------------------------------------------
  // Close connection to POP3 server and finish up MailBag
  // ----------------------------------------------------------------------
  if ($mailbagdebug) echo "Close POP3 connection<br>";

  fputs( $mbsocket, "QUIT\r\n" );
  fgets( $mbsocket, 500 );
  fclose( $mbsocket );

  // Log this run and the number of messages processed
  pnModSetVar('MailBag', 'lastrunlog', date("Y-m-d H:i").": ".$msgcount."+".$errcount);

  if ($mailvis) {
    $output->Linebreak(2).$output->BoldText(_MAILBAGDONE).$output->Linebreak(3);

    // Return the output that has been generated by this function
    return $output->GetOutput();
  }

if ($mailbagdebug) echo "<b>MailBag is done</b><br>";

}



// Reply mail to message sender

function mailbagreply($replyto, $mailerror) {

  // Prevent sending mail to own domain, send warning to postmaster
  if (eregi(pnModGetVar('MailBag', 'emaildomain'), $replyto)) mail(pnModGetVar('MailBag', 'postmaster'), "Mail loop warning", "Prevented mail loop to ".$replyto."\n\nReply message:\n".$msgindex[$i][text], "From: ".pnModGetVar('MailBag', 'postmaster'));
    else mail($replyto, pnConfigGetVar('sitename')." "._MAILBAGMAILERROR,
    _MAILBAGMAILHEADER."\n\n".$mailerror."\n\n"._MAILBAGMAILFOOTER,
    "From: ".pnConfigGetVar('sitename')." <".pnModGetVar('MailBag', 'postmaster').">");
}


// ----------------------------------------------------------------------------
// B64QPDecode()
//
// This function is used to decode BASE64 and/or QUOTED PRINTABLE substrings
// in certain headers.
// Based on code from PHPost by Mark Morley (mark@webgadgets.com)
// http://webgadgets.com/phpost/
// ----------------------------------------------------------------------------

function B64QPDecode( $s )
{
  // Start by assuming that the final result will be unchanged

  $result = $s;
  // Loop until we can't find "=?" in the result any more (we loop
  // because there may be multiple encoded substrings, not just one)

  while( $begin = strpos( $result, "=?" ) ) {
    // Save a lowercase copy of the current result (PHP needs stripos!)
    $lr = strtolower( $result );

    // Extract the left part (everything prior to the encoded substring)
    $left = substr( $result, 0, $begin );

    // If it isn't QUOTED PRINTABLE (?q?) or BASE64 (?b?) then we bail
    // leaving things just the way they are.

    if( ($b = strpos( $lr, "?q?" )) == false && ($b = strpos( $lr, "?b?" )) == false )
      break;

    // Extract the middle part (the encoded substring)
    $rest = substr( $result, $b + 3 );
    $e = strpos( $rest, "?=" );
    $middle = substr( $rest, 0, $e );

    // Extract the right part (everything after the substring)
    $right = substr( $rest, $e + 2 );

    // Decode the substring and build a new result
    if( strpos( $lr, "?q?" ) )
      $result = $left . quoted_printable_decode( $middle ) . $right;
    else
      $result = $left . base64_decode( $middle ) . $right;

    // Replace underscores with spaces
    $result = str_replace( "_", " ", $result );
  }
  return $result;
}

// ----------------------------------------------------------------------------
// FindBoundary()
//
// This function finds a boundary in a message
// Based on code from PHPost by Mark Morley (mark@webgadgets.com)
// http://webgadgets.com/phpost/
// ----------------------------------------------------------------------------

function FindBoundary( $b ) {
  global $mbsocket;
  $b2 = $b . "--";
  while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 );
  return $line;
}


// ----------------------------------------------------------------------------
// ExtractTEXT()
//
// This function extracts text from a message
// Based on code from PHPost by Mark Morley (mark@webgadgets.com)
// http://webgadgets.com/phpost/
// ----------------------------------------------------------------------------

function ExtractTEXT( $b, $a, $i ) {
  global $mbsocket, $msgindex;
  $b2 = $b . "--";
  while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 ) {
    if( strncmp( $line, "..", 2 ) == 0 )
      $line = substr( $line, 1 );
    if ($a) $msgindex[$i][text] .= htmlentities($line)."<br>\n";
    else $msgindex[$i][text] .= $line."\n";
  }
  return $line;
}


// ----------------------------------------------------------------------------
// ExtractQP()
//
// This function extracts a quoted-printable MIME part.
// Based on code from PHPost by Mark Morley (mark@webgadgets.com)
// http://webgadgets.com/phpost/
// ----------------------------------------------------------------------------

function ExtractQP( $b, $a, $i ) {
  global $mbsocket, $msgindex;
  $b2 = $b . "--";
  while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 ) {
    if( strncmp( $line, "..", 2 ) == 0 )
      $line = substr( $line, 1 );
    if ($a) $msgindex[$i][text] .= htmlentities(quoted_printable_decode($line))."<br>\n";
    else $msgindex[$i][text] .= quoted_printable_decode($line)."\n";
  }
  return $line;
}

// ----------------------------------------------------------------------------
// ExtractBASE64()
//
// This function reads data from the socket, base64 decodes it, and returns it
// Based on code from PHPost by Mark Morley (mark@webgadgets.com)
// http://webgadgets.com/phpost/
// ----------------------------------------------------------------------------

function ExtractBASE64( $b, $i ) {
  global $mbsocket, $msgindex;
  $b2 = $b . "--";
  while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 ) {
    $msgindex[$i][text] .= htmlentities(base64_decode($line))."<br>\n";
  }
  return $line;
}
?>