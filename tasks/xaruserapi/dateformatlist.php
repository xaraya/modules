<?php
/**
 * Get the date formats supported
 *
 */
function tasks_userapi_dateformatlist() {
	$dateformatlist = array(xarML('Please choose a Date/Time Format'),
							'%m/%d/%Y',
							'%m.%d.%y',
							'%B %d, %Y',
							'%a, %B %d, %Y',
							'%A, %B %d, %Y',
							'%m/%d/%Y %H:%M',
							'%m.%d.%y %H:%M',
							'%B %d, %Y %H:%M',
							'%a, %B %d, %Y %H:%M',
							'%A, %B %d, %Y %H:%M',
							'%m/%d/%Y %I:%M %p',
							'%m.%d.%y %I:%M %p',
							'%B %d, %Y %I:%M %p',
							'%a, %B %d, %Y %I:%M %p',
							'%A, %B %d, %Y %I:%M %p');
	return $dateformatlist;
}
    
?>