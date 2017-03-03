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

class Vyrobka extends Dokument {
    //put your code here
    
    public function __construct($cislo, $user_id) {
        parent::__construct($cislo, $user_id); //volame veduceho a nztvorenie dokumentu
        $this->name = "prijemka";
    }
    
    public function nacitajSessionPolozky(){
        foreach( $_SESSION["zoznam_poloziek_vyrobka"] as $kluc => $polozky ){
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
        //funkcia:
        // 1. zapise dokument vzrobka do databazy
        // 2. zvysi pocet vyrobkov na sklade
        // 3. znizi pocet materialu podla definicie na sklade
        global $database;
        
        // 1. zapisanie vyrobky do databazy
        $array_data = array(
                                "organizacia_id" => $this->organizacia_id,
                                "cislo" => $this->cislo,
                                "datum_vytvorenia" => $this->datum_vytvorenia->format('Y-m-d H:i:s'),
                                "poznamka" => $this->poznamka
                           );
        $database->insert("vyrobky", $array_data);
        $this->database_id = $database->getInsertId();  
        
        //ulozenie zoznamu poloziek do databazy
        foreach ($this->zoznam_poloziek as $kluc => $polozka){
            $vyrobok = new Vyrobok(); //vytvorime si vyrobok
            $vyrobok->loadItem($kluc); //natiahneme zakladne udaje o vyrobku z datab
            $vyrobok->loadTovarFromDatabase(); //natiahne z databzy zoznam tpvaru definujuci tento vyrobok
            
            //ulozenie hodnot do tabulky vyrobka
            $array_data = array (
                                  "vyrobka_id" => $this->database_id,
                                  "tovar_id" => $kluc,
                                  "pocet" => $polozka
                                );  
            $database->insert("vyrobka_tovar", $array_data);
            $this->dokument_id = $database->getInsertid(); //nacitanie vlozeneho id
            
            
            //2. zvysenie poctu vyrobenych vzrobkov na sklade
            $stary_pocet_vyrobkov_na_sklade = $vyrobok->getCount();
            $novy_pocet_vyrobkov_na_sklade = $stary_pocet_vyrobkov_na_sklade + $polozka;
            $vyrobok->setCount($novy_pocet_vyrobkov_na_sklade); //nastavenie noveho poctu vyrobkov na sklade
            $vyrobok->updateItem();
            
            //3. update hodnot tovaru na skladoch uzivatela pre tento konretny vyrobok
            // $polozka udava pocet vyrobkov, ktore sa vyrabaju
            $vyrobok->vyrobVyrobok($polozka);
        }     
    }//end function storeToDatabase
    
}
