<?php
/**
 * Primarily used by Articles as a transform hook to turn "upload tags" into various display formats
 * 
 * @param  $args ['extrainfo'] 
 * @returns 
 * @return 
 */
function & uploads_userapi_transformhook ( $args )
{
    extract($args);

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] =& uploads_userapi_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] =& uploads_userapi_transform($text);
        }
    } else {
        $result =& uploads_userapi_transform($extrainfo);
    }
    return $result;
}

function & uploads_userapi_transform ( $body )
{
    
    while(eregi('#(ulid|ulidd|ulfn|fileURL|fileIcon|fileName):([^#]*)#', $body, $matches)) {
        array_shift($matches);
        list($type, $id) = $matches;
        
        switch ( $type )  {
            case 'ulid':
                // DEPRECATED
                $replacement = "index.php?module=uploads&func=download&fileId=$id";
                break;
            case 'ulidd':
                // DEPRECATED
                $replacement = "index.php?module=uploads&func=download&fileId=$id";
                break;
            case 'ulfn': // ULFN is DEPRECATED
            case 'fileLinkedIcon':
                $list = xarModAPIFunc('uploads', 'user', 'get', array('fileId' => $id));
                $replacement = xarTplModule('uploads', 'user', 'attachment-list', 
                                             array('Attachments' => $list));
                break;
            case 'fileIcon':
                $file = xarModAPIFunc('uploads', 'user', 'get', array('fileId' => $id));
                $file = end($file);
                $replacement = $file['mimeImage'];
                break;
            case 'fileURL':
                $file = xarModAPIFunc('uploads', 'user', 'get', array('fileId' => $id));
                $file = end($file);
                $replacement = $file['fileDownload'];
                break;
            case 'fileName':
                $file = xarModAPIFunc('uploads', 'user', 'get', array('fileId' => $id));
                $file = end($file);
                $replacement = $file['fileName'];
                break;
        }
        
        $body = ereg_replace("#$type:$id#", $replacement, $body);
    }

    return $body;
}
?>