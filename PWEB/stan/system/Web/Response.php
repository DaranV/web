<?php
namespace Web;

class Response
{
    /**
     * Init Headers
     * @var array
     */
    private static $headers = array();

    /**
     * Default Http Status
     * @var array
     */
    public static $status = array(100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => '(Unused)', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported');

    /**
     * Add Http Status
     *
     * @param int $code
     */

    public static function addStatus(int $code)
    {
        if (isset(self::$status[$code])) {
            $httpProtocol = $_SERVER['SERVER_PROTOCOL'];
            
            self::addHeader("$httpProtocol $code " . self::$status[$code]);
        } //isset(self::$status[$code])
    }

    /**
     * Add Header
     *
     * @param string$header
     */

    public static function addHeader(string $header)
    {
        self::$headers[] = $header;
    }

    /**
     * Add Headers
     *
     * @param array $headers
     */

    public static function addHeaders(array $headers = array())
    {
        self::$headers = array_merge(self::$headers, $headers);
    }

    /**
     * Send Headers
     */

    public static function sendHeaders()
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header, true);
            } //self::$headers as $header
        } //!headers_sent()
    }

    /**
     * Read File
     *
     * @param string $filePath
     * @return bool
     */

    public static function serveFile(string $filePath) : bool
    {
        $httpProtocol = $_SERVER['SERVER_PROTOCOL'];
        
        $expires = 60 * 60 * 24 * 365;
        
        if (!file_exists($filePath)) {
            header("$httpProtocol 404 Not Found");
            
            return false;
        } //!file_exists($filePath)
        else if (!is_readable($filePath)) {
            header("$httpProtocol 403 Forbidden");
            
            return false;
        } //!is_readable($filePath)
        
        $finfo = \finfo_open(FILEINFO_MIME_TYPE);
        
        $contentType = \finfo_file($finfo, $filePath);
        
        \finfo_close($finfo);
        
        switch ($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
            case 'css':
                $contentType = 'text/css';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            default:
                break;
        } //$fileExt = pathinfo($filePath, PATHINFO_EXTENSION)
        
        $lastModified = filemtime($filePath);
        
        $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
        
        if (ob_get_level()) {
            ob_end_clean();
        } //ob_get_level()
        
        header('Access-Control-Allow-Origin: *');
        header('Content-type: ' . $contentType);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        header('Cache-Control: max-age=' . $expires);
        
        if (@strtotime($ifModifiedSince) == $lastModified) {
            header("$httpProtocol 304 Not Modified");
            
            return true;
        } //@strtotime($ifModifiedSince) == $lastModified
        
        header("$httpProtocol 200 OK");
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        
        return true;
    }
}
