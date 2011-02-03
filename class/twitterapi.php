<?php
sys::import('modules.libtwitteroauth.class.twitteroauth');
class TwitterAPI extends TwitterOAuth
{
    // Overloaded properties from TwitterOAuth
    public $useragent = 'xarTwitter v0.9.0';
    public $format = 'xml';
    
    // Caching properties specific to TwitterAPI
    public $cached = true;
    public $expires = 300;

    /**
     * GET wrapper for oAuthRequest.
     * Overloaded here to take advantage of Xaraya's filesystem caching
     */
    function get($url, $parameters = array()) 
    {
        // add caching to GET requests
        if ($this->cached) {
            sys::import('xaraya.caching');
            // init the filesystem cache object
            $fileCache = xarCache::getStorage(array(
                'storage' => 'filesystem', 
                'cachedir' => sys::varpath() . '/cache', 
                'type' => 'twitter',
                'expire' => $this->expires,
                'code' => $this->format,
            ));
            $cacheKey = $this->getCacheKey($url, $parameters);
            if ($fileCache->isCached($cacheKey, $this->expires)) {
                $response = @unserialize($fileCache->getCached($cacheKey, 0, $this->expires));
                return $response;
            }
        }
        if (!isset($response))
            $response = $this->oAuthRequest($url, 'GET', $parameters);

        if ($this->format === 'json' && $this->decode_json) {
            // @TODO json2arr function so this can be cached 
            // $reponse = TwitterUtil::json2arr($response);
            return json_decode($response);
        } elseif ($this->format === 'xml') {
            $response = TwitterUtil::xml2arr($response);
            if (!is_array($response)) {
                $response = array('error' => xarML('Invalid response from Twitter'));
                return $response;
            }
        }
        // save processing on future requests by caching the array
        if ($this->cached)
            $fileCache->setCached($cacheKey, serialize($response));      

        return $response;
    }

    /**
     * POST wrapper for oAuthRequest.
     */
    function post($url, $parameters = array()) 
    {
        $response = $this->oAuthRequest($url, 'POST', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response);
        } elseif ($this->format === 'xml') {
            $response = TwitterUtil::xml2arr($response);
            if (!is_array($response)) {
                $response = array('error' => xarML('Invalid response from Twitter'));
                return $response;
            }
        }
        return $response;
   }

    /**
     * DELETE wrapper for oAuthReqeust.
     */
    function delete($url, $parameters = array()) 
    {
        $response = $this->oAuthRequest($url, 'DELETE', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response);
        } elseif ($this->format === 'xml') {
            $response = TwitterUtil::xml2arr($response);
            if (!is_array($response)) {
                $response = array('error' => xarML('Invalid response from Twitter'));
                return $response;
            }
        }
        return $response;
    }
  
    /**
     * Generate cacheKey for a GET request
     * Helper function for filesystem caching
    **/
    private function getCacheKey($url, $parameters=array())
    {
        if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
            $url = "{$this->host}{$url}.{$this->format}";
        }
        $keys = array();
        $keys[] = $url;
        if (!empty($this->consumer)) {
            $keys[] = $this->consumer->secret;
            $keys[] = $this->consumer->key;
        }
        if (!empty($this->token)) {
            $keys[] = $this->token->secret;
            $keys[] = $this->token->key;
        }
        $keys += $parameters;
        $cacheKey = md5(serialize($keys));
        return $cacheKey;
    }
}

Class TwitterUtil
{
    /**
     * Util function to transform Twitter XML response to arrays
    **/
    public static function xml2arr($response="")
    {
        if (empty($response)) return "";
        if (!function_exists('simplexml_load_string')) return $response;
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response);
        if (!$xml) return $response;
        $attributes = $xml->attributes();
        $returntype = 'item';
        if (!empty($attributes)) {
            foreach ($attributes as $attributeName => $attributeValue) {
                if ($attributeName == 'type' && $attributeValue == 'array') {
                    $returntype = 'items';
                    break;
                }
            }
        }
        if ($returntype == 'item') {
            $item = self::__xml2arr($xml);
            return $item;
        } else {
            $items = array();
            foreach ($xml as $key => $value) {
                $items[] = self::__xml2arr($value);
            }
            return $items;
        }

    }

    public static function getMeta($filename)
    {
        if (empty($filename)) return "";
        if (!function_exists('simplexml_load_file')) return $filename;
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($filename);
        if (!$xml) return "";
        return self::__xml2arr($xml);    
    }

    /**
     * Recursive function to transform xml responses from twitter to array
     * Based on the function by T CHASSAGNETTE t_chassagnette at yahoo dot fr
     * via http://php.net/manual/en/ref.simplexml.php#52512
    **/
    private static function __xml2arr($xml)
    {
        $fils = 0;
        $tab = false;
        $array = array();
        foreach($xml->children() as $key => $value)
        {   
            $child = self::__xml2arr($value);
       
            //To deal with the attributes
            foreach($value->attributes() as $ak=>$av)
            {
                $child[$ak] = (string)$av;
           
            }
       
            //Let see if the new child is not in the array
            if($tab==false && in_array($key,array_keys($array)))
            {
                //If this element is already in the array we will create an indexed array
                $tmp = $array[$key];
                $array[$key] = NULL;
                $array[$key][] = $tmp;
                $array[$key][] = $child;
                $tab = true;
            }
            elseif($tab == true)
            {
                //Add an element in an existing array
                $array[$key][] = $child;
            }
            else
            {
                //Add a simple element
                if (!empty($child)) {
                    // add converted times and text                    
                    switch ($key) {
                        case 'text';
                            $array['text_transformed'] = self::transform($child);
                        break;
                        case 'created_at';
                            $array['created_at_unix'] = strtotime($child);
                        break;
                        case 'query';
                            $array['query_urlencoded'] = urlencode($child);
                        break;
                    }
                    // convert string representation of bool values
                    switch ($child) {
                        case 'true':
                            $child = true;
                        break;
                        case 'false':
                            $child = false;
                        break;
                    }
    
                }
                $array[$key] = $child;
            }
                   
            $fils++;       
        }
   
   
        if($fils==0)
        {
            return (string)$xml;
        }
   
        return $array;     
    }
    
    public static function json2arr($response="")
    {
        // @TODO...
    }  
    
    /**
     * Util function to transform @replies, #hashtags and urls in Twitter status text
    **/
    public static function transform($text)
    {
        if (!is_string($text)) return $text;
        $text = trim($text);
        if (empty($text)) return "";
        
        // urls
        $text = preg_replace(
            '@(https?://([-\w\.]+)+(/([\w/_\-\.]*(\?\S+)?(#\S+)?)?)?)@',
            '<a href="$1">$1</a>',
        $text);        

        // @replies
        $text = preg_replace(
            '/@(\w+)/',
            '<a href="http://twitter.com/$1">@$1</a>',
        $text);

        // #hashtags 
        $text = preg_replace(
            '/#(\w+)/',
            '<a href="http://search.twitter.com/search?q=%23$1">#$1</a>',
        $text);

        return $text;
    } 

    /**
     * Prep status message for update to Twitter
     *
     * This function sanitizes a status update for posting to Twitter
     *
     * @param string $text status message text to prepare
     * @throws BadParameterException
     * @returns string text prepped for sending to Twitter
    **/
    public static function prepstatus($text)
    {
      
        if (!is_string($text))
            throw new BadParameterException('text');

        $limit = 140;
        $text = strip_tags($text);  
        $text = trim($text);
        if (empty($text)) return "";

        // replace &#38;s (from xml urls)
        $text = str_replace('&amp;', '&', $text);
        if (strlen($text) <= $limit) return $text; // string within limits 

        // try reducing length by replacing urls with tinyurls      
        preg_match_all("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", $text, $matches);
        // replace with tinyurls
        if (!empty($matches[0])) {
            foreach ($matches[0] as $url) {
                $tinyurl = self::encodetinyurl($url);
                if (!$tinyurl) continue;
                $text = preg_replace("!{$url}!", " {$tinyurl}", $text);
                // see if replacing this url took us below the limit                
                if (strlen($text) <= $limit) break;
            }
        }
        if (strlen($text) <= $limit) return $text; // string within limits      
        
        // try reducing the length of the text by removing words 
        $words = explode(' ', $text);
        $length = strlen($text);
        // start at the end and work backwards
        $words = array_reverse($words);
        foreach ($words as $key => $word) {
            // preserve urls, hashtags, and @replies in the text          
            if (preg_match("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", $word) ||
                preg_match("/(?:^|\W)\#([a-zA-Z0-9\-_\.+:=]+\w)(?:\W|$)/is", $word) ||
                preg_match("/(?:^|\W|#)@(\w+)/is", $word)) continue;
            // see if removing this word took us below the limit
            $length -= strlen($word)+1;
            if ($length <= $limit - 4) {
                // replace this word with concatenation indicator
                $words[$key] = '...';
                break;
            }
            // still too long, remove this word         
            unset($words[$key]);
        }
        $words = array_reverse($words);
        $text = join(' ', $words);
        if (strlen($text) <= $limit) return $text; // string within limits 
                
        // if we're still over the limit, the text either has no urls, hashtags or @replies,
        // or contains nothing but, either way no option left but to slice it indiscriminately...
        $text = substr($text, 0, $limit - 3);
        // take off non-word characters + part of word at end
        $text = preg_replace('/[^a-z0-9_\-\?\.]+[a-z0-9_\-\?\.]*\z/i', '', $text);
        // append concatenation indicator
        $text .= ' ...';        
          
        return $text;
              
    }
    /**
     * Encode url as tinyurl 
     *
     * @param string $url url to make tinyurl
     * @throws none
     * @return string tinyurl
    **/
    public static function encodetinyurl($url)
    {
        if (empty($url)) return false;
        // replace &#38;s (from xml urls)
        $url = str_replace('&amp;', '&', $url);
        if (function_exists('curl_init')) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.urlencode($url));
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $tinyurl = curl_exec($ch);
            curl_close($ch);
        }
        if (empty($tinyurl) || strlen($tinyurl) > strlen($url)) return $url;
        return $tinyurl;    
    }

}


?>