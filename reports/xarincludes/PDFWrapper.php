<?php
// ***
// PDFWrapper Version 1.0b
// ***

// Defines the absolute path to the PDFWrapper library
if (!defined('PDF_WRAPPER')) define('PDF_WRAPPER',dirname(__FILE__));

function PDFLoadCode($type) 
{
  if (!$type) return false;
	include_once(PDF_WRAPPER."/wrappers/PDFWrapper-$type.php");		
	return true;		    
}  
                
function &newPDFWrapper($type) 
{
  $wrapperclass = "PDFWrapper_".$type;
  return new $wrapperclass();
}
                
                
class PDFWrapper 
{
  var $p=''; 
  var $type='';
  function PDFWrapper() 
  {
    die('Virtual Class -- cannot instantiate');
  }
  function getVersion() 
  {
    return "Version 1.1b (c) InterAKT 2001";
  }  
}
?>