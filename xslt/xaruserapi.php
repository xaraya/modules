<?php
/**
 * File: $Id$
 * 
 * XSLT Transform Hook
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage XSLT Transform Hook
 * @author mikespub
 */

/**
 * transform text
 * 
 * @param  $args ['objectid'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function xslt_userapi_transform($args)
{ 
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($objectid)) ||
            (!isset($extrainfo))) {
        $msg = xarML('Invalid Parameters for XSLT Transform Hook');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $modname = xarModGetName();
    $itemtype = 0;

    if (is_array($extrainfo)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
        if (!empty($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
        xarLogVariable('itemtype',$itemtype);
        if (empty($itemtype)) {
            $xsl = xarModGetVar('xslt',$modname);
        } else {
            $xsl = xarModGetVar('xslt',$modname.'.'.$itemtype);
        }
        if (empty($xsl)) {
            $xsl = xarModGetVar('xslt','default');
            if (empty($xsl)) {
                return $extrainfo;
            }
        }

        $xh = xslt_create();
        // Set the base to the location of the xsl file
        xslt_set_base($xh,'file://' . realpath($xsl));
        xslt_set_error_handler($xh, "xslt_trap_error");

        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $fieldkey) {
                $key= $fieldkey;
                if(array_key_exists($fieldkey."_output",$extrainfo)) {
                    // dd property
                    $key = $fieldkey ."_output";
                }
                       
                if (!empty($extrainfo[$key]) && preg_match('/^\s*</',$extrainfo[$key])) {
                    $args = array('/_xml' => $extrainfo[$key]);
                    // Pass the absolute minimum on parameters to the stylesheet, so the document
                    // can be transformed properly
                    // What will be the uri under which this item will be reachable (so anchors in xsl can be properly transformed)
                    $xar_docuri = xarServerGetCurrentURL();
                    // Pass in the web path to the xsl file, so relative links can be resolved from there if needed
                    $xar_webpath = dirname($xsl);
                    $params = array('xar_docuri'  => $xar_docuri,
                                    'xar_webpath' => $xar_webpath);
                    $transformed = xslt_process($xh,'arg:/_xml','file://'.realpath($xsl),NULL,$args,$params);
                    if ($transformed) {
                        xarLogMessage("XSLT: transformation successfull");
                        $extrainfo[$key] = $transformed;
                    } else {
                        // Error trapper will catch errors and set exception
                        xslt_free($xh);
                        return;
                    }
                }
            }
            xslt_free($xh);
            return $extrainfo;
        } else {
            $newinfo = array();
            foreach ($extrainfo as $text) {
                if (empty($text) || !preg_match('/^\s*</s',$text)) {
                    $newinfo[] = $text;
                } else {
                    $args = array('/_xml' => $text);
                    $transformed = @xslt_process($xh,'arg:/_xml','file://'.realpath($xsl),NULL,$args);
                    if ($transformed) {
                        $newinfo[] = $transformed;
                    } else {
                        // Error trapper will catch errors and set exception
                        xslt_free($xh);
                        return;
                    }
                }
            }
            xslt_free($xh);
            return $newinfo;
        }
    } elseif (empty($extrainfo) || !preg_match('/^\s*</s',$extrainfo)) {
        return $extrainfo;
    } else {
        $xsl = xarModGetVar('xslt',$modname);
        if (empty($xsl)) {
            $xsl = xarModGetVar('xslt','default');
            if (empty($xsl)) {
                return $extrainfo;
            }
        }

        $xh = xslt_create();
        xslt_set_base($xh,'file://' . realpath($xsl));
        xslt_set_error_handler($xh, "xslt_trap_error");

        $args = array('/_xml' => $extrainfo);
        $transformed = @xslt_process($xh,'arg:/_xml','file://'.realpath($xsl),NULL,$args);
        if ($transformed) {
            $extrainfo = $transformed;
        } else {
            // Error trapper will catch errors and set exception
            xslt_free($xh);
            return;
        }
        xslt_free($xh);
        return $extrainfo;
    }
}

/**
 * XSLT error trapping (cfr. http://www.php.net/manual/en/function.xslt-set-error-handler.php)
 */
function xslt_trap_error($parser, $errorno, $level, $fields) 
{
    $M = "Error Number $errorno, Level $level, Fields;\n";
    if(is_array($fields)) {
        while(list($key, $value) = each($fields)) {
            $M .= " $key => $value\n";
        }
    } else {
        $M .= "$fields";
    }
    xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                    new SystemException($M));
}

?>
