<?php
/**
 * Create a new phplot item
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage PHPlot Module
 * @link http://xaraya.com/index.php/release/818.html
 * @author PHPlot Module Development Team
 */

/**
 * Create a new phplot item
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the PHPlot module development team
 * @param  string $args['name'] name of the item
 * @param  int    $args['number'] number of the item
 * @return int phplot item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function phplot_adminapi_create($args)
{
 /* Get arguments from argument array - all arguments to this function
     * should be obtained from the $args array, getting them from other
     * places such as the environment is not allowed, as that makes
     * assumptions that will not hold in future versions of Xaraya
     */
    extract($args);
 /* Argument check - make sure that all required arguments are present
     * and in the right format, if not then set an appropriate error
     * message and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...
     */
    $invalid = array();
    if (!isset($DataValues) || !is_array($DataValues)) {
        $invalid[] = 'DataValues';
    }
    // For the moment, generate an error
    if (!isset($DataType) || !is_string($DataType)) {
        $invalid[] = 'DataType';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'PHPlot');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
            * avoid potential security holes or just too much wasted processing
           */
    if (!xarSecurityCheck('AddPHPlot', 1, 'Item', "All")) {
        return;
    }
    
    /* Return the graph */
    return $exid;
}
?>