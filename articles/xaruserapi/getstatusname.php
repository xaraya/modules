<?php
function articles_userapi_getstatusname( $args )
{
    extract($args);

	switch( $status )
	{
		case 0:
			$name = xarML('Submitted');
			break;
		case 1:
			$name = xarML('Rejected');
			break;
		case 2:
			$name = xarML('Approved');
			break;
		case 3:
			$name = xarML('Frontpage');
			break;
		default:
			$name = xarML('Unknown');
	}
	
	return $name;
}
?>
