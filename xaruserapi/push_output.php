<?php

function reports_userapi_push_output($args)
{
    extract($args);
    
    // Write the xmldata to a temporary inputfile
    $input = realpath(tempnam('var/cache/reports','REP_IN'));
    $hIn = fopen($input, 'w');
    if($hIn) {
        fwrite($hIn, $xmldata);
        fclose($hIn);
    } else {
        // TODO: raise an exception
    }
    
    // Write the resulting document into a temporary outputfile
    $output = realpath(tempnam('var/cache/reports','REP_OUT'));
    $command ="fop -q -fo $input -$format $output";
    xarLogMessage("FOP: $command");
    $lastline = exec($command, $outlines, $returnvalue);
    $outlines = join("\n",$outlines);
    if( (!file_exists($output)) || (filesize($output) == 0)) {
        // Something went wrong, produce the output of the command as exception
        $outtext = xarML("The #(1) document was not correctly generated, perhaps the log below gives some insight on what went wrong:\n", strtoupper($format));
        $outtext .= $outlines;
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_FOUND', $outtext);
        return;
    }
    
    // Determine the content type
    switch($format) {
        case 'pdf':
            $contenttype = "application/pdf";
            break;
        case 'txt':
            $contenttype = "plain/text";
            break;
        case 'svg':
            $contenttype = "image/svg+xml";
            break;
        default:
            // force download?
    }
    
    // Push the contents of the output to the users client
    $hOut = fopen($output, "rb");
    if($hOut) {
        header("Pragma: ");
        header("Cache-Control: ");
        header("Content-Type: $contenttype");
        header("Content-disposition: $disposition; filename=\"$filename\"");
        header("Content-Length: " . filesize($output));
        fpassthru($hOut);
        fclose($hOut);
    } else {
        // TODO: raise exception?
    }
    // When done, remove the xmldata and the produced output file
    // TODO: investigate output cache here, huge potential
    unlink($input); unlink($output);
    return true;
}