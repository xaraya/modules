<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
* Standard function to decode short urls for the module
*
* @author crisp <crisp@crispcreations.co.uk>
* @return array
*/
function crispbb_userapi_decode_shorturl($params)
{
    $args = [];
    $module = 'crispbb';
    /* Check and see if we have a module alias */
    $aliasisset = xarModVars::get($module, 'useModuleAlias');
    $aliasname = xarModVars::get($module, 'aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else {
        $usealias = false;
    }

    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModVars::get($module, 'aliasname');
    }

    // forum index
    if (empty($params[1]) || (preg_match('/^index/i', $params[1]))) {
        return ['forum_index', $args];
    // forum category
    } elseif (preg_match('/^c(_?[0-9 +-]+)/', $params[1], $matches)) {
        $catid = $matches[1];
        $args['catid'] = $catid;
        return ['forum_index', $args];
    // search forums
    } elseif (preg_match('/^search/i', $params[1]) && empty($params[2])) {
        return ['search', $args];
    } elseif (preg_match('/^redirect/i', $params[1]) && empty($params[2])) {
        return ['redirect', $args];
    } elseif (preg_match('/^updatetopic/i', $params[1]) && empty($params[2])) {
        return ['updatetopic', $args];
    } elseif (preg_match('/^updatereply/i', $params[1]) && empty($params[2])) {
        return ['updatereply', $args];
    } elseif (preg_match('/^newtopic/i', $params[1]) && empty($params[2])) {
        return ['newtopic', $args];
    } elseif (preg_match('/^modifytopic/i', $params[1]) && empty($params[2])) {
        return ['modifytopic', $args];
    } elseif (preg_match('/^newreply/i', $params[1]) && empty($params[2])) {
        return ['newreply', $args];
    } elseif (preg_match('/^modifyreply/i', $params[1]) && empty($params[2])) {
        return ['modifyreply', $args];
    } elseif (preg_match('/^movetopic/i', $params[1]) && empty($params[2])) {
        return ['movetopic', $args];
    } elseif (preg_match('/^splittopic/i', $params[1]) && empty($params[2])) {
        return ['splittopic', $args];
    } elseif (preg_match('/^stats/i', $params[1]) && empty($params[2])) {
        return ['stats', $args];
    } elseif (preg_match('/^moderate/i', $params[1]) && empty($params[2])) {
        return ['moderate', $args];
    } elseif (preg_match('/^read/i', $params[1]) && !empty($params[3])) {
        if (preg_match('/^f(_?[0-9 +-]+)/', $params[3], $matches)) {
            $fid = $matches[1];
            $args['action'] = 'read';
            $args['fid'] = $fid;
        }
        return ['forum_index', $args];
    } elseif (preg_match('/^f(_?[0-9 +-]+)/', $params[1], $matches1)) {
        $fid = $matches1[1];
        $args['fid'] = $fid;
        if (!empty($params[2])) {
            if (preg_match('/^newtopic/i', $params[2])) {
                return ['newtopic', $args];
            } elseif (preg_match('/^moderate/i', $params[2])) {
                return ['moderate', $args];
            }
        }
        return ['view', $args];
    // topic id
    } elseif (preg_match('/^t(_?[0-9 +-]+)/', $params[1], $matches1)) {
        $tid = $matches1[1];
        $args['tid'] = $tid;
        if (!empty($params[3])) {
            if (preg_match('/^p(_?[0-9 +-]+)/', $params[2], $matches2)) {
                $pid = $matches2[1];
                $args['pid'] = $pid;
                if (!empty($params[3])) {
                    if (preg_match('/^edit/i', $params[3])) {
                        return ['modifyreply', $args];
                    } elseif (preg_match('/^split/i', $params[3])) {
                        $args['startpid'] = $pid;
                        return ['splittopic', $args];
                    } elseif (preg_match('/^delete/i', $params[3])) {
                        $args['pstatus'] = 5;
                        return ['updatereply', $args];
                    }
                }
                return ['display', $args];
            // topic id
            } elseif (preg_match('/^edit/i', $params[2])) {
                return ['modifytopic', $args];
            } elseif (preg_match('/^newreply/i', $params[2])) {
                return ['newreply', $args];
            } elseif (preg_match('/^move/i', $params[2])) {
                return ['movetopic', $args];
            } elseif (preg_match('/^split/i', $params[2])) {
                return ['splittopic', $args];
            } elseif (preg_match('/^delete/i', $params[2])) {
                $args['tstatus'] = 5;
                return ['updatetopic', $args];
            } elseif (preg_match('/^open/i', $params[2]) || preg_match('/^unlock/i', $params[2]) || preg_match('/^undelete/i', $params[2])) {
                $args['tstatus'] = 0;
                return ['updatetopic', $args];
            } elseif (preg_match('/^close/i', $params[2])) {
                $args['tstatus'] = 1;
                return ['updatetopic', $args];
            } elseif (preg_match('/^lock/i', $params[2])) {
                $args['tstatus'] = 4;
                return ['updatetopic', $args];
            }
        }
        return ['display', $args];
    } elseif (preg_match('/^(\w+)/', $params[1], $matches) && !empty($params[2])) {
        // look for a param identifying component
        if (preg_match('/^f(_?[0-9 +-]+)/', $params[2], $matches1)) {
            $fid = $matches1[1];
            $args['fid'] = $fid;
            if (!empty($params[3])) {
                if (preg_match('/^newtopic/i', $params[3])) {
                    return ['newtopic', $args];
                } elseif (preg_match('/^moderate/i', $params[3])) {
                    return ['moderate', $args];
                }
            }
            return ['view', $args];
        // topic id
        } elseif (preg_match('/^t(_?[0-9 +-]+)/', $params[2], $matches1)) {
            $tid = $matches1[1];
            $args['tid'] = $tid;
            if (!empty($params[3])) {
                if (preg_match('/^p(_?[0-9 +-]+)/', $params[3], $matches2)) {
                    $pid = $matches2[1];
                    $args['pid'] = $pid;
                    if (!empty($params[4])) {
                        if (preg_match('/^edit/i', $params[4])) {
                            return ['modifyreply', $args];
                        } elseif (preg_match('/^split/i', $params[4])) {
                            $args['startpid'] = $pid;
                            return ['splittopic', $args];
                        } elseif (preg_match('/^delete/i', $params[4])) {
                            $args['pstatus'] = 5;
                            return ['updatereply', $args];
                        }
                    }
                    return ['display', $args];
                // topic id
                } elseif (preg_match('/^edit/i', $params[3])) {
                    return ['modifytopic', $args];
                } elseif (preg_match('/^newreply/i', $params[3])) {
                    return ['newreply', $args];
                } elseif (preg_match('/^move/i', $params[3])) {
                    return ['movetopic', $args];
                } elseif (preg_match('/^split/i', $params[3])) {
                    return ['splittopic', $args];
                } elseif (preg_match('/^delete/i', $params[3])) {
                    $args['tstatus'] = 5;
                    return ['updatetopic', $args];
                } elseif (preg_match('/^open/i', $params[3]) || preg_match('/^unlock/i', $params[3]) || preg_match('/^undelete/i', $params[3])) {
                    $args['tstatus'] = 0;
                    return ['updatetopic', $args];
                } elseif (preg_match('/^close/i', $params[3])) {
                    $args['tstatus'] = 1;
                    return ['updatetopic', $args];
                } elseif (preg_match('/^lock/i', $params[3])) {
                    $args['tstatus'] = 4;
                    return ['updatetopic', $args];
                } elseif (preg_match('/^moderate/i', $params[3])) {
                    return ['moderate', $args];
                }
            }
            return ['display', $args];
        } elseif (preg_match('/^p(_?[0-9 +-]+)/', $params[2], $matches1)) {
            $pid = $matches1[1];
            $args['pid'] = $pid;
            return ['displayreply', $args];
        } elseif (preg_match('/^c(_?[0-9 +-]+)/', $params[2], $matches1)) {
            $catid = $matches1[1];
            $args['catid'] = $catid;
            return ['forum_index', $args];
            // search forums
        }
    }
    /* default : return nothing -> no short URL decoded */
}
