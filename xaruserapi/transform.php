<?php
/**
 * transform text
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function markdown_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count'); 
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = markdown_userapitransform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = markdown_userapitransform($text);
        }
    } else {
        $transformed = markdown_userapitransform($extrainfo);
    }
    return $transformed;
}

function markdown_userapitransform($text)
{
    include_once 'modules/markdown/xarclass/markdown.php';
    $text = Markdown($text);
    return $text;
}
?>