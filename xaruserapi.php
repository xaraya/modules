<?php

/**
    
*/
function sitesearch_userapi_processtitle($title)
{
    $seperator = xarModGetVar('themes', 'SiteTitleSeparator');
    $order = xarModGetVar('themes', 'SiteTitleOrder');
 
    /**
        default - Site Name - Module Name - Page Name
        sp - Site Name - Page Name
        mps - Module Name - Page Name - Site Name
        pms - Page Name - Module Name - Site Name
        to - Page Name
        theme - Theme driven
    */

    switch( $order )
    {
        case 'sp':
            $limit = 2;
            $index = 1;
            break;
            
        case 'mps':
            $limit = 3;
            $index = 1;
            break;
            
        case 'pms':
            $limit = 3;
            $index = 0;
            break;
            
        case 'to':
        case 'theme':
            $limit = 1;
            $index = 0;
            break;
            
        default:
            $limit = 3;
            $index = 2;
            break;
    }
    
    $title = split($seperator, $title, $limit);
    
    // This is just incase a page title does not conform to current title order
    while( count($title) < $index )
        $index--;
    
    if(!isset($title[$index]))
        $index = 0;    
    
    return $title[$index];
}

/**
    Formats byte size nicely
    
    @param $size - size in bytes
    
    @return string
*/
function sitesearch_userapi_formatbytesize($args)
{
    extract($args);
    
    $postfix = array("B", "K", "M", "G", "T");
    //Calc nice file size
    $i = 0;
    $displaySize = $size;
    while( $displaySize > 1024 )
    {
        $displaySize /= 1024;
        $i++;
    }

    return round($displaySize, 2) . $postfix[$i];
}
?>