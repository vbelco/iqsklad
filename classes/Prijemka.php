<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Prijemka
 *
 * @author u
 */
include_once "Dokument.php";

class Prijemka extends Dokument {
    //put your code here
    
    public function __construct($cislo, $organizacia_id) {
        parent::__construct($cislo, $organizacia_id); //volame veduceho a nztvorenie dokumentu
        $this->name = "prijemka";
    }
    
    public function nacitajSessionPolozky(){
        foreach( $_SESSION["zoznam_poloziek"] as $kluc => $polozky ){
            //kontrola ci nieje hodnota nulova
            if ($polozky != 0){
                $this->zoznam_poloziek[$kluc] = $polozky;
            }
        }
    }
    
    public function nacitajPolePoloziek($polozky) {
        foreach( $polozky as $kluc => $polozky ){
            //kontrola ci nieje hodnota nulova
            if ($polozky != 0){
                $this->zoznam_poloziek[$kluc] = $polozky;
            }
        }
    }
    
    public function storeToDatabase(){
        global $database;
        $array_data = array(
                                "organizacia_id" => $this->organizacia_id,
                                "cislo" => $this->cislo,
                                "datum_vytvorenia" => $this->datum_vytvorenia->format('Y-m-d H:i:s') ,
                                "poznamka" => $this->poznamka
                           );
        $database->insert("prijemky", $array_data);
        $this->database_id = $database->getInsertId();  
        
        //ulozenie zoznamu poloziek do databazy
        foreach ($this->zoznam_poloziek as $kluc => $polozka){
            //ulozenie hodnot do tabulky prijemka
            $array_data = array (
                                  "prijemka_id" => $this->database_id,
                                  "tovar_id" => $kluc,
                                  "pocet" => $polozka
                                );  
            $database->insert("prijemka_tovar", $array_data);
            $this->dokument_id = $database->getInsertid(); //nacitanie vlozeneho id
            
            //update hodnot tovaru na skladoch uzivatela
            $raw_sql = "UPDATE tovar SET count = count + ".$polozka." WHERE id = ".$kluc;
            $database->raw_query($raw_sql);
        }     
    }//end function storeToDatabase
    
}
