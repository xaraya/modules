<?php
/**
 * Search in Courses Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses Development team
 */
/**
 * Search for a course via content description
 *
 * @author Michel V.
 * original author Jim McDonalds, dracos, mikespub et al.
 * @param string q The search token
 * @param bool bool
 * @param string sort The sort order
 * @param int name Search for the name of the course
 * @param int shortdesc Search for a part of the short course description
 * @param int longdesc Search for a part of the long description
 * @param int number Search in the course number
 * @return array with courses
 */
function courses_user_search()
{
    if (!xarVarFetch('q', 'isset', $q, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('bool', 'isset', $bool, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort','isset', $sort, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('name', 'int:0:1', $name, 1, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('shortdesc', 'int:0:1', $shortdesc, 1, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('longdesc', 'int:0:1', $longdesc, 1, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('number', 'int:0:1', $number, 0, XARVAR_DONT_SET)) return;

    $data = array();
    $data['name'] = $name;
    $data['number'] = $number;
    $data['shortdesc'] = $shortdesc;
    $data['longdesc'] = $longdesc;

    if($q == ''){
        return $data;
    }

    // Search course information
    $data['courses'] = xarModAPIFunc('courses',
                          'user',
                          'search',
                           array('name' => $name,
                                 'number' => $number,
                                 'shortdesc' => $shortdesc,
                                 'longdesc' => $longdesc,
                                 'q' => $q));

    if (empty($data['courses'])){
        $data['status'] = xarML('No Course Found Matching Search');
    }

    return $data;
}

?>