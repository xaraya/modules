<?php

// Run MailBag to get and process the mail

// Reply mail to message sender

function mailbagreply($replyto, $mailerror, $msgtext) 
{

    // Prevent sending mail to own domain, send warning to postmaster
    if (eregi(xarModGetVar('mailbag', 'emaildomain'), $replyto)) 
        mail(xarModGetVar('mailbag', 'postmaster'), "Mail loop warning", 
            "Prevented mail loop to ".$replyto."\n\nReply message:\n".$msgtext, 
            "From: ".xarModGetVar('mailbag', 'postmaster'));
    else 
        mail($replyto, pnConfigGetVar('sitename')." "." Mail Bag Error ",
            "  "."\n\n".$mailerror."\n\n",
            "From: ".xarConfigGetVar('sitename')." <".xarModGetVar('mailbag', 'postmaster').">");
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

function FindBoundary( $b, &$mbsocket )
{
    //global $mbsocket;
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

function ExtractTEXT( $b, $a, $i, &$mbsocket, &$msg ) 
{
    //global $mbsocket, $msgindex;
    $b2 = $b . "--";
    while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 ) {
        if( strncmp( $line, "..", 2 ) == 0 )
            $line = substr( $line, 1 );
        if ($a) $msg .= htmlentities($line)."<br>\n";
        else $msg .= $line."\n";
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

function ExtractQP( $b, $a, $i, &$mbsocket, &$msg ) 
{
  //global $mbsocket, $msgindex;
    $b2 = $b . "--";
    while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 ) {
        if( strncmp( $line, "..", 2 ) == 0 )
            $line = substr( $line, 1 );
        if ($a) $msg .= htmlentities(quoted_printable_decode($line))."<br>\n";
        else $msg .= quoted_printable_decode($line)."\n";
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

function ExtractBASE64( $b, $i, &$mbsocket, &$msg ) 
{
  //global $mbsocket, $msgindex;
    $b2 = $b . "--";
    while( ($line = chop( fgets( $mbsocket, 10000 ) )) != "." && $line != $b && $line != $b2 ) {
        $msg .= htmlentities(base64_decode($line))."<br>\n";
    }
    return $line;
}
?>