<?php

function &flashservices_userapi_initflashservices()
{
  $gatewayPath = xarModGetVar('flashservices','gatewayPath');
  $servicesPath = xarModGetVar('flashservices','servicesPath');

  if (!file_exists($gatewayPath) || !file_exists($servicesPath)) {
    return false;
  }// if
  
  include $gatewayPath;
  
  if (class_exists('Gateway')) {
    $gateway = new Gateway();
    $gateway->setBaseClassPath($servicesPath);
    return $gateway;
    
  } else {
    return false;
    
  }// if
  
}

?>