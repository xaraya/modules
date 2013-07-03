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
 * /publications/pubtype_name/page_name...
 * /publications/page_id...
**/

sys::import('xaraya.mapper.controllers.short');

class PublicationsShortController extends ShortActionController
{
    public $pubtypes = array();
    
    function decode(Array $data=array())
    {
        $token1 = $this->firstToken();
        switch ($token1) {
            case 'admin':
                return parent::decode($data);
            break;

            case 'search':
                return parent::decode($data);
            break;

            case 'new':
                $data['func'] = 'new';
            break;

            case 'create':
                $data['func'] = 'create';
            break;

            case 'modify':
                $data['func'] = 'modify';
            break;

            case 'update':
                $data['func'] = 'update';
            break;

            case 'delete':
                $data['func'] = 'delete';
            break;

           case 'view':
                $data['func'] = 'view';
                
                // Get the pubtype
                $token2 = $this->nextToken();
                if ($token2) {
                    // Get all publication types present
                    if (empty($this->pubtypes)) $this->pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
                    
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        // Match the first token
                        foreach ($this->pubtypes as $id => $pubtype) {
                            if (strtolower($token2) == strtolower($pubtype['description'])) {
                                $data['ptid'] = $id;
                                break;
                            }
                        }
                    } else {
                        $data['ptid'] = $token2;
                    }
                }
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
                // Get all publication types present
                if (empty($this->pubtypes)) $this->pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
                
                $token2 = $this->nextToken();

                // A single numeric token is an id
                if (!$token2 && is_numeric($token1) && !xarModVars::get('publications', 'usetitleforurl')) {
                    $data['itemid'] = $token1;
                } else {
                
                    // Match the first token
                    foreach ($this->pubtypes as $id => $pubtype) {
                        if (xarModVars::get('publications', 'usetitleforurl')) {
                            if (strtolower($token1) == strtolower($pubtype['description'])) {
                                $data['ptid'] = $id;
                                break;
                            }
                        } else {
                            if ($token1 == $id) {
                                if ($token2) {
                                    $data['ptid'] = $id;
                                } else {
                                    $data['itemid'] = $id; // This should neve happen. should be taken care of above
                                }
                                break;
                            }
                        }
                    }
                }

                // We now have the pubtype; check for the publication
                if (!$token2) {
                    // No more tokens; set this as a view or display, depending on whether the token was an id or not
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        $data['func'] = 'view';
                    } else {
                        $data['func'] = 'display';
                    }
                } else {
                    // This is a publication display; find which publication
                    // Bail if we don't have a valid pubtype 
                    if (!isset($data['ptid'])) $data['ptid'] = 0;
                    sys::import('xaraya.structures.query');
                    xarModLoad('publications');
                    $xartables = xarDB::getTables();
                    $q = new Query('SELECT',$xartables['publications']);
                    $q->eq('pubtype_id',$data['ptid']);
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        $q->eq('name',$token2);
                    } else {
                        $q->eq('id',(int)$token2);
                    }
                    $q->addfield('id');
                    $q->run();
                    $result = $q->row();
                    if (!empty($result['id'])) $data['id'] = $result['id'];
                    else $data['id'] = 0;
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
                $path = array_merge($path,$params);
            break;

            case 'create':
                $path[] = 'create';
                $path = array_merge($path,$params);
            break;

            case 'modify':
                $path[] = 'modify';
                $path = array_merge($path,$params);
            break;

            case 'update':
                $path[] = 'update';
                $path = array_merge($path,$params);
            break;

            case 'delete':
                $path[] = 'delete';
                $path = array_merge($path,$params);
            break;

            case 'view':
                $path[] = 'view';
                if (isset($params['ptid'])) {
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        // Get all publication types present
                        if (empty($this->pubtypes)) $this->pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
                        // Match to the function token
                        foreach ($this->pubtypes as $id => $pubtype) {
                            if ($params['ptid'] == $id) {
                                $path[] = strtolower($pubtype['description']);
                                break;
                            }
                        }
                    } else {
                        $path[] = $params['ptid'];
                    }
                }
                unset($params['ptid']);
            break;
            
            case 'viewmap':
                $path[] = 'viewmap';
                $params = array();
            break;

            case 'display':
                
                if (isset($params['itemid'])) {
                    sys::import('xaraya.structures.query');
                    xarModLoad('publications');
                    $xartables = xarDB::getTables();
                    $q = new Query('SELECT',$xartables['publications']);
                    $q->eq('id',$params['itemid']);
                    $q->addfield('pubtype_id');
                    $q->addfield('name');
                    $q->addfield('id');
                    $q->run();
                    $result = $q->row();
                    if (xarModVars::get('publications', 'usetitleforurl')) {
                        // Get all publication types present
                        if (empty($this->pubtypes)) $this->pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
                        if (!empty($result['pubtype_id'])) $path[] = strtolower($this->pubtypes[$result['pubtype_id']]['description']);
                        if (!empty($result['name'])) $path[] = strtolower($result['name']);
                    } else {
                        if (!empty($result['id'])) $path[] = $result['id'];
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
                    if (!empty($use_shortest_paths) && xarModGetAlias($pages[$pid_follow]['name']) == 'publications') {
                        break;
                    }
                    array_unshift($path, $pages[$pid_follow]['name']);
                    $pid_follow = $pages[$pid_follow]['parent_key'];
                }

                // Do the final path part.
                array_unshift($path, $pages[$pid_follow]['name']);
            
                // If the base path component is not the module alias, then add the
                // module name to the start of the path.
                if (xarModGetAlias($pages[$pid_follow]['name']) != 'publications') {
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
}
?>