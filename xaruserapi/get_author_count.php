<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get the number of comments for a module based on the author
 *
 * @author mikespub
 * @access public
 * @param integer    $moduleid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $author      the id of the author you want to count comments for
 * @param integer    $status    (optional) the status of the comments to tally up
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_author_count($args)
{
    extract($args);

    $exception = false;

    if (!isset($moduleid) || empty($moduleid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'moduleid',
            'userapi',
            'get_author_count',
            'comments'
        );
        throw new BadParameterException($msg);
    }


    if (!isset($author) || empty($author)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'author',
            'userapi',
            'get_author_count',
            'comments'
        );
        throw new BadParameterException($msg);
    }

    if (!isset($status) || !is_numeric($status)) {
        $status = _COM_STATUS_ON;
    }

    $tables =& xarDB::getTables();
    $q = new Query('SELECT', $tables['comments']);
    $q->addfield('COUNT(id) AS numitems');
    $q->eq('module_id', $moduleid);
    $q->eq('author', $author);
    $q->eq('status', $status);
    if (isset($itemtype) && is_numeric($itemtype)) {
        $q->eq('itemtype', $itemtype);
    }
    $q->run();
    $result = $q->row();
    
    return $result['numitems'];
}
