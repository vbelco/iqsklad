<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author Viktor
 */
class Message {
    private $sprava;
    
    public function set($m) {$this->sprava = $m;}
    public function get() { return $this->sprava; }
    
    public function vypis() {echo "<div id='oznam'>".$this->sprava."</div>";}
    public function add($m) {
        if (strlen($this->sprava)) $this->sprava .= "<br>";
        $this->sprava .= $m;
    }
}

?>
