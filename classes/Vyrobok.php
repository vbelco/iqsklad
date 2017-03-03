<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vyrobok
 *
 * @author u
 */
include_once "Item.php";



class Vyrobok extends Item {
    protected $items; // pole tovaru z ktoreho je vyrobok zlozeny, index je id tovaru a hodnota je mnozstvo tovaru potrebneho k vyrobku
    
    //funkcia nahra udaje o dielcich tovaroch z ktoeho je vyrobok zlozeny
    public function loadTovarFromDatabase() {
        global $database;
        $sql_query = "SELECT * FROM tovar_vyrobok WHERE tovar_vyrobok_id = '".$this->id."'";
        $query = $database->select($sql_query);
        while ( $vysl = $database->fetch_array($query) ){
            $this->items[$vysl["tovar_id"]] = $vysl["pocet"]; //nahratie do pola items hodnoty dielcih tovarov, tovar_id je index a hodnota pola je pocet tovaru vo vyrobku
        }
    }
    
    // vlozi do datbazy vsetky tovary definovane v poli $items
    public function insertToDatabase() {
        global $database;
        foreach ($this->items as $kluc => $pocet) {
            $udaje = array(   
                          "tovar_vyrobok_id" => $this->id ,
                          "tovar_id" => $kluc ,
                          "pocet" => $pocet); 
            $database->insert("tovar_vyrobok", $udaje);
        }
    }
    
    //zmeni udaje v databaze na zaklade hodnot v premennej $items
    public function updateInDatabase() {
        global $database;
        //prebehne vsetky definicie, tie co su nulove vymaze z databazy, nove prida a na ostatnych updatne hodnotu pocet, ked je hodnota pocet v databaze rovnaka ako mame my, tak nebude menit nic
        global $database;
        foreach ($this->items as $kluc => $pocet) { //prebehne vsetky definovane session
            $stary_vyrobok = new Vyrobok(); //natiahneme si obraz vyrobku co mame v databaze
            $stary_vyrobok->loadItem($this->id); //nahra zakladne udaje o vyrobku
            $stary_vyrobok->loadTovarFromDatabase(); //nahra ostatne udaje o vyrobku z databazy
            if ( !$stary_vyrobok->getTovar($kluc)  ) { //nemame taky v databaze, budeme vkladat novy
                $udaje = array(   
                          "tovar_vyrobok_id" => $this->id ,
                          "tovar_id" => $kluc ,
                          "pocet" => $pocet); 
                $database->insert("tovar_vyrobok", $udaje);
            } else if ( $pocet == 0 ) { //vynulovali sme pocet tovarov podielajucich sa na vyrobku, teda tento riadok zmazeme z databazy
              $database->raw_query("DELETE FROM tovar_vyrobok WHERE tovar_vyrobok_id = '".$this->id."' AND tovar_id = '".$kluc."'");  
            } else if ( $stary_vyrobok->getTovar($kluc) != $pocet) { //len sa name ymenila hodnota, ked ostava taka ista, nejdeme nic robit, sak naco
              $sql_query = "UPDATE tovar_vyrobok SET  pocet = '".$pocet."' WHERE tovar_vyrobok_id = '".$this->id."' AND tovar_id = '".$kluc."'";
              $database->raw_query($sql_query);    
            }
        }//end foreach
    }
    
    //prida do triedy vyrobku dielci tovar
    public function pridajTovar($id_tovaru, $pocet) {
        $this->items[$id_tovaru] = $pocet;
    }
    
    //nacita cele pole do premennej $items
    public function pridajVsetkyTovary ($pole_tovarov){
        foreach ($pole_tovarov as $kluc => $hodnota){
            $this->items[$kluc] = $hodnota;
        }
    }
    
    //nahra polozky $items do SESSION pre dalsie pouzitie
    public function nahrajPolozkyDoSession($nazov_session) {
        foreach ( $this->items as $kluc => $pocet ){
            $_SESSION[$nazov_session][$kluc] = $pocet;
        }
    }
    
    //vrati hodnotu pocet jedneho tovaru z definicie vyrobku
    public function getTovar($index) {
        if (!empty($this->items[$index]) ){
            return $this->items[$index]; //vrati pocet
        } else { 
            return false; 
        }
    }
    
    //vrati pole vsetkych tovarov z definicie vyrobku
    public function getPoleTovarov() { return $this->items;    }
    
    //vrati pole vsetkzch tovarov z definicie vyrobku aj s jeho menami
    //struktura je [id_tovaru] = array ( meno_tovaru, pocet_vo_vyrobku )
    public function getPoleTovarovMena(){
        global $database;
        $navrat = array();
        foreach ($this->items as $kluc => $hodnota){
            $sql_query = "SELECT tovar.name as name, sklad.name as sklad  FROM tovar, sklad WHERE tovar.id = '".$kluc."' AND sklad.id = tovar.sklad_id"; //
            $query = $database->select($sql_query);
            $vysl = $database->fetch_array($query); 
            $navrat[$kluc] = array('meno' => $vysl["name"], 
                                   'pocet' => $hodnota,
                                   'sklad' => $vysl["sklad"]);
        }
        return $navrat;
    }
    
    public function vyrobVyrobok($pocet_vyrobkov) {
    /*
     * odrata dielcie tovary z databazy
     */
      global $database;
      foreach ( $this->items as $kluc => $pocet ){
            $real_pocet = $pocet * $pocet_vyrobkov;
            $raw_sql = "UPDATE tovar SET count = count - ".$real_pocet." WHERE id = ".$kluc;
            $database->raw_query($raw_sql);    
      }//end foreach
    }   
     
}
