 <?php

 function uploads_userapi_flush_page_buffer(/*VOID*/)
 {
    if (ini_get('output_handler') == 'ob_gzhandler' || ini_get('zlib.output_compression') == TRUE) {
        do {
            $contents = ob_get_contents();
            if (!strlen($contents)) {
                // Assume we have nothing to store
                $pageBuffer[] = '';
                break;
            } else {
                $pageBuffer[] = $contents;
            }
        } while (@ob_end_clean());
    } else {
        do {
            $pageBuffer[] = ob_get_contents();
        } while (@ob_end_clean());
    }

    $buffer = array_reverse($pageBuffer);

    return $buffer;
}
?>