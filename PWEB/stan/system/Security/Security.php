<?php
namespace Security;

use Exception;

/**
 * Class Security
 * @package Security
 * @authour internet
 */

class Security
{

    /**
     * Bad FileName Chars
     * @var array
     */
    public $filename_bad_chars = array('../', '<!--', '-->', '<', '>', "'", '"', '&', '$', '#', '{', '}', '[', ']', '=', ';', '?', '%20', '%22', '%3c', '%253c', '%3e', '%0e', '%28', '%29', '%2528', '%26', '%24', '%3f', '%3b', '%3d');

    /**
     * Default Charset
     * @var string
     */
    public $charset = 'UTF-8';

    /**
     * Xss Hash
     * @var string
     */
    protected $_xss_hash;

    /**
     * Never Allowed String
     * @var array
     */
    protected $_never_allowed_str = array('document.cookie' => '[removed]', 'document.write' => '[removed]', '.parentNode' => '[removed]', '.innerHTML' => '[removed]', '-moz-binding' => '[removed]', '<!--' => '&lt;!--', '-->' => '--&gt;', '<![CDATA[' => '&lt;![CDATA[', '<comment>' => '&lt;comment&gt;');

    /**
     * Never Allowed Regex
     * @var array
     */
    protected $_never_allowed_regex = array('javascript\s*:', '(document|(document\.)?window)\.(location|on\w*)', 'expression\s*(\(|&\#40;)', 'vbscript\s*:', 'wscript\s*:', 'jscript\s*:', 'vbs\s*:', 'Redirect\s+30\d', "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?");

    /**
     * Security constructor.
     */
    public function __construct(){}

    /**
     * Xss Clean Method
     *
     * @param string|array $str
     * @param bool $is_image
     * @return array|bool|mixed|string
     */

    public function xss_clean($str, bool $is_image = false)
    {
        if (is_array($str)) {
            foreach($str as $key){
                $str[$key] = $this->xss_clean($str[$key]);
            }

            /*while (list($key) = each($str)) {
                $str[$key] = $this->xss_clean($str[$key]);
            }*/
            
            return $str;
        } //is_array($str)
        
        $str = $this->remove_invisible_characters($str);
        
        do {
            $str = rawurldecode($str);
        } while (preg_match('/%[0-9a-f]{2,}/i', $str));
        
        $str = preg_replace_callback("/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si", array(
            $this,
            '_convert_attribute'
        ), $str);
        $str = preg_replace_callback('/<\w+.*/si', array(
            $this,
            '_decode_entity'
        ), $str);
        
        $str = $this->remove_invisible_characters($str);
        
        $str = str_replace("\t", ' ', $str);
        
        $converted_string = $str;
        
        $str = $this->_do_never_allowed($str);
        
        if ($is_image === true) {
            $str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
        } //$is_image === TRUE
        else {
            $str = str_replace(array(
                '<?',
                '?' . '>'
            ), array(
                '&lt;?',
                '?&gt;'
            ), $str);
        }
        
        $words = array(
            'javascript',
            'expression',
            'vbscript',
            'jscript',
            'wscript',
            'vbs',
            'script',
            'base64',
            'applet',
            'alert',
            'document',
            'write',
            'cookie',
            'window',
            'confirm',
            'prompt',
            'eval'
        );
        
        foreach ($words as $word) {
            $word = implode('\s*', str_split($word)) . '\s*';
            $str  = preg_replace_callback('#(' . substr($word, 0, -3) . ')(\W)#is', array(
                $this,
                '_compact_exploded_words'
            ), $str);
        } //$words as $word
        do {
            $original = $str;
            if (preg_match('/<a/i', $str)) {
                $str = preg_replace_callback('#<a[^a-z0-9>]+([^>]*?)(?:>|$)#si', array(
                    $this,
                    '_js_link_removal'
                ), $str);
            } //preg_match('/<a/i', $str)
            
            if (preg_match('/<img/i', $str)) {
                $str = preg_replace_callback('#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si', array(
                    $this,
                    '_js_img_removal'
                ), $str);
            } //preg_match('/<img/i', $str)
            
            if (preg_match('/script|xss/i', $str)) {
                $str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
            } //preg_match('/script|xss/i', $str)
        } while ($original !== $str);
        unset($original);
        
        $pattern = '#' . '<((?<slash>/*\s*)(?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)' . '[^\s\042\047a-z0-9>/=]*' . '(?<attributes>(?:[\s\042\047/=]*' . '[^\s\042\047>/=]+' . '(?:\s*=' . '(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))' . ')?' . ')*)' . '[^>]*)(?<closeTag>\>)?#isS';
        
        do {
            $old_str = $str;
            $str     = preg_replace_callback($pattern, array(
                $this,
                '_sanitize_naughty_html'
            ), $str);
        } while ($old_str !== $str);
        unset($old_str);
        
        $str = preg_replace('#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', '\\1\\2&#40;\\3&#41;', $str);
        
        $str = $this->_do_never_allowed($str);
        
        if ($is_image === true) {
            return ($str === $converted_string);
        } //$is_image === TRUE
        
        return $str;
    }

    /**
     * Set Xss Hash
     *
     * @return string
     */

    public function xss_hash() : string
    {
        if ($this->_xss_hash === NULL) {
            $rand            = $this->get_random_bytes(16);
            $this->_xss_hash = ($rand === FALSE) ? md5(uniqid(mt_rand(), TRUE)) : bin2hex($rand);
        } //$this->_xss_hash === NULL
        
        return $this->_xss_hash;
    }

    /**
     * Get Random Bytes
     *
     * @param int $length
     * @return bool|string
     */

    public function get_random_bytes(int $length)
    {
        if (empty($length) OR !ctype_digit((string) $length)) {
            return FALSE;
        } //empty($length) OR !ctype_digit((string) $length)
        
        if (function_exists('random_bytes')) {
            try {
                return random_bytes((int) $length);
            }
            catch (Exception $e) {
                return FALSE;
            }
        } //function_exists('random_bytes')
        
        if (defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== FALSE) {
            return $output;
        } //defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== FALSE
        
        
        if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE) {
            is_php('5.4') && stream_set_chunk_size($fp, $length);
            $output = fread($fp, $length);
            fclose($fp);
            if ($output !== FALSE) {
                return $output;
            } //$output !== FALSE
        } //is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE
        
        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        } //function_exists('openssl_random_pseudo_bytes')
        
        return FALSE;
    }


    /**
     * Entity Decode Method
     *
     * @param string $str
     * @param null|string $charset
     * @return mixed|string
     */

    public function entity_decode(string $str, string $charset = NULL)
    {
        if (strpos($str, '&') === FALSE) {
            return $str;
        } //strpos($str, '&') === FALSE
        
        static $_entities;
        
        isset($charset) OR $charset = $this->charset;
        $flag = is_php('5.4') ? ENT_COMPAT | ENT_HTML5 : ENT_COMPAT;
        
        do {
            $str_compare = $str;
            
            if (preg_match_all('/&[a-z]{2,}(?![a-z;])/i', $str, $matches)) {
                if (!isset($_entities)) {
                    $_entities = array_map('strtolower', is_php('5.3.4') ? get_html_translation_table(HTML_ENTITIES, $flag, $charset) : get_html_translation_table(HTML_ENTITIES, $flag));
                    
                    if ($flag === ENT_COMPAT) {
                        $_entities[':']  = '&colon;';
                        $_entities['(']  = '&lpar;';
                        $_entities[')']  = '&rpar;';
                        $_entities["\n"] = '&newline;';
                        $_entities["\t"] = '&tab;';
                    } //$flag === ENT_COMPAT
                } //!isset($_entities)
                
                $replace = array();
                $matches = array_unique(array_map('strtolower', $matches[0]));
                foreach ($matches as &$match) {
                    if (($char = array_search($match . ';', $_entities, TRUE)) !== FALSE) {
                        $replace[$match] = $char;
                    } //($char = array_search($match . ';', $_entities, TRUE)) !== FALSE
                } //$matches as &$match
                
                $str = str_ireplace(array_keys($replace), array_values($replace), $str);
            } //preg_match_all('/&[a-z]{2,}(?![a-z;])/i', $str, $matches)
            
            $str = html_entity_decode(preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', $str), $flag, $charset);
        } while ($str_compare !== $str);
        return $str;
    }

    /**
     * Sanitize File Name Method
     *
     * @param string $str
     * @param bool $relative_path
     * @return string
     */
    public function sanitize_filename(string $str, bool $relative_path = FALSE) : string
    {
        $bad = $this->filename_bad_chars;
        
        if (!$relative_path) {
            $bad[] = './';
            $bad[] = '/';
        } //!$relative_path
        
        $str = $this->remove_invisible_characters($str, FALSE);
        
        do {
            $old = $str;
            $str = str_replace($bad, '', $str);
        } while ($old !== $str);
        
        return stripslashes($str);
    }

    /**
     * Remove Invisible Characters Method
     *
     * @param string $str
     * @param bool $url_encoded
     * @return mixed
     */
    function remove_invisible_characters(string $str, bool $url_encoded = TRUE)
    {
        $non_displayables = array();
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';
            $non_displayables[] = '/%1[0-9a-f]/';
        } //$url_encoded
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);
        return $str;
    }

    /**
     * Compact Exploded Words Handler
     *
     * @param array $matches
     * @return string
     */
    protected function _compact_exploded_words(array $matches) : string
    {
        return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
    }

    /**
     * Sanitize Naughty Html Method
     *
     * @param array $matches
     * @return mixed|string
     */
    protected function _sanitize_naughty_html(array $matches)
    {
        static $naughty_tags = array('alert', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound', 'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer', 'iframe', 'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object', 'plaintext', 'style', 'script', 'textarea', 'title', 'math', 'video', 'svg', 'xml', 'xss');
        
        static $evil_attributes = array('on\w+', 'style', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime');
        
        if (empty($matches['closeTag'])) {
            return '&lt;' . $matches[1];
        } //empty($matches['closeTag'])
        
        elseif (in_array(strtolower($matches['tagName']), $naughty_tags, TRUE)) {
            return '&lt;' . $matches[1] . '&gt;';
        } //in_array(strtolower($matches['tagName']), $naughty_tags, TRUE)
            elseif (isset($matches['attributes'])) {
            $attributes = array();
            
            $attributes_pattern = '#' . '(?<name>[^\s\042\047>/=]+)' . '(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))' . '#i';
            
            $is_evil_pattern = '#^(' . implode('|', $evil_attributes) . ')$#i';
            
            do {
                $matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);
                
                if (!preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE)) {
                    break;
                } //!preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE)
                
                if (preg_match($is_evil_pattern, $attribute['name'][0]) OR (trim($attribute['value'][0]) === '')) {
                    $attributes[] = 'xss=removed';
                } //preg_match($is_evil_pattern, $attribute['name'][0]) OR (trim($attribute['value'][0]) === '')
                else {
                    $attributes[] = $attribute[0][0];
                }
                
                $matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
            } while ($matches['attributes'] !== '');
            
            $attributes = empty($attributes) ? '' : ' ' . implode(' ', $attributes);
            return '<' . $matches['slash'] . $matches['tagName'] . $attributes . '>';
        } //isset($matches['attributes'])
        
        return $matches[0];
    }

    /**
     * JS Link Removal Method
     *
     * @param string $match
     * @return string
     */
    protected function _js_link_removal(string $match) : string
    {
        return str_replace($match[1], preg_replace('#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si', '', $this->_filter_attributes($match[1])), $match[0]);
    }

    /**
     * JS Img Removal Method
     *
     * @param string $match
     * @return string
     */
    protected function _js_img_removal(string $match) : string
    {
        return str_replace($match[1], preg_replace('#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si', '', $this->_filter_attributes($match[1])), $match[0]);
    }

    /**
     * Convert Atrribute Handler
     *
     * @param $match
     * @return mixed
     */

    protected function _convert_attribute($match)
    {
        return str_replace(array(
            '>',
            '<',
            '\\'
        ), array(
            '&gt;',
            '&lt;',
            '\\\\'
        ), $match[0]);
    }

    /**
     * Filter Attributes
     *
     * @param string $str
     * @return string
     */
    protected function _filter_attributes(string $str) : string
    {
        $out = '';
        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace('#/\*.*?\*/#s', '', $match);
            } //$matches[0] as $match
        } //preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)
        
        return $out;
    }

    /**
     * Decode Entity Handler
     *
     * @param $match
     * @return string
     */

    protected function _decode_entity($match) : string
    {
        $match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $this->xss_hash() . '\\1=\\2', $match[0]);
        
        return str_replace($this->xss_hash(), '&', $this->entity_decode($match, $this->charset));
    }

    /**
     * Do Never Allowed Check
     *
     * @param string $str
     * @return string
     */

    protected function _do_never_allowed(string $str) : string
    {
        $str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);
        
        foreach ($this->_never_allowed_regex as $regex) {
            $str = preg_replace('#' . $regex . '#is', '[removed]', $str);
        } //$this->_never_allowed_regex as $regex
        
        return $str;
    }
}
