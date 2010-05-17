<?php
/**
 * Function to compile BlockLayout string
 * @package modules
 * @copyright (C) copyright-placeholder 
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://www.xaraya.com 
 * @subpackage Xarpages Module 
 * @link link-placeholder 
 * @author Param Software Services <paramsoft@eth.net>
 * @param $args['body'] String to be compile
 * @param $args['data'] data required to compile body.Its basically the variables used inside the body string to be compile. 
 * @returns compiled string. 
 */
function xarpages_userapi_compilebody($args)
{
    extract($args);
     
    sys::import('xaraya.templating.compiler');
    $blCompiler = XarayaCompiler::instance();
    
    $data = isset($args['data']) ? $args['data'] : array();
    try {
        $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
        $tplString .= $body;
        $tplString .= '</xar:template>';
        
        $body       = $blCompiler->compilestring($tplString);
        $body       = xarTplString($body, $data);
        
    } catch (Exception $e) {
        echo $e;
        return false;
    }
    return $body;
}
?>