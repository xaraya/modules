<?php
/**
 * Produce html output based on xmldata supplied
 *
 * We expect a xsl-fo xml document in the param xmldata and produce
 * html from that with the use of an xsl stylesheet
 *
 */
function reports_userapi_html_output($args)
{
    extract($args);

    $xslproc = new xslt_processor();
    if($xslproc) {
        $xslfile = 'modules/reports/xarincludes/fo2html.xsl';
        $output = $xslproc->process($xmldata, $xslfile);
        if(!$output) {
            $xslproc->free();
            return;
        }
        $xslproc->free();
    }
    return $output;
}

class xslt_processor
{
    var $xslproc = null;
    var $xmldata = '';
    
    function xslt_processor()
    {
        $this->xslproc = xslt_create();
        if($this->xslproc) {
            xslt_set_object($this->xslproc, $this);
            xslt_set_encoding($this->xslproc,"UTF-8");
        }
    }

    function process($xmldata, $xslfile)
    {
        $this->xmldata = $xmldata;
        $arguments = array('/_xml' => $this->xmldata);
        $transformed = xslt_process($this->xslproc, 'arg:/_xml', $xslfile, NULL, $arguments);
        return $transformed;
    }
         
    function free() 
    {
        xslt_free($this->xslproc);
    }
    
    function xslt_trap_error($parser, $errorno, $level, $fields) 
    {
        $msg = "Error Number $errorno, Level $level, Fields;\n";
        if(is_array($fields)) {
            while(list($key, $value) = each($fields)) {
                $msg .= " $key => $value\n";
            }
        } else {
            $msg .= "$fields";
        }
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', $msg);
        return;
    }
}
