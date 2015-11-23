<?php
/*
Greeting model for the Hello World controller
*/
class Greeting {
    public $greet;
    public $target;
    
    public function create() {
        return $this->greet . ', ' . $this->target . '!';
    }

}
?>