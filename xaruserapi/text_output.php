<?php
/**
* Produce text output from the xmldata supplied
 *
 */
function reports_userapi_text_output($args)
{
    extract($args);
    
    // Command line would be fop -q input.xml output.pdf
    // so we have to make temporary files to hold the stuff
    
    // Write the xml data into the inputfile
    $input = tempnam('var/cache','REPORT');
    $hIn = fopen($input, 'w');
    fwrite($hIn, $xmldata);
    fclose($hIn);
    
    $output = tempnam('var/cache','REPORT');
    $lastline = exec("fop -q -fo $input -txt $output", $outlines, $returnvalue);
    
    // Return the contents of the output to the user
    $fp = fopen($output, 'rb');
    header("Content-Type: plain/text");
    header('Content-disposition: inline; filename='.$report['name'].'.txt');
    header("Content-Length: " . filesize($output));
    fpassthru($fp);
    fclose($fp);
    unlink($input); unlink($output);
    exit;
}
?>