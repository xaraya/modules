<?php
/**
 * Publications
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Supported URLs :
 *
 * View:
 * /publications/view (default pubtype)
 * /publications/view/pubtype_id
 * /publications/view/pubtype_name
 *
 * Display:
 * /publications/page_id
 * /publications/pubtype_name/page_name[/page_id]
 * /publications/pubtype_name/page_name[/page_start_date]
 * /publications/display/pubtype_name/page_name[/page_id]
 *
 * New
 * /publications/new/pubtype_name
 * /publications/new/pubtype_id
 *
 * Modify
 * /publications/modify/page_id
 * /publications/modify/pubtype_name/page_name[/page_start_date]
 * /publications/modify/pubtype_name/page_name[/page_id]
 *
 * Delete
 * /publications/delete/page_id
 * /publications/delete/pubtype_name/page_name[/page_start_date]
 * /publications/delete/pubtype_name/page_name[/page_id]
**/

sys::import('xaraya.mapper.controllers.short');
sys::import('xaraya.structures.query');
xarMod::load('publications');

class PublicationsShortController extends ShortActionController
{
    public $pubtypes = array();
    
    public function decode(Array $data=array())
    {
        $token1 = $this->shorturl_decode($this->firstToken());
        switch ($token1) {
            case 'admin':
                return parent::decode($data);
            break;

            case 'search':
                return parent::decode($data);
            break;

            case 'new':
                $data['func'] = 'new';

                // Get the pubtype
                $token2 = $this->nextToken();
                if ($token2) $data['ptid'] = $this->decode_pubtype($token2);
            break;

            case 'create':
                $data['func'] = 'create';
            break;

            case 'modify':
                // Here we are dealing with publications/modify/pubtype[/publication] or publications/modify/itemid
                $data['func'] = 'modify';
                $token2 = $this->shorturl_decode($this->nextToken());
                $token3 = $this->shorturl_decode($this->nextToken());

                if (!$token3 && is_numeric($token2) && !xarModVars::get('publications', 'usetitleforurl')) {
                    // A single numeric token is a page id
                    $data['itemid'] = $token2;
                } else {
                    // Match the first token
                    if ($token2) $data['ptid'] = $this->decode_pubtype($token2);
                }
                // We now have the pubtype; check for the publication if we have another token
                if ($token3) {
                    // This is a publication modify; find which publication
                    $data['itemid'] = $this->decode_page($token3, $data['ptid']);
                }
            break;

            case 'update':
                $data['func'] = 'update';
            break;

            case 'delete':
                $data['func'] = 'delete';
                $token2 = $this->shorturl_decode($this->nextToken());
                $token3 = $this->shorturl_decode($this->nextToken());

                if (!$token3 && is_numeric($token2) && !xarModVars::get('publications', 'usetitleforurl')) {
                    // A single numeric token is a page id
                    $data['itemid'] = $token2;
                } else {
                    // Match the first token
                    if ($token2) $data['ptid'] = $this->decode_pubtype($token2);
                }
                // We now have the pubtype; check for the publication if we have another token
                if ($token3) {
                    // This is a publication delete; find which publication
                    $data['itemid'] = $this->decode_page($token3, $data['ptid']);
                }
            break;

           case 'view':
                $data['func'] = 'view';
                
                // Get the pubtype
                $token2 = $this->nextToken();
                if ($token2) $data['ptid'] = $this->decode_pubtype($token2);
            break;
            
            case 'display':
                $data['func'] = 'display';

                $module = xarController::$request->getModule();
                $roottoken = $this->firstToken();

                // Look for a root page with the name as the first part of the path.
                $rootpage = xarMod::apiFunc(
                    'publications', 'user', 'getpages',
                    array('name' => strtolower($roottoken), 'parent' => 0, 'status' => 'ACTIVE,EMPTY', 'key' => 'pid')
                );
                            
                // If no root page matches, and an alias was provided, look for a non-root start page.
                // These are used as short-cuts.
                if (empty($rootpage) && $module != 'publications') {
                    $rootpage = xarMod::apiFunc(
                        'publications', 'user', 'getpages',
                        array('name' => strtolower($roottoken), 'status' => 'ACTIVE,EMPTY', 'key' => 'pid')
                    );
                }
                            
                // TODO: allow any starting point to be a module alias, and so provide
                // short-cuts to the requested page. For example, the 'about' page could
                // be set as an alias. That page could also be under /site/about, but
                // just 'index.php/about' would work, and would be equivalent to
                // index.php/site/about or index.php/publications/site/about
            
                if (!empty($rootpage)) {
                    // The first part of the path matches 
            
                    // Fetch the complete page tree for the root page.
                    $tree = xarMod::apiFunc(
                        'publications', 'user', 'getpagestree',
                        array(
                            'left_range' => array($rootpage['left'], $rootpage['right']),
                            'dd_flag' => false,
                            'key' => 'pid',
                            'status' => 'ACTIVE,EMPTY'
                        )
                    );
            
                    // TODO: Cache the tree away for use in the main module (perhaps getpagestree can go that?).
                    // If doing that, then ensure the dd data is retrieved at some point.
            
                    // Walk the page tree, matching as many path components as possible.
                    $pid = $rootpage['pid'];
                    
                    while (($token = $this->nextToken()) && isset($tree['child_refs']['names'][$pid]) && array_key_exists(strtolower($token), $tree['child_refs']['names'][$pid])) {
                        $token = strtolower($token);
                        $pid = $tree['child_refs']['names'][$pid][$token];
                    }
            
                    // We have the page ID.
                    $data['pid'] = $pid;
            
                    // Run any further URL decode functions, and merge in the result.
                    // The custom decode URL functions are coded the same as normal
                    // decode functions, but placed into the 'xardecodeapi' API
                    $decode_url = $tree['pages'][$pid]['decode_url'];
                    if (!empty($decode_url)) {
                        // Attempt to invoke the custom decode URL function, suppressing errors.
                        try {
                            $args2 = xarMod::apiFunc('publications', 'decode', $decode_url, $params);
                        } catch (Exception $e) {
                            $args2 = array();
                        }
            
                        // If any decoding was done, merge in the results.
                        if (!empty($args2) && is_array($args2)) {
                            foreach($args2 as $key => $value) {
                                $data[$key] = $value;
                            }
                        }
                    }
                }
            break;

            default:
                // Here we are dealing with publications/pubtype[/publication] or publications/itemid

                $token2 = $this->shorturl_decode($this->nextToken());

                if (!$token2 && is_numeric($token1)) {
                // A single numeric token is an id. We keep this decode option: publications/itemid
                // irrespective of the encoding scheme
                // We do this for folks who are creating hard links in pages so that they can use the convenient publications/itemid
                    $data['itemid'] = $token1;
                    $data['func'] = 'display';
                    return $data;
                } else {
                    // Match the first token
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        if ($token1) $data['ptid'] = $this->decode_pubtype($token1);
                    }
                }

                // We now have the pubtype; check for the publication
                if (!$token2) {
                    // No more tokens; set this as a view or display, depending on whether the previous token was an id or not
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        $data['func'] = 'view';
                    } else {
                        $data['func'] = 'display';
                        unset($data['ptid']);
                        $data['itemid'] = $token1;
                    }
                } else {
                    // This is a publication display; find which publication
                    // Bail if we don't have a valid pubtype 
                    if (!isset($data['ptid'])) $data['ptid'] = 0;
                    $data['id'] = $this->decode_page($token2, $data['ptid']);
                    $data['func'] = 'display';
                }
            break;
        }
        return $data;
    }

    public function encode(xarRequest $request)
    {
        if ($request->getType() == 'admin') return parent::encode($request);

        $params = $request->getFunctionArgs();
        $path = array();
        switch($request->getFunction()) {

            case 'search':
                $path[] = 'search';
                $path = array_merge($path,$params);
            break;

            case 'new':
                $path[] = 'new';
                if (isset($params['ptid'])) {
                    $path[] = $this->encode_pubtype($params['ptid']);
                    unset($params['ptid']);
                }
            break;

            case 'create':
                $path[] = 'create';
                $path = array_merge($path,$params);
            break;

            case 'modify':
                $path[] = 'modify';
                if (isset($params['itemid'])) {
                    $row = $this->getpage($params['itemid']);
                    if (!empty($row['id'])) {
                        $path = array_merge($path,$this->encode_page($row));
                    }
                }
                $params = array();
            break;

            case 'update':
                $path[] = 'update';
                $path = array_merge($path,$params);
            break;

            case 'delete':
                $path[] = 'delete';
                if (isset($params['itemid'])) {
                    $row = $this->getpage($params['itemid']);
                    if (!empty($row['id'])) {
                        $path = array_merge($path,$this->encode_page($row));
                    }
                }
                $params = array();
            break;

            case 'view':
                $path[] = 'view';
                if (isset($params['ptid'])) {
                    $path[] = $this->encode_pubtype($params['ptid']);
                    unset($params['ptid']);
                }
            break;
            
            case 'viewmap':
                $path[] = 'viewmap';
                $params = array();
            break;

            case 'display':
                $path[] = 'display';
                if (isset($params['itemid'])) {
                    $row = $this->getpage($params['itemid']);
                    if (!empty($row['id'])) {
                        $path = $this->encode_page($row);
                    }
                }
                $params = array();
            break;
            
            case 'main':

                // We need a page ID to continue, for now.
                // TODO: allow this to be expanded to page names.
                if (empty($params['pid'])) return;

                static $pages = NULL;

                // The components of the path.
            //    $get = $args;
            
                // Get the page tree that includes this page.
                // TODO: Do some kind of cacheing on a tree-by-tree basis to prevent
                // fetching this too many times. Every time any tree is fetched, anywhere
                // in this module, it should be added to the cache so it can be used again.
                // For now we are going to fetch all pages, without DD, to cut down on
                // the number of queries, although we are making an assumption that the
                // number of pages is not going to get too high.
                if (empty($pages)) {
                    // Fetch all pages, with no DD required.
                    $pages = xarMod::apiFunc(
                        'publications', 'user', 'getpages',
                        array('dd_flag' => false, 'key' => 'pid' /*, 'status' => 'ACTIVE'*/)
                    );
                }

                // Check that the pid is a valid page.
                if (!isset($pages[$params['pid']])) return;


                $use_shortest_paths = xarModVars::get('publications', 'shortestpath');

                // Consume the pid from the get parameters.
                $pid = $params['pid'];
                unset($params['pid']);

                // 'Consume' the function now we know we have enough information.
//                unset($params['func']);

                // Follow the tree up to the root.
                $pid_follow = $pid;
                while ($pages[$pid_follow]['parent_key'] <> 0) {
                    // TODO: could do with an API to get all aliases for a given module in one go.
                    if (!empty($use_shortest_paths) && xarModAlias::resolve($pages[$pid_follow]['name']) == 'publications') {
                        break;
                    }
                    array_unshift($path, $pages[$pid_follow]['name']);
                    $pid_follow = $pages[$pid_follow]['parent_key'];
                }

                // Do the final path part.
                array_unshift($path, $pages[$pid_follow]['name']);
            
                // If the base path component is not the module alias, then add the
                // module name to the start of the path.
                if (xarModAlias::resolve($pages[$pid_follow]['name']) != 'publications') {
//                    array_unshift($path, 'publications');
                }

                // Now we have the basic path, we can check if there are any custom
                // URL handlers to handle the remainder of the GET parameters.
                // The handler is placed into the xarencodeapi API directory, and will
                // return two arrays: 'path' with path components and 'get' with
                // any unconsumed (or new) get parameters.
                if (!empty($pages[$pid]['encode_url'])) {
                    $extra = xarMod::apiFunc('publications', 'encode', $pages[$pid]['encode_url'], $get, false);
            
                    if (!empty($extra)) {
                        // The handler has supplied some further short URL path components.
                        if (!empty($extra['path'])) {
                            $path = array_merge($path, $extra['path']);
                        }
            
                        // Assume it has consumed some GET parameters too.
                        // Take what is left (i.e. unconsumed).
                        if (isset($extra['get']) && is_array($extra['get'])) {
                            $get = $extra['get'];
                        }
                    }
                }
            break;

            default:
                return;
            break;
            
        }
        // Encode the processed params
        $request->setFunction($this->getFunction($path));

        // Send the unprocessed params back
        $request->setFunctionArgs($params);
        return parent::encode($request);
    }    
    
    private function decode_pubtype($token='')
    {
        $token = $this->shorturl_decode($token);
        if (xarModVars::get('publications', 'usetitleforurl')) {
            // Get all publication types present
            if (empty($this->pubtypes)) $this->pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');
            // Match the first token
            foreach ($this->pubtypes as $id => $pubtype) {
                if (strtolower($token) == strtolower($pubtype['description'])) {
                    return $id;
                    break;
                }
            }
        } else {
            return $token;
        }
    }
    
    private function decode_page($token2='', $ptid)
    {
        $xartables =& xarDB::getTables();
        $q = new Query('SELECT',$xartables['publications']);
        $q->eq('pubtype_id',$ptid);
        switch ((int)xarModVars::get('publications', 'usetitleforurl')) {
            case 0:
                $q->eq('id',(int)$token2);
            break;
            case 1:
                $q->eq('name', $token2);
                $token3 = $this->shorturl_decode($this->nextToken());
                if ($token3) {
                    $timestamp = strtotime($token3);
                    $q->ge('start_date',$timestamp);
                    $q->le('start_date',$timestamp + 100);
                }
            break;
            case 2:
                $q->eq('name',$token2);
                $token3 = (int)$this->nextToken();
                if ($token3) {
                    $q->eq('id', $token3);
                }
            break;
            case 3:
                $q->eq('id', (int)$token2);
            break;
            case 4:
                $q->eq('name', $token2);
            break;
            case 5:
                $q->eq('title', $token2);
                $token3 = $this->shorturl_decode($this->nextToken());
                if ($token3) {
                    $timestamp = strtotime($token3);
                    $q->ge('start_date',$timestamp);
                    $q->le('start_date',$timestamp + 100);
                }
            break;
            case 6:
                $q->eq('title', $token2);
                $token3 = (int)$this->nextToken();
                if ($token3) {
                    $q->eq('id', $token3);
                }
            break;
            case 7:
                $q->eq('id', (int)$token2);
            break;
            case 8:
                $q->eq('title', $token2);
            break;
        }
        $q->addfield('id');
        $q->run();
        $result = $q->row();
        if (!empty($result['id'])) return (int)$result['id'];
        else return 0;
    }

    private function encode_page($row=array())
    {
        $usetitles = xarModVars::get('publications', 'usetitleforurl');
        $path = array();
        if ($usetitles == 0) {
            // We're not using names: just use the ID
            $path[] = $row['id'];
        } elseif ($usetitles == 4) {
            // We're ignoring duplicates: just slap in the pubtype and name
            $path[] = $this->encode_pubtype($row['pubtype_id']);
            if (!empty($row['name'])) $path[] = $this->shorturl_encode($row['name']);
        } elseif ($usetitles == 8) {
            // We're ignoring duplicates: just slap in the pubtype and title
            $path[] = $this->encode_pubtype($row['pubtype_id']);
            if (!empty($row['title'])) $path[] = $this->shorturl_encode($row['title']);
        } elseif (!empty($row['name']) && in_array($usetitles, array(1,2,3))) {
            // Now come the cases where we distinguish duplicates in the URL
            // For this we need to do another SELECT on the name to see if there are actually duplicates
            $xartables =& xarDB::getTables();
            $q = new Query('SELECT',$xartables['publications']);
            $q->eq('name',$row['name']);
            $q->eq('pubtype_id',$row['pubtype_id']);
            $q->addfield('pubtype_id');
            $q->addfield('name');
            $q->addfield('start_date');
            $q->addfield('id');
            $q->run();
            $duplicates = $q->output();
            
            $path[] = $this->encode_pubtype($row['pubtype_id']);
            if (count($duplicates) == 1) {
                // No duplicates, so we just put the name
                $path[] = $this->shorturl_encode($row['name']);
            } elseif ($usetitles == 1) {
                // We will add the publication start date to distinguish duplicates
                $path[] = $this->shorturl_encode($row['name']);
                $path[] = date('Y-m-d H:i',$this->shorturl_encode($row['start_date']));
            } elseif ($usetitles == 2) {
                // We will add the publication ID to distinguish duplicates
                $path[] = $this->shorturl_encode($row['name']);
                $path[] = $row['id'];
            } elseif ($usetitles == 3) {
                // We will use just the publication ID to distinguish duplicates
                $path[] = $row['id'];
            }
        } elseif (!empty($row['title']) && in_array($usetitles, array(5,6,7))) {
            // Now come the cases where we distinguish duplicates in the URL
            // For this we need to do another SELECT on the name to see if there are actually duplicates
            $xartables =& xarDB::getTables();
            $q = new Query('SELECT',$xartables['publications']);
            $q->eq('title',$row['title']);
            $q->eq('pubtype_id', (int)$row['pubtype_id']);
            $q->addfield('pubtype_id');
            $q->addfield('title');
            $q->addfield('start_date');
            $q->addfield('id');
            $q->run();
            $duplicates = $q->output();
            
            $path[] = $this->encode_pubtype($row['pubtype_id']);
            if (count($duplicates) == 1) {
                // No duplicates, so we just put the name
                $path[] = $this->shorturl_encode($row['title']);
            } elseif ($usetitles == 5) {
                // We will add the publication start date to distinguish duplicates
                $path[] = $this->shorturl_encode($row['title']);
                $path[] = $this->shorturl_encode(date('Y-m-d H:i',$row['start_date']));
            } elseif ($usetitles == 6) {
                // We will add the publication ID to distinguish duplicates
                $path[] = $this->shorturl_encode($row['title']);
                $path[] = $row['id'];
            } elseif ($usetitles == 7) {
                // We will use just the publication ID to distinguish duplicates
                $path[] = $row['id'];
            }
                
        }
        return $path;
    }

    private function encode_pubtype($ptid=0)
    {
        if (xarModVars::get('publications', 'usetitleforurl')) {
            // Get all publication types present
            if (empty($this->pubtypes)) $this->pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');
            // Match to the function token
            foreach ($this->pubtypes as $id => $pubtype) {
                if ($ptid == $id) {
                    return $this->shorturl_encode(strtolower($pubtype['description']));
                    break;
                }
            }
            return 0;
        } else {
            return $ptid;
        }
    }
    private function getpage($itemid=0)
    {
        $xartables =& xarDB::getTables();
        $q = new Query('SELECT',$xartables['publications']);
        $q->eq('id', (int)$itemid);
        $q->addfield('pubtype_id');
        $q->addfield('name');
        $q->addfield('title');
        $q->addfield('start_date');
        $q->addfield('id');
        $q->run();
        $result = $q->row();
        return $result;
    }

    private function shorturl_encode($string)
    {   
        $string = urlencode($string);
        $allows_slashes = xarConfigVars::get(null, 'Site.Core.WebserverAllowsSlashes');
        if (empty($allows_slashes)) $string = urlencode($string);
        return $string;
    }
    
    private function shorturl_decode($string)
    {   
        $string = urldecode($string);
        $allows_slashes = xarConfigVars::get(null, 'Site.Core.WebserverAllowsSlashes');
        if (empty($allows_slashes)) $string = urldecode($string);
        return $string;
    }
}
?>