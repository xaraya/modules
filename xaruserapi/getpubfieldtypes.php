<?php

/**
 * get array of (fixed) field types for publication types
// TODO: add dynamic fields here for .81+
 * @returns array
 * @return array('title'   => 'string',
                 'summary' => 'text',
                 ...);
 */
function articles_userapi_getpubfieldtypes($args)
{
    return array(
        'title'    => 'string',
        'summary'  => 'text',
        'notes'    => 'text',
        'body'     => 'text',
        'authorid' => 'integer',
        'pubdate'  => 'integer',
        'status'   => 'integer',
    );
}

?>
