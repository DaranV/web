<?php
namespace Usage;

use Session\Session;
use Stan\Stan;

class Flash {

    /**
     * Message Types
     * @var array
     */
    private $msgTypes = array("info" => "", "warning" => "", "success" => "", "error" => "");

    /**
     * Message Wrapper
     * @var array
     */
    private $msgWrapper = array("dismissable" => "", "nodismissable" => "");

    /**
     * Instance of Flash
     * @var Flash
     */
    private static $instance;

    /**
     * Flash constructor.
     */
    public function __construct() {
        self::$instance = $this;
        $stan = Stan::getInstance();
        if(is_null(Session::read("flash_messages"))) {
            Session::write("flash_messages", array());
        }
        foreach ($this->msgWrapper as $wrapper => $value){
            $this->msgWrapper[$wrapper] = $stan->theme->flash->$wrapper->template;
        }
        foreach ($this->msgTypes as $type => $value){
            $this->msgTypes[$type] = $stan->theme->flash->class->$type;
        }

    }

    /**
     * Return the instance of Flash
     * @return Flash
     */
    public static function getInstance() : Flash{
        if(!self::$instance){
            self::$instance = new Flash();
        }

        return self::$instance;
    }

    /**
     * Add a message
     *
     * @param string $type
     * @param string $message
     * @return bool
     */

    public function add(string $type, string $message) : bool {
        if(is_null(Session::read("flash_messages"))) return false;
        if(!isset($type) || !isset($message[0])) return false;
        if(!array_key_exists($type, $this->msgTypes)) die('"' . strip_tags($type) . '" is not a valid message type!');
        if(!array_key_exists($type, Session::read("flash_messages"))) Session::write("flash_messages", array(), $type);
        Session::add("flash_messages", $message, $type);
        return true;
    }

    /**
     * Display Message(s)
     *
     * @param string $type
     * @param bool $print
     * @param bool $dismissable
     * @return bool|string
     */

    public function display(string $type='all', bool $print=true, bool $dismissable = true) {
        $messages = '';
        $data = '';
        $flashType = 'dismissable';
        if(!$dismissable) $flashType = "nodismissable";

        if(is_null(Session::read("flash_messages"))) return false;

        if(array_key_exists($type, $this->msgTypes)) {
            foreach(Session::read("flash_messages", $type) as $msg) {
                $messages .= $msg;
            }
            $data .= sprintf($this->msgWrapper[$flashType], $this->msgTypes[$type], $messages);
            $this->clear($type);
        } elseif($type == 'all') {
            foreach(Session::read("flash_messages") as $type => $msgArray) {
                $messages = '';
                foreach($msgArray as $msg) {
                    $messages .= $msg;
                }
                $data .= sprintf($this->msgWrapper[$flashType], $this->msgTypes[$type], $messages);
            }
            $this->clear();
        } else {
            return false;
        }

        if($print) {
            echo $data;
        } else {
            return $data;
        }
    }

    /**
     * Return Formmated Message(s)
     *
     * @param string $messages
     * @param string $type
     * @param bool $dismissable
     * @return string
     */

    public function show(string $messages, string $type='success', bool $dismissable = true) : string {
        $flashType = 'dismissable';
        if(!$dismissable) $flashType = "nodismissable";
        $data = sprintf($this->msgWrapper[$flashType], $this->msgTypes[$type], $messages);
        return $data;
    }

    /**
     * Has Error
     *
     * @return bool
     */
    public function hasErrors() : bool {
        return !is_null(Session::read("flash_messages", "danger")) ? false : true;
    }

    /**
     * Has Messages
     *
     * @param null|string $type
     * @return array|bool|null|string
     */

    public function hasMessages($type=null) {
        if(!is_null($type)) {
            if(!is_null(Session::read("flash_messages", $type))) return Session::read("flash_messages", $type);
        } else {
            foreach($this->msgTypes as $type) {
                if(!is_null(Session::read("flash_messages"))) return true;
            }
        }
        return false;
    }

    /**
     * Clear Message(s)
     *
     * @param string $type
     * @return bool
     */

    public function clear(string $type='all') : bool {
        if( $type == 'all' ) {
            Session::destroy("flash_messages");
        } else {
            Session::destroy("flash_messages", $type);
        }
        return true;
    }
}