<?php
/**
* Get sample code
*
* @package modules
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get sample code
*
* Return a sample of PHP code for demonstrating the usage
* of this module
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  string of sample code
*/
function highlight_userapi_getsamplecode($args)
{
    extract($args);

    // get defaults
    if (empty($string)) {
        $string = xarModGetVar('highlight', 'string');
    }

    $code = '<p>I\'ve been coding so much lately that I\'ve begun to think
in PHP.  Honest!  I was at the grocery store today and caught
myself thinking:</p>

<div '.$string.'="php">
if ($pears == "bartlett") {
    echo "Yummy!";
    $me = new Eating($pears);
    while ($me->food_remaining) {
        $me->bite();
        $me->chew();
        $me->swallow();
    }
} else {
    echo "No, thanks.";
}
</div>

<p>I guess that means I need a break...</p>
';

    return $code;
}

?>
