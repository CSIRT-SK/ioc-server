<?php
/*
Hello World API controller
*/
class Hello {
    private $_params;
    
    public function __construct($params) {
        $this->_params = $params;
    }
    
    public function greetAction() {
        $greeting = new Greeting();
        $greeting->greet = $this->_params['greeting'];
        $greeting->target = $this->_params['target'];
        
        return $greeting->create();
    }
}
?>