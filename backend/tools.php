<?php
// Start session if it hasn't been started
//if (!session_id()) session_start();

// Set timezone
date_default_timezone_set('Europe/Vienna');

/**
 * Create a proper JSON response for XHR requests.
 * 
 * A response will have the following values:
 * | `success` - Did the request succeed? (true/false)
 * | `msg`     - Field for the main message
 * | `payload` - Array of your custom designed fields
 * | `debug`
 *     | `log`     - Array of debug log messages
 *     | `ms_time` - Time it took in milliseconds to execute PHP
 * 
 * By default, the response status is marked as successful, 
 * so you need to change it manually if you want to show an error.
 * 
 * If $json_header is true, then the object changes the header 
 * type to application/json. Turn off for debugging
 */
class JSON_Resp {
    private $start_time;
    private $success; // false or true
    private $msg;
    private $debug_log;
    private $payload;

    function __construct($json_header = true){
        if ($json_header) header('Content-Type: application/json');
        $this->start_time = round(microtime(true) * 1000);
        $this->success = true;
        $this->msg = "";
        $this->payload = array();
        $this->debug_log = array();
    }

    /**
     * Show that something went wrong with 
     * the message telling the cause (optional, 
     * will override previous message)
     */
    function set_error($error_msg = "") {
        $this->success = false;
        $this->msg = ($error_msg == "") ? $this->msg : $error_msg;
    }

    /**
     * Validate response's status to show everything
     * went fine and computation was successful 
     * (optional, will override previous message)
     */
    function set_success($success_msg = "") {
        $this->success = true;
        $this->msg = ($success_msg == "") ? $this->msg : $success_msg;
    }

    /**
     * Set response message
     */
    function set_message($new_msg) {
        $this->msg = $this->msg.$new_msg;
    }

    /**
     * Add a new value into the `payload` field. 
     * If can be a simple value or an array
     * @param string $identifier Key of value
     * @param any $val Value
     */
    function add_field($identifier, $val){
        $this->payload[$identifier] = $val;
    }

    /**
     * Add a new debug entry into the debug log
     */
    function push_to_debug_log($new_log) {
        array_push($this->debug_log, $new_log);
    }

    /**
     * Prints result on end of PHP
     * If the $json_header is enabled then you MUST NOT 
     * print anything else with echo, only call this function
     */
    function __destruct() {
        echo json_encode(
            array(
                "success" => $this->success, 
                "msg" => $this->msg,
                "payload" => $this->payload,
                "debug" => array(
                    "log" => $this->debug_log,
                    "ms_time" => (round(microtime(true) * 1000) - $this->start_time)
                    )
                )
        );
    }
}

?>