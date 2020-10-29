 <?php
 
 function curPageURL()
 {
     $pageURL = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))?'https':'http';
     $pageURL .= '://' . $_SERVER['SERVER_NAME'];
     if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != '80')) {
         $pageURL .= ':' . $_SERVER['SERVER_PORT'];
     }
     $pageURL .= $_SERVER['REQUEST_URI'];
     return $pageURL;
 }

?>