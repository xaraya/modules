<?php

function &flashservices_userapi_initflashservices()
{
  include xarModGetVar('flashservicesapi','gatewayPath');
  
  $gateway = new Gateway();
  $gateway->setBaseClassPath(xarModGetVar('flashservicesapi','servicesPath'));
  
  return $gateway;
}

?>