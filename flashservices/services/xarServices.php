<?php 

class xarServices
{ 
	var $module;
	var $func; 
	var $type;
	var $methodTable;
	
	function xarServices()
	{ 
		$this->methodTable = array( 
			'xarModApiService' => array( 
				'description' => "Calls the Xaraya Mod Api Function", 
				'access' => 'remote', 
				'arguments' => array('module', 'type', 'func', 'args') 
			), 
			'xarModFuncService' => array(
				'description' => "Calls the Xaraya Mod Function",
				'access' => 'remote',
				'arguments' => array('module', 'type', 'func', 'args') 			
			)
		);
	}
	
	/*********************************************************************\	
	\*********************************************************************/
	function xarModApiService($module, $type, $func, $args = array())
	{ 
		//we need to change the working directory so that xarModApiFunc will 
		//be able to find the correct file. 
		$curdir = getcwd();
		$curdir = preg_replace('/\\\\/', '/', $curdir);
		$dir = preg_replace('/\/modules\/.*$/', '/', $curdir);				
		chdir($dir);
		if(empty($module)) { 
			return "You must give the module";
		}
		if(empty($func)) { 
			$func = 'main';
		}
		
		if(empty($type)) { 
			$type = 'user';
		}
		
		if(empty($args)) { 
			$args = array();
		}
		///return $module;
		return xarModApiFunc($module, $type, $func, $args);	
	}
	
	/*********************************************************************\	
	\*********************************************************************/
	function xarModFuncService($module, $type, $func, $args = array())
	{ 
		$curdir = getcwd();
		$curdir = preg_replace('/\\\\/', '/', $curdir);
		$dir = preg_replace('/\/modules\/.*$/', '/', $curdir);
		chdir($dir);
		
		if(empty($module)) { 
			return "You must give the module";
		}
		if(empty($func)) { 
			$func = 'main';
		}
		
		if(empty($type)) { 
			$type = 'user';
		}
		
		if(empty($args)) { 
			$args = array();
		}
	
		return xarModFunc($module, $type, $func, $args);
	}
}

?>