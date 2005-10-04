<?php
/**
 * Primarily used by Articles as a transform hook to turn "upload tags" into various display formats
 *
 * @param  $args ['extrainfo']
 * @returns
 * @return
 */
function & filemanager_userapi_transformhook ( $args )
{
    extract($args);

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] =& filemanager_userapi_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $key => $text) {
            $result[] =& filemanager_userapi_transform($text);
        }
    } else {
        $result =& filemanager_userapi_transform($extrainfo);
    }
    return $result;
}

function & filemanager_userapi_transform ( $body )
{
    while(eregi('#(ulid|file|ulidd|ulfn|fileURL|fileIcon|fileName|fileLinkedIcon):([^#]+)#', $body, $matches)) {
        $replacement=NULL;
        array_shift($matches);
        list($type, $id) = $matches;
        switch ( $type )  {
            case 'ulid':
                // DEPRECATED
            case 'file':
                //$replacement = "index.php?module=filemanager&func=download&fileId=$id";
                $list = xarModAPIFunc('filemanager', 'user', 'db_get_file', array('fileId' => $id));
                $replacement = xarTplModule('filemanager', 'user', 'attachment-list',
                                             array('Attachments' => $list,
                                                   'style' => 'transform'));
                break;
            case 'ulidd':
                // DEPRECATED
                //$replacement = "index.php?module=filemanager&func=download&fileId=$id";
                $replacement = xarModAPIFunc('filemanager','user','showoutput',
                                             array('value' => $id));
                break;
            case 'ulfn': // ULFN is DEPRECATED
            case 'fileLinkedIcon':
                $list = xarModAPIFunc('filemanager', 'user', 'db_get_file', array('fileId' => $id));
                $replacement = xarTplModule('filemanager', 'user', 'attachment-list',
                                             array('Attachments' => $list));
                break;
            case 'fileIcon':
                $file = xarModAPIFunc('filemanager', 'user', 'db_get_file', array('fileId' => $id));
                $file = end($file);
                $replacement = $file['mimeImage'];
                break;
            case 'fileURL':
                $file = xarModAPIFunc('filemanager', 'user', 'db_get_file', array('fileId' => $id));
                $file = end($file);
                $replacement = $file['fileDownload'];
                break;
            case 'fileName':
                $file = xarModAPIFunc('filemanager', 'user', 'db_get_file', array('fileId' => $id));
                $file = end($file);
                $replacement = $file['fileName'];
                break;
        }

        $body = ereg_replace("#$type:$id#", $replacement, $body);
    }

    return $body;
}
?>
