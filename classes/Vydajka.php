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

class Vydajka extends Dokument {
    //put your code here
    
    public function __construct($cislo, $organizacia_id) {
        $this->name = "vydajka";
        $this->cislo = $cislo;
        //zaznamena datum vytvorenia
        $this->datum_vytvorenia = new DateTime();
        $this->organizacia_id = $organizacia_id;
    }
    
    public function nacitajSessionPolozky(){
        foreach( $_SESSION["zoznam_poloziek_vydajka"] as $kluc => $polozky ){
            //kontrola ci nieje hodnota nulova
            if ($polozky != 0){
                $this->zoznam_poloziek[$kluc] = $polozky;
            }
        }
    }
    
    public function storeToDatabase(){
        global $database;
        global $uzivatel;
        $organizacia = new Organizacia();
        $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
        
        $array_data = array(
                                "organizacia_id" => $this->organizacia_id,
                                "cislo" => $this->cislo,
                                "datum_vytvorenia" => $this->datum_vytvorenia->format('Y-m-d H:i:s')    
                           );
        $database->insert("vydajky", $array_data);
        $this->database_id = $database->getInsertId();  
        
        //ulozenie zoznamu poloziek do databazy
        foreach ($this->zoznam_poloziek as $kluc => $polozka){
            
            //ulozenie hodnot do tabulky vydajka
            $array_data = array (
                                  "vydajka_id" => $this->database_id,
                                  "tovar_id" => $kluc,
                                  "pocet" => $polozka
                                );  
            $database->insert("vydajka_tovar", $array_data);
            
            //update hodnot tovaru na skladoch uzivatela
            // v pripade ze dany tovar je VYROBKOM  odratame pocet vyrobku kolko ho je na sklade 
            // ak by mal ist do minusu zadefinujeme dokument tzv. automaticku vyrobu (specialne cislovanie)
            $tovar = new Item($kluc); //vytvorime tovar, nacitame udaje z databazy
            $raw_sql = "UPDATE tovar SET count = count - ".$polozka." WHERE id = ".$kluc;
            $database->raw_query($raw_sql);     
        }//end foreach 
    }//end function storeToDatabase
    
}
