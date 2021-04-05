<?php
namespace Security;

use Security\Security as Security;

/**
 * Class Upload
 * @package Security
 * @author internet
 */

class Upload
{

    /**
     * @var int
     */
    public $max_size = 0;

    /**
     * @var int
     */
    public $max_width = 0;

    /**
     * @var int
     */
    public $max_height = 0;

    /**
     * @var int
     */
    public $max_filename = 0;

    /**
     * @var string|array
     */
    public $allowed_types = "";

    /**
     * @var string
     */
    public $file_temp = "";

    /**
     * @var string
     */
    public $file_name = "";

    /**
     * @var string
     */
    public $orig_name = "";

    /**
     * @var string
     */
    public $file_type = "";

    /**
     * @var string
     */
    public $file_size = "";

    /**
     * @var string
     */
    public $file_ext = "";

    /**
     * @var string
     */
    public $upload_path = "";

    /**
     * @var bool
     */
    public $overwrite = FALSE;

    /**
     * @var bool
     */
    public $encrypt_name = FALSE;

    /**
     * @var bool
     */
    public $is_image = FALSE;

    /**
     * @var string
     */
    public $image_width = '';

    /**
     * @var string
     */
    public $image_height = '';

    /**
     * @var string
     */
    public $image_type = '';

    /**
     * @var string
     */
    public $image_size_str = '';

    /**
     * @var array
     */
    public $error_msg = array();

    /**
     * @var array
     */
    public $mimes = array();

    /**
     * @var bool
     */
    public $remove_spaces = TRUE;

    /**
     * @var bool
     */
    public $xss_clean = FALSE;

    /**
     * @var string
     */
    public $temp_prefix = "temp_file_";

    /**
     * @var string
     */
    public $client_name = '';

    /**
     * @var string
     */
    protected $_file_name_override = '';

    /**
     * @var \Security\Security
     */
    protected $security;

    /**
     * Upload constructor.
     * @param array $props
     */
    public function __construct($props = array())
    {
        if (count($props) > 0) {
            $this->initialize($props);
        } //count($props) > 0
        $this->security = new Security();
    }

    /**
     * Initialize Method
     *
     * @param array $config
     */
    public function initialize($config = array())
    {
        $defaults = array(
            'max_size' => 0,
            'max_width' => 0,
            'max_height' => 0,
            'max_filename' => 0,
            'allowed_types' => "",
            'file_temp' => "",
            'file_name' => "",
            'orig_name' => "",
            'file_type' => "",
            'file_size' => "",
            'file_ext' => "",
            'upload_path' => "",
            'overwrite' => FALSE,
            'encrypt_name' => FALSE,
            'is_image' => FALSE,
            'image_width' => '',
            'image_height' => '',
            'image_type' => '',
            'image_size_str' => '',
            'error_msg' => array(),
            'mimes' => array(),
            'remove_spaces' => TRUE,
            'xss_clean' => FALSE,
            'temp_prefix' => "temp_file_",
            'client_name' => ''
        );


        foreach ($defaults as $key => $val) {
            if (isset($config[$key])) {
                $method = 'set_' . $key;
                if (method_exists($this, $method)) {
                    $this->$method($config[$key]);
                } //method_exists($this, $method)
                else {
                    $this->$key = $config[$key];
                }
            } //isset($config[$key])
            else {
                $this->$key = $val;
            }
        } //$defaults as $key => $val
        $this->_file_name_override = $this->file_name;
    }

    /**
     * Do Upload Method
     *
     * @param string $field
     * @return bool
     */
    public function do_upload($field = 'userfile')
    {

        if (!isset($_FILES[$field])) {
            $this->set_error('upload_no_file_selected');
            return FALSE;
        } //!isset($_FILES[$field])

        if (!$this->validate_upload_path()) {
            return FALSE;
        } //!$this->validate_upload_path()

        if (!is_uploaded_file($_FILES[$field]['tmp_name'])) {
            $error = (!isset($_FILES[$field]['error'])) ? 4 : $_FILES[$field]['error'];

            switch ($error) {
                case 1:
                    $this->set_error('upload_file_exceeds_limit');
                    break;
                case 2:
                    $this->set_error('upload_file_exceeds_form_limit');
                    break;
                case 3:
                    $this->set_error('upload_file_partial');
                    break;
                case 4:
                    $this->set_error('upload_no_file_selected');
                    break;
                case 6:
                    $this->set_error('upload_no_temp_directory');
                    break;
                case 7:
                    $this->set_error('upload_unable_to_write_file');
                    break;
                case 8:
                    $this->set_error('upload_stopped_by_extension');
                    break;
                default:
                    $this->set_error('upload_no_file_selected');
                    break;
            } //$error

            return FALSE;
        } //!is_uploaded_file($_FILES[$field]['tmp_name'])


        $this->file_temp = $_FILES[$field]['tmp_name'];
        $this->file_size = $_FILES[$field]['size'];
        $this->_file_mime_type($_FILES[$field]);
        $this->file_type   = preg_replace("/^(.+?);.*$/", "\\1", $this->file_type);
        $this->file_type   = strtolower(trim(stripslashes($this->file_type), '"'));
        $this->file_name   = $this->_prep_filename($_FILES[$field]['name']);
        $this->file_ext    = $this->get_extension($this->file_name);
        $this->client_name = $this->file_name;

        if (!$this->is_allowed_filetype()) {
            $this->set_error('upload_invalid_filetype');
            return FALSE;
        } //!$this->is_allowed_filetype()

        if ($this->_file_name_override != '') {
            $this->file_name = $this->_prep_filename($this->_file_name_override);

            if (strpos($this->_file_name_override, '.') === FALSE) {
                $this->file_name .= $this->file_ext;
            } //strpos($this->_file_name_override, '.') === FALSE

            else {
                $this->file_ext = $this->get_extension($this->_file_name_override);
            }

            if (!$this->is_allowed_filetype(TRUE)) {
                $this->set_error('upload_invalid_filetype');
                return FALSE;
            } //!$this->is_allowed_filetype(TRUE)
        } //$this->_file_name_override != ''

        if (!$this->is_allowed_filesize()) {
            $this->set_error('upload_invalid_filesize');
            return FALSE;
        } //!$this->is_allowed_filesize()

        if (!$this->is_allowed_dimensions()) {
            $this->set_error('upload_invalid_dimensions');
            return FALSE;
        } //!$this->is_allowed_dimensions()


        $this->file_name = $this->security->sanitize_filename($this->file_name);

        if ($this->max_filename > 0) {
            $this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
        } //$this->max_filename > 0

        if ($this->remove_spaces == TRUE) {
            $this->file_name = preg_replace("/\s+/", "_", $this->file_name);
        } //$this->remove_spaces == TRUE

        $this->orig_name = $this->file_name;

        if ($this->overwrite == FALSE) {
            $this->file_name = $this->set_filename($this->upload_path, $this->file_name);

            if ($this->file_name === FALSE) {
                return FALSE;
            } //$this->file_name === FALSE
        } //$this->overwrite == FALSE

        if ($this->xss_clean) {
            if ($this->do_xss_clean() === FALSE) {
                $this->set_error('upload_unable_to_write_file');
                return FALSE;
            } //$this->do_xss_clean() === FALSE
        } //$this->xss_clean

        if (!@copy($this->file_temp, $this->upload_path . $this->file_name)) {
            if (!@move_uploaded_file($this->file_temp, $this->upload_path . $this->file_name)) {
                $this->set_error('upload_destination_error');
                return FALSE;
            } //!@move_uploaded_file($this->file_temp, $this->upload_path . $this->file_name)
        } //!@copy($this->file_temp, $this->upload_path . $this->file_name)

        $this->set_image_properties($this->upload_path . $this->file_name);

        return TRUE;
    }

    /**
     * Get Datas
     *
     * @return array
     */
    public function data()
    {
        return array(
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'file_path' => $this->upload_path,
            'full_path' => $this->upload_path . $this->file_name,
            'raw_name' => str_replace($this->file_ext, '', $this->file_name),
            'orig_name' => $this->orig_name,
            'client_name' => $this->client_name,
            'file_ext' => $this->file_ext,
            'file_size' => $this->file_size,
            'is_image' => $this->is_image(),
            'image_width' => $this->image_width,
            'image_height' => $this->image_height,
            'image_type' => $this->image_type,
            'image_size_str' => $this->image_size_str
        );
    }

    /**
     * Set Upload Path
     *
     * @param $path
     */
    public function set_upload_path($path)
    {
        $this->upload_path = rtrim($path, '/') . '/';
    }

    /**
     * Set FileName
     *
     * @param $path
     * @param $filename
     * @return bool|mixed|string
     */
    public function set_filename($path, $filename)
    {
        if ($this->encrypt_name == TRUE) {
            mt_srand();
            $filename = md5(uniqid(mt_rand())) . $this->file_ext;
        } //$this->encrypt_name == TRUE

        if (!file_exists($path . $filename)) {
            return $filename;
        } //!file_exists($path . $filename)

        $filename = str_replace($this->file_ext, '', $filename);

        $new_filename = '';
        for ($i = 1; $i < 100; $i++) {
            if (!file_exists($path . $filename . $i . $this->file_ext)) {
                $new_filename = $filename . $i . $this->file_ext;
                break;
            } //!file_exists($path . $filename . $i . $this->file_ext)
        } //$i = 1; $i < 100; $i++

        if ($new_filename == '') {
            $this->set_error('upload_bad_filename');
            return FALSE;
        } //$new_filename == ''
        else {
            return $new_filename;
        }
    }

    /**
     * Set Max FileSize
     *
     * @param int $n
     */
    public function set_max_filesize(int $n)
    {
        $this->max_size = ((int) $n < 0) ? 0 : (int) $n;
    }

    /**
     * Set Max FileName
     *
     * @param int $n
     */
    public function set_max_filename(int $n)
    {
        $this->max_filename = ((int) $n < 0) ? 0 : (int) $n;
    }

    /**
     * Set Max Width
     *
     * @param int $n
     */
    public function set_max_width(int $n)
    {
        $this->max_width = ((int) $n < 0) ? 0 : (int) $n;
    }

    /**
     * Set Max Height
     *
     * @param int $n
     */
    public function set_max_height(int $n)
    {
        $this->max_height = ((int) $n < 0) ? 0 : (int) $n;
    }

    /**
     * Set Allowed Types
     *
     * @param string|array $types
     */
    public function set_allowed_types($types)
    {
        if (!is_array($types) && $types == '*') {
            $this->allowed_types = '*';
            return;
        } //!is_array($types) && $types == '*'
        $this->allowed_types = explode('|', $types);
    }

    /**
     * Set Image Properties
     *
     * @param string $path
     */
    public function set_image_properties(string $path = '')
    {
        if (!$this->is_image()) {
            return;
        } //!$this->is_image()

        if (function_exists('getimagesize')) {
            if (FALSE !== ($D = @getimagesize($path))) {
                $types = array(
                    1 => 'gif',
                    2 => 'jpeg',
                    3 => 'png'
                );

                $this->image_width    = $D['0'];
                $this->image_height   = $D['1'];
                $this->image_type     = (!isset($types[$D['2']])) ? 'unknown' : $types[$D['2']];
                $this->image_size_str = $D['3'];
            } //FALSE !== ($D = @getimagesize($path))
        } //function_exists('getimagesize')
    }

    /**
     * Set Xss Clean State
     *
     * @param bool $flag
     */
    public function set_xss_clean($flag = FALSE)
    {
        $this->xss_clean = ($flag == TRUE) ? TRUE : FALSE;
    }

    /**
     * Is an Image
     *
     * @return bool
     */
    public function is_image()
    {

        $png_mimes  = array(
            'image/x-png'
        );
        $jpeg_mimes = array(
            'image/jpg',
            'image/jpe',
            'image/jpeg',
            'image/pjpeg'
        );

        if (in_array($this->file_type, $png_mimes)) {
            $this->file_type = 'image/png';
        } //in_array($this->file_type, $png_mimes)

        if (in_array($this->file_type, $jpeg_mimes)) {
            $this->file_type = 'image/jpeg';
        } //in_array($this->file_type, $jpeg_mimes)

        $img_mimes = array(
            'image/gif',
            'image/jpeg',
            'image/png'
        );

        return (in_array($this->file_type, $img_mimes, TRUE)) ? TRUE : FALSE;
    }

    /**
     * Allowed FileType
     *
     * @param bool $ignore_mime
     * @return bool
     */
    public function is_allowed_filetype($ignore_mime = FALSE)
    {
        if ($this->allowed_types == '*') {
            return TRUE;
        } //$this->allowed_types == '*'

        if (count($this->allowed_types) == 0 OR !is_array($this->allowed_types)) {
            $this->set_error('upload_no_file_types');
            return FALSE;
        } //count($this->allowed_types) == 0 OR !is_array($this->allowed_types)

        $ext = strtolower(ltrim($this->file_ext, '.'));

        if (!in_array($ext, $this->allowed_types)) {
            return FALSE;
        } //!in_array($ext, $this->allowed_types)

        $image_types = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'jpe'
        );

        if (in_array($ext, $image_types)) {
            if (getimagesize($this->file_temp) === FALSE) {
                return FALSE;
            } //getimagesize($this->file_temp) === FALSE
        } //in_array($ext, $image_types)

        if ($ignore_mime === TRUE) {
            return TRUE;
        } //$ignore_mime === TRUE

        $mime = $this->mimes_types($ext);

        if (is_array($mime)) {
            if (in_array($this->file_type, $mime, TRUE)) {
                return TRUE;
            } //in_array($this->file_type, $mime, TRUE)
        } //is_array($mime)
        elseif ($mime == $this->file_type) {
            return TRUE;
        } //$mime == $this->file_type

        return FALSE;
    }

    /**
     * Allowed FileSize
     *
     * @return bool
     */
    public function is_allowed_filesize()
    {
        if ($this->max_size != 0 AND $this->file_size > $this->max_size) {
            return FALSE;
        } //$this->max_size != 0 AND $this->file_size > $this->max_size
        else {
            return TRUE;
        }
    }

    /**
     * Allowed Dimensions
     *
     * @return bool
     */
    public function is_allowed_dimensions()
    {
        if (!$this->is_image()) {
            return TRUE;
        } //!$this->is_image()

        if (function_exists('getimagesize')) {
            $D = @getimagesize($this->file_temp);

            if ($this->max_width > 0 AND $D['0'] > $this->max_width) {
                return FALSE;
            } //$this->max_width > 0 AND $D['0'] > $this->max_width

            if ($this->max_height > 0 AND $D['1'] > $this->max_height) {
                return FALSE;
            } //$this->max_height > 0 AND $D['1'] > $this->max_height

            return TRUE;
        } //function_exists('getimagesize')

        return TRUE;
    }

    /**
     * Is a Valid Upload Path
     *
     * @return bool
     */
    public function validate_upload_path()
    {
        if ($this->upload_path == '') {
            $this->set_error('upload_no_filepath');
            return FALSE;
        } //$this->upload_path == ''

        if (function_exists('realpath') AND @realpath($this->upload_path) !== FALSE) {
            $this->upload_path = str_replace("\\", "/", realpath($this->upload_path));
        } //function_exists('realpath') AND @realpath($this->upload_path) !== FALSE

        if (!@is_dir($this->upload_path)) {
            $this->set_error('upload_no_filepath');
            return FALSE;
        } //!@is_dir($this->upload_path)

        if (!$this->is_really_writable($this->upload_path)) {
            $this->set_error('upload_not_writable');
            return FALSE;
        } //!$this->is_really_writable($this->upload_path)

        $this->upload_path = preg_replace("/(.+?)\/*$/", "\\1/", $this->upload_path);
        return TRUE;
    }

    /**
     * Get Extension
     *
     * @param string $filename
     * @return string
     */
    public function get_extension(string $filename)
    {
        $x = explode('.', $filename);
        return '.' . end($x);
    }

    /**
     * Clean FileName
     *
     * @param string $filename
     * @return string
     */
    public function clean_file_name(string $filename)
    {
        return $this->security->sanitize_filename($filename);
    }

    /**
     * Limit FileName Length
     *
     * @param string $filename
     * @param int $length
     * @return string
     */
    public function limit_filename_length(string $filename, int $length)
    {
        if (strlen($filename) < $length) {
            return $filename;
        } //strlen($filename) < $length

        $ext = '';
        if (strpos($filename, '.') !== FALSE) {
            $parts    = explode('.', $filename);
            $ext      = '.' . array_pop($parts);
            $filename = implode('.', $parts);
        } //strpos($filename, '.') !== FALSE

        return substr($filename, 0, ($length - strlen($ext))) . $ext;
    }

    /**
     * Do Xss Clean Method
     *
     * @return array|bool|mixed|string
     */
    public function do_xss_clean()
    {
        $file = $this->file_temp;

        if (filesize($file) == 0) {
            return FALSE;
        } //filesize($file) == 0

        if (function_exists('memory_get_usage') && memory_get_usage() && ini_get('memory_limit') != '') {
            $current = intval(ini_get('memory_limit')) * 1024 * 1024;

            $new_memory = number_format(ceil(filesize($file) + $current), 0, '.', '');

            ini_set('memory_limit', intval($new_memory));
        } //function_exists('memory_get_usage') && memory_get_usage() && ini_get('memory_limit') != ''

        if (function_exists('getimagesize') && @getimagesize($file) !== FALSE) {
            if (($file = @fopen($file, 'rb')) === FALSE) {
                return FALSE;
            } //($file = @fopen($file, 'rb')) === FALSE

            $opening_bytes = fread($file, 256);
            fclose($file);

            if (!preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', $opening_bytes)) {
                return TRUE;
            } //!preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', $opening_bytes)
            else {
                return FALSE;
            }
        } //function_exists('getimagesize') && @getimagesize($file) !== FALSE

        if (($data = @file_get_contents($file)) === FALSE) {
            return FALSE;
        } //($data = @file_get_contents($file)) === FALSE


        return $this->security->xss_clean($data, TRUE);
    }

    /**
     * Set Error Method
     *
     * @param string|array $msg
     */
    public function set_error($msg)
    {

        if (is_array($msg)) {
            foreach ($msg as $val) {
                $this->error_msg[] = $msg;
            } //$msg as $val
        } //is_array($msg)
        else {
            $this->error_msg[] = $msg;
        }
    }

    /**
     * Display Errors
     *
     * @param string $open
     * @param string $close
     * @return string
     */
    public function display_errors(string $open = '<p>', string $close = '</p>')
    {
        $str = '';
        foreach ($this->error_msg as $val) {
            $str .= $open . $val . $close;
        } //$this->error_msg as $val

        return $str;
    }

    /**
     * Get Mimes Types
     *
     * @param string $mime
     * @return bool|mixed
     */
    public function mimes_types(string $mime)
    {
        $this->mimes = $this->mimes_array();
        return (!isset($this->mimes[$mime])) ? FALSE : $this->mimes[$mime];
    }

    /**
     * Array of Mimes
     *
     * @return array
     */
    function mimes_array()
    {

        return array(
            'hqx' => array(
                'application/mac-binhex40',
                'application/mac-binhex',
                'application/x-binhex40',
                'application/x-mac-binhex40'
            ),
            'cpt' => 'application/mac-compactpro',
            'csv' => array(
                'text/x-comma-separated-values',
                'text/comma-separated-values',
                'application/octet-stream',
                'application/vnd.ms-excel',
                'application/x-csv',
                'text/x-csv',
                'text/csv',
                'application/csv',
                'application/excel',
                'application/vnd.msexcel',
                'text/plain'
            ),
            'bin' => array(
                'application/macbinary',
                'application/mac-binary',
                'application/octet-stream',
                'application/x-binary',
                'application/x-macbinary'
            ),
            'dms' => 'application/octet-stream',
            'lha' => 'application/octet-stream',
            'lzh' => 'application/octet-stream',
            'exe' => array(
                'application/octet-stream',
                'application/x-msdownload'
            ),
            'class' => 'application/octet-stream',
            'psd' => array(
                'application/x-photoshop',
                'image/vnd.adobe.photoshop'
            ),
            'so' => 'application/octet-stream',
            'sea' => 'application/octet-stream',
            'dll' => 'application/octet-stream',
            'oda' => 'application/oda',
            'pdf' => array(
                'application/pdf',
                'application/force-download',
                'application/x-download',
                'binary/octet-stream'
            ),
            'ai' => array(
                'application/pdf',
                'application/postscript'
            ),
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'mif' => 'application/vnd.mif',
            'xls' => array(
                'application/vnd.ms-excel',
                'application/msexcel',
                'application/x-msexcel',
                'application/x-ms-excel',
                'application/x-excel',
                'application/x-dos_ms_excel',
                'application/xls',
                'application/x-xls',
                'application/excel',
                'application/download',
                'application/vnd.ms-office',
                'application/msword'
            ),
            'ppt' => array(
                'application/powerpoint',
                'application/vnd.ms-powerpoint',
                'application/vnd.ms-office',
                'application/msword'
            ),
            'pptx' => array(
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/x-zip',
                'application/zip'
            ),
            'wbxml' => 'application/wbxml',
            'wmlc' => 'application/wmlc',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'gtar' => 'application/x-gtar',
            'gz' => 'application/x-gzip',
            'gzip' => 'application/x-gzip',
            'php' => array(
                'application/x-httpd-php',
                'application/php',
                'application/x-php',
                'text/php',
                'text/x-php',
                'application/x-httpd-php-source'
            ),
            'php4' => 'application/x-httpd-php',
            'php3' => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source',
            'js' => array(
                'application/x-javascript',
                'text/plain'
            ),
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'tar' => 'application/x-tar',
            'tgz' => array(
                'application/x-tar',
                'application/x-gzip-compressed'
            ),
            'z' => 'application/x-compress',
            'xhtml' => 'application/xhtml+xml',
            'xht' => 'application/xhtml+xml',
            'zip' => array(
                'application/x-zip',
                'application/zip',
                'application/x-zip-compressed',
                'application/s-compressed',
                'multipart/x-zip'
            ),
            'rar' => array(
                'application/x-rar',
                'application/rar',
                'application/x-rar-compressed'
            ),
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'mp3' => array(
                'audio/mpeg',
                'audio/mpg',
                'audio/mpeg3',
                'audio/mp3'
            ),
            'aif' => array(
                'audio/x-aiff',
                'audio/aiff'
            ),
            'aiff' => array(
                'audio/x-aiff',
                'audio/aiff'
            ),
            'aifc' => 'audio/x-aiff',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'rv' => 'video/vnd.rn-realvideo',
            'wav' => array(
                'audio/x-wav',
                'audio/wave',
                'audio/wav'
            ),
            'bmp' => array(
                'image/bmp',
                'image/x-bmp',
                'image/x-bitmap',
                'image/x-xbitmap',
                'image/x-win-bitmap',
                'image/x-windows-bmp',
                'image/ms-bmp',
                'image/x-ms-bmp',
                'application/bmp',
                'application/x-bmp',
                'application/x-win-bitmap'
            ),
            'gif' => 'image/gif',
            'jpeg' => array(
                'image/jpeg',
                'image/pjpeg'
            ),
            'jpg' => array(
                'image/jpeg',
                'image/pjpeg'
            ),
            'jpe' => array(
                'image/jpeg',
                'image/pjpeg'
            ),
            'jp2' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'j2k' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'jpf' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'jpg2' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'jpx' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'jpm' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'mj2' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'mjp2' => array(
                'image/jp2',
                'video/mj2',
                'image/jpx',
                'image/jpm'
            ),
            'png' => array(
                'image/png',
                'image/x-png'
            ),
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'css' => array(
                'text/css',
                'text/plain'
            ),
            'html' => array(
                'text/html',
                'text/plain'
            ),
            'htm' => array(
                'text/html',
                'text/plain'
            ),
            'shtml' => array(
                'text/html',
                'text/plain'
            ),
            'txt' => 'text/plain',
            'text' => 'text/plain',
            'log' => array(
                'text/plain',
                'text/x-log'
            ),
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'xml' => array(
                'application/xml',
                'text/xml',
                'text/plain'
            ),
            'xsl' => array(
                'application/xml',
                'text/xsl',
                'text/xml'
            ),
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'avi' => array(
                'video/x-msvideo',
                'video/msvideo',
                'video/avi',
                'application/x-troff-msvideo'
            ),
            'movie' => 'video/x-sgi-movie',
            'doc' => array(
                'application/msword',
                'application/vnd.ms-office'
            ),
            'docx' => array(
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
                'application/msword',
                'application/x-zip'
            ),
            'dot' => array(
                'application/msword',
                'application/vnd.ms-office'
            ),
            'dotx' => array(
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
                'application/msword'
            ),
            'xlsx' => array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip',
                'application/vnd.ms-excel',
                'application/msword',
                'application/x-zip'
            ),
            'word' => array(
                'application/msword',
                'application/octet-stream'
            ),
            'xl' => 'application/excel',
            'eml' => 'message/rfc822',
            'json' => array(
                'application/json',
                'text/json'
            ),
            'pem' => array(
                'application/x-x509-user-cert',
                'application/x-pem-file',
                'application/octet-stream'
            ),
            'p10' => array(
                'application/x-pkcs10',
                'application/pkcs10'
            ),
            'p12' => 'application/x-pkcs12',
            'p7a' => 'application/x-pkcs7-signature',
            'p7c' => array(
                'application/pkcs7-mime',
                'application/x-pkcs7-mime'
            ),
            'p7m' => array(
                'application/pkcs7-mime',
                'application/x-pkcs7-mime'
            ),
            'p7r' => 'application/x-pkcs7-certreqresp',
            'p7s' => 'application/pkcs7-signature',
            'crt' => array(
                'application/x-x509-ca-cert',
                'application/x-x509-user-cert',
                'application/pkix-cert'
            ),
            'crl' => array(
                'application/pkix-crl',
                'application/pkcs-crl'
            ),
            'der' => 'application/x-x509-ca-cert',
            'kdb' => 'application/octet-stream',
            'pgp' => 'application/pgp',
            'gpg' => 'application/gpg-keys',
            'sst' => 'application/octet-stream',
            'csr' => 'application/octet-stream',
            'rsa' => 'application/x-pkcs7',
            'cer' => array(
                'application/pkix-cert',
                'application/x-x509-ca-cert'
            ),
            '3g2' => 'video/3gpp2',
            '3gp' => array(
                'video/3gp',
                'video/3gpp'
            ),
            'mp4' => 'video/mp4',
            'm4a' => 'audio/x-m4a',
            'f4v' => array(
                'video/mp4',
                'video/x-f4v'
            ),
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            'aac' => 'audio/x-acc',
            'm4u' => 'application/vnd.mpegurl',
            'm3u' => 'text/plain',
            'xspf' => 'application/xspf+xml',
            'vlc' => 'application/videolan',
            'wmv' => array(
                'video/x-ms-wmv',
                'video/x-ms-asf'
            ),
            'au' => 'audio/x-au',
            'ac3' => 'audio/ac3',
            'flac' => 'audio/x-flac',
            'ogg' => array(
                'audio/ogg',
                'video/ogg',
                'application/ogg'
            ),
            'kmz' => array(
                'application/vnd.google-earth.kmz',
                'application/zip',
                'application/x-zip'
            ),
            'kml' => array(
                'application/vnd.google-earth.kml+xml',
                'application/xml',
                'text/xml'
            ),
            'ics' => 'text/calendar',
            'ical' => 'text/calendar',
            'zsh' => 'text/x-scriptzsh',
            '7zip' => array(
                'application/x-compressed',
                'application/x-zip-compressed',
                'application/zip',
                'multipart/x-zip'
            ),
            'cdr' => array(
                'application/cdr',
                'application/coreldraw',
                'application/x-cdr',
                'application/x-coreldraw',
                'image/cdr',
                'image/x-cdr',
                'zz-application/zz-winassoc-cdr'
            ),
            'wma' => array(
                'audio/x-ms-wma',
                'video/x-ms-asf'
            ),
            'jar' => array(
                'application/java-archive',
                'application/x-java-application',
                'application/x-jar',
                'application/x-compressed'
            ),
            'svg' => array(
                'image/svg+xml',
                'application/xml',
                'text/xml'
            ),
            'vcf' => 'text/x-vcard',
            'srt' => array(
                'text/srt',
                'text/plain'
            ),
            'vtt' => array(
                'text/vtt',
                'text/plain'
            ),
            'ico' => array(
                'image/x-icon',
                'image/x-ico',
                'image/vnd.microsoft.icon'
            )
        );
    }

    /**
     * Prep FileName
     *
     * @param string $filename
     * @return mixed|string
     */
    protected function _prep_filename(string $filename)
    {
        if (strpos($filename, '.') === FALSE OR $this->allowed_types == '*') {
            return $filename;
        } //strpos($filename, '.') === FALSE OR $this->allowed_types == '*'

        $parts    = explode('.', $filename);
        $ext      = array_pop($parts);
        $filename = array_shift($parts);

        foreach ($parts as $part) {
            if (!in_array(strtolower($part), $this->allowed_types) OR $this->mimes_types(strtolower($part)) === FALSE) {
                $filename .= '.' . $part . '_';
            } //!in_array(strtolower($part), $this->allowed_types) OR $this->mimes_types(strtolower($part)) === FALSE
            else {
                $filename .= '.' . $part;
            }
        } //$parts as $part

        $filename .= '.' . $ext;

        return $filename;
    }

    /**
     * Get File Mime Type
     *
     * @param array $file
     */
    protected function _file_mime_type(array $file)
    {
        $regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME);
            if (is_resource($finfo)) {
                $mime = @finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                if (is_string($mime) && preg_match($regexp, $mime, $matches)) {
                    $this->file_type = $matches[1];
                    return;
                } //is_string($mime) && preg_match($regexp, $mime, $matches)
            } //is_resource($finfo)
        } //function_exists('finfo_file')
        if (DIRECTORY_SEPARATOR !== '\\') {
            $cmd = 'file --brief --mime ' . escapeshellarg($file['tmp_name']) . ' 2>&1';

            if (function_exists('exec')) {
                $mime = @exec($cmd, $mime, $return_status);
                if ($return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches)) {
                    $this->file_type = $matches[1];
                    return;
                } //$return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches)
            } //function_exists('exec')

            if ((bool) @ini_get('safe_mode') === FALSE && function_exists('shell_exec')) {
                $mime = @shell_exec($cmd);
                if (strlen($mime) > 0) {
                    $mime = explode("\n", trim($mime));
                    if (preg_match($regexp, $mime[(count($mime) - 1)], $matches)) {
                        $this->file_type = $matches[1];
                        return;
                    } //preg_match($regexp, $mime[(count($mime) - 1)], $matches)
                } //strlen($mime) > 0
            } //(bool) @ini_get('safe_mode') === FALSE && function_exists('shell_exec')

            if (function_exists('popen')) {
                $proc = @popen($cmd, 'r');
                if (is_resource($proc)) {
                    $mime = @fread($proc, 512);
                    @pclose($proc);
                    if ($mime !== FALSE) {
                        $mime = explode("\n", trim($mime));
                        if (preg_match($regexp, $mime[(count($mime) - 1)], $matches)) {
                            $this->file_type = $matches[1];
                            return;
                        } //preg_match($regexp, $mime[(count($mime) - 1)], $matches)
                    } //$mime !== FALSE
                } //is_resource($proc)
            } //function_exists('popen')
        } //DIRECTORY_SEPARATOR !== '\\'

        if (function_exists('mime_content_type')) {
            $this->file_type = @mime_content_type($file['tmp_name']);
            if (strlen($this->file_type) > 0) {
                return;
            } //strlen($this->file_type) > 0
        } //function_exists('mime_content_type')

        $this->file_type = $file['type'];
    }

    /**
     * Is Really Writable Method
     *
     * @param string $file
     * @return bool
     */
    function is_really_writable(string $file)
    {
        define('FOPEN_WRITE_CREATE', 'ab');
        define('DIR_WRITE_MODE', 0777);

        if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE) {
            return is_writable($file);
        } //DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE

        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand(1, 100) . mt_rand(1, 100));

            if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
                return FALSE;
            } //($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE

            fclose($fp);
            @chmod($file, DIR_WRITE_MODE);
            @unlink($file);
            return TRUE;
        } //is_dir($file)
        elseif (!is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
            return FALSE;
        } //!is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE

        fclose($fp);
        return TRUE;
    }

}
