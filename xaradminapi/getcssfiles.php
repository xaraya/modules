<?php
function netquery_adminapi_getcssfiles($dirname="./modules/netquery/xarstyles")
{
   $ext = array("css");
   $files = array();
   if($handle = opendir($dirname))
   {
       while(false !== ($file = readdir($handle)))
           for($i=0;$i<sizeof($ext);$i++)
               if(strstr($file, ".".$ext[$i]))
                   $files[] = getFilename( $file, false );
       closedir($handle);
   }
   sort($files);
   return($files);
}
function getFilename( $file, $extension = true )
{
   return ($extension || false === $dot = strrpos( basename( $file ), '.' )) 
       ? basename( $file ) : substr( basename( $file ), 0, $dot ); 
}
?>