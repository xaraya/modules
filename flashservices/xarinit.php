<?php

function flashservicesapi_init() 
{
  $path = './modules/'.xarModGetName().'/';
  $gatewayPath = $path.'classes/app/Gateway.php';
  $servicesPath = $path.'services/';
  
  xarModSetVar('flashservicesapi','gatewayPath', $gatewayPath);
  xarModSetVar('flashservicesapi','servicesPath', $servicesPath);
  
  return file_exists($gatewayPath) && file_exists($servicesPath); 
}

/**
 * upgrade the flashservicesapi module from an old version
 * This function can be called multiple times
 */
function flashservicesapi_upgrade($oldversion) 
{ 
  return true; 
}

/**
 * delete the flashservicesapi module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function flashservicesapi_delete()
{

  xarModDelVar('flashservicesapi','gatewayPath');
  xarModDelVar('flashservicesapi','servicesPath');

  // Remove Masks and Instances
  xarRemoveMasks('flashservicesapi');
  xarRemoveInstances('flashservicesapi');

  return true;
}

?>