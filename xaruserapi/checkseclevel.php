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
 * Standard function to check sec level for current user for a given component and privilege mask
 * (note, privilege masks here are internal masks used by crispBB,
 *  and not the module masks used by the privileges module)
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param array $args['check'] component to check privileges against (either forum, topic, or post)
 * @param string $args['priv'] privilege to check for specified component
 * @return array
 */
function crispbb_userapi_checkseclevel($args)
{
    extract($args);

    static $loggedin = NULL;
    static $uid = 0;
    //static $checks = array();

    if (is_null($loggedin)) {
        $loggedin=xarUserIsLoggedIn();
    }
    if (empty($uid)) {
        $uid = xarUserGetVar('id');
    }

    if (!isset($fid) || empty($fid) || !is_numeric($fid)) $fid = NULL;
    if (empty($fid) && isset($check['fid']) && !empty($check['fid']) && is_numeric($check['fid'])) $fid = $check['fid'];

    if (!isset($catid) || empty($catid) || !is_numeric($catid)) $catid = NULL;
    if (empty($catid) && isset($check['catid']) && !empty($check['catid']) && is_numeric($check['catid'])) $catid = $check['catid'];

    /*
    if (isset($checks[$catid][$fid][$tid][$priv])) {
        //return $checks[$catid][$fid][$tid][$priv];
    }
    */
    $userLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
        array('catid' => $catid, 'fid' => $fid));

    // no privileges for current instance
    if (empty($userLevel)) return false;

    if (isset($check) && is_array($check)) {
        if (empty($check['fprivileges'][$userLevel])) return false;
        $privs = $check['fprivileges'][$userLevel];
        if (!empty($priv)) {
            switch ($priv) {
                case 'viewforum':
                    if (!empty($privs['viewforum'])) return $userLevel;
                break;
                case 'readforum':
                    if (!empty($privs['readforum'])) {
                        return $userLevel;
                        // this causes locked topics to display to users without locktopic privs
                        // CHECKME: eval for possible conflicts
                        /*
                        if ($check['tstatus'] < 4) {
                            return $userLevel;
                        } elseif ($check['tstatus'] == 4) {
                            if (!empty($privs['locktopics'])) return $userLevel;
                        } elseif ($check['tstatus'] == 5) {
                            if (!empty($privs['deletetopics'])) return $userLevel;
                        }
                        */
                    }
                break;
                case 'newtopic':
                    if (!$loggedin) return false;
                    if (!empty($privs['editforum'])) return $userLevel;
                    if ($check['fstatus'] == 0 && !empty($privs['newtopic'])) {
                        return $userLevel;
                    }
                break;
                case 'newreply':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['newreply'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'editowntopic':
                case 'edittopics':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if ((!empty($privs['editowntopic']) && $check['towner'] == $uid) || !empty($privs['edittopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'editownreply':
                case 'editreplies':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if ((!empty($privs['editownreply']) && $check['powner'] == $uid) || !empty($privs['editreplies'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'closeowntopic':
                case 'closetopics':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if ((!empty($privs['closeowntopic']) && $check['towner'] == $uid) || !empty($privs['closetopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                                //return false;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'stickies':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['stickies'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'announcements':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['announcements'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }

                break;
                case 'faqs':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['faqs'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'bbcode':
                    if (!$loggedin) return false;

                break;
                case 'bbcodedeny':
                    if (!$loggedin) return false;

                break;
                case 'smilies':
                    if (!$loggedin) return false;

                break;
                case 'smiliesdeny':
                    if (!$loggedin) return false;

                break;
                case 'html':
                    if (!$loggedin) return false;

                break;
                case 'htmldeny':
                    if (!$loggedin) return false;

                break;
                /*
                case 'edittopics':
                    if (!$loggedin) return false;

                break;
                case 'editreplies':
                    if (!$loggedin) return false;

                break;
                case 'closetopics':
                    if (!$loggedin) return false;

                break;
                */
                case 'locktopics':
                     if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['locktopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'movetopics':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['movetopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'splittopics':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['splittopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'approvetopics':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['approvetopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'approvereplies':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['approvereplies'])) {
                            if ($check['pstatus'] == 5) {
                                if (empty($privs['deletereplies'])) return false;
                            }
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'deletetopics':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['deletetopics'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return $userLevel;
                            }
                        }
                    }
                break;
                case 'deletereplies':
                    if (!$loggedin) return false;
                    if ($check['fstatus'] == 0 || !empty($privs['editforum'])) {
                        if (!empty($privs['deletereplies'])) {
                            if ($check['tstatus'] == 0) {
                                return $userLevel;
                            } elseif ($check['tstatus'] == 1) {
                                if (!empty($privs['closetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 2) {
                                if (!empty($privs['approvetopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 3) {
                                return false;
                            } elseif ($check['tstatus'] == 4) {
                                if (!empty($privs['locktopics'])) return $userLevel;
                            } elseif ($check['tstatus'] == 5) {
                                return false;
                            }
                        }
                    }
                break;
                case 'ismoderator':
                    if (!$loggedin) return false;
                    if (!empty($privs['editforum'])) return $userLevel;
                    if ($check['fstatus'] == 0) {
                        if (!empty($privs['closetopics']) ||
                            !empty($privs['edittopics']) ||
                            !empty($privs['editreplies']) ||
                            !empty($privs['locktopics']) ||
                            !empty($privs['movetopics']) ||
                            !empty($privs['splittopics']) ||
                            !empty($privs['approvetopics']) ||
                            !empty($privs['locktopics']) ||
                            !empty($privs['deletetopics'])) {
                            return $userLevel;
                        }
                    }
                break;

                case 'addforum':
                    if (!$loggedin) return false;
                    if (!empty($privs['editforum'])) return $userLevel;
                    if (!empty($privs['addforum'])) return $userLevel;
                break;
                case 'editforum':
                    if (!$loggedin) return false;
                    if (!empty($privs['editforum'])) return $userLevel;
                break;
                case 'deleteforum':
                    if (!$loggedin) return false;
                    if (!empty($privs['deleteforum'])) return $userLevel;
                break;
                case 'adminforum':
                    if (!$loggedin) return false;
                    if ($userLevel == 800) return $userLevel;
                break;


            }
        } elseif (!empty($urlparam)) {

        }
    }


    return false;

}
?>