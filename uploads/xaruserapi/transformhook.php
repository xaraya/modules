<?php
/**
 * Primarily used by Articles as a transform hook to turn "upload tags" into various display formats
 * 
 * @param  $args ['extrainfo'] 
 * @returns 
 * @return 
 */
function uploads_userapi_transformhook ( $args )
{
    extract($args);

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = uploads_userapi_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] = uploads_userapi_transform($text);
        }
    } else {
        $result = uploads_userapi_transform($extrainfo);
    }
    return $result;
}

function uploads_userapi_transform ( $body )
{
    // Loop over each Upload ID Tag, Auto detect display tag based on extension
    while(ereg('#file:([0-9]+)#', $body, $matches)) {
        $ulid = $matches[1];

        // Lookup Upload
        $info = xarModAPIFunc('uploads',
                              'user',
                              'get',
                              array('ulid'=>$ulid));
        // Retrieve user specified filename
        $ulfile = $info['ulfile'];

        // Check if file approved
        if( $info['ulhash'] != '' )
        {
            $tpl = xarTplModule('uploads','user','viewdownload'
                               ,array('ulid' => $ulid, 'ulfile' => $ulfile)
                               ,$info['ulext']);
        } else {
            $tpl = xarTplModule('uploads','user','viewdownload_na'
                               ,array('ulid' => $ulid, 'ulfile' => $ulfile)
                               ,$info['ulext']);
        }
        $body=ereg_replace("#ulid:$matches[1]#",$tpl,$body);
    }

    // Loop over each Upload Tag set to use Default Template
    while( ereg('#ulidd:([0-9]+)#',$body,$matches) )
    {
        $ulid = $matches[1];

        // Lookup Upload
        $info = xarModAPIFunc('uploads',
                              'user',
                              'get',
                              array('ulid'=>$ulid));
        // Retrieve user specified filename
        $ulfile = $info['ulfile'];

        // Check if file approved
        $tpl = xarTplModule('uploads','user','viewdownload'
                           ,array('ulid' => $ulid, 'ulfile' => $ulfile));
        $body=ereg_replace("#ulidd:$matches[1]#",$tpl,$body); 
    }

    // Loop over each Upload Tag set to use Default Template
    while( ereg('#ulfn:(.+)#',$body,$matches) )
    {
        $ulname = $matches[1];

        // Lookup Upload
        $info = xarModAPIFunc('uploads',
                              'user',
                              'get',
                              array('ulname'=>$ulname));
        // Retrieve user specified filename
        $ulfile = $info['ulfile'];

        // Check if file approved
        $tpl = xarTplModule('uploads','user','viewdownload'
                           ,array('ulid' => $ulid, 'ulfile' => $ulfile));
        $body=ereg_replace("#ulidd:$matches[1]#",$tpl,$body); 
    }

    return $body;
}
?>
