<?php

function flashservices_init() 
{
  $path = './modules/flashservices/';
  $gatewayPath = $path.'classes/app/Gateway.php';
  $servicesPath = $path.'services/';

  xarModSetVar('flashservices','gatewayPath', $gatewayPath);
  xarModSetVar('flashservices','servicesPath', $servicesPath);
  
  return file_exists($gatewayPath) && file_exists($servicesPath); 
}

/**
 * upgrade the flashservicesapi module from an old version
 * This function can be called multiple times
 */
function flashservices_upgrade($oldversion) 
{ 
  return true; 
}

/**
 * delete the flashservicesapi module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function flashservices_delete()
{

  xarModDelVar('flashservices','gatewayPath');
  xarModDelVar('flashservices','servicesPath');

  // Remove Masks and Instances
  xarRemoveMasks('flashservices');
  xarRemoveInstances('flashservices');

  return true;
}

?>