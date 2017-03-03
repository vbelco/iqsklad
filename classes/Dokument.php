<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dokument
 *
 * @author u
 */
class 

Dokument {
    var $name;  //nazov dokumentu: prijemka, vydajka a pod.
    var $organizacia_id; // refernecia na vlastnika dokumentu
    var $cislo; //cislo dokumentu, toto cislo sa repreyentuje na obrazovke
    var $dokument_id; // id z databazy
    var $datum_vytvorenia; //datum, kedz sa dokument vytvoril
    var $zoznam_poloziek; //zoznam poloziek obsiahnutych v danom dokumente
                              // pole obsahuje udaje 'tovar_id' a 'pocet' 
    var $database_id; //hodnota id z databazy
    var $poznamka; //text obsahujuci poznamku k danemu dokumentu
    
    public function __construct($cislo, $organizacia_id) {
        $this->cislo = $cislo; //cislo dokumentu je v tvare xxxAABBB
        //zaznamena datum vytvorenia
        $this->datum_vytvorenia = new DateTime();
        $this->organizacia_id = $organizacia_id;
    }
    
    public function pridajTovar($tovar_id, $pocet){
        $this->zoznam_poloziek [$tovar_id] += $pocet; //pripocita k aktualnemu poctu sumu yadanu vo formulare
    }
    
    public function odoberTovar ($tovar_id, $pocet){
        global $message;
        if ($this->zoznam_poloziek[$tovar_id] < $pocet) {
            $message->add("Pocet poloziek nemoze klesnut na zapornu hodnotu!!!");
            return false;
        } else {
            $this->zoznam_poloziek[$tovar_id] -= $pocet;
        }
    }
    
    public function nacitajPolozku($tovar_id, $pocet) { //nacita do pola zoznam poloziek polozku z parametrov
         $this->zoznam_poloziek[$tovar_id] = $pocet;
    }
    
    public function setDokumentId($datab_id){ $this->dokument_id = $datab_id; }
    public function setDatumVytvorenia($date) { $this->datum_vytvorenia = $date; }
    public function setPoznamka($pozn) { $this->poznamka = $pozn; }
    
    public function getDatumVytvorenia() { return $this->datum_vytvorenia->format('d. m. Y H:i'); }
    public function getCislo() {  return $this->cislo;  }
    public function getName(){ return $this->name;  }
    public function getDokumentId() { return $this->dokument_id; }
    public function getPoznamka() { return $this->poznamka; }
    
    public function getPolozkuTovaru($kluc) { return $this->zoznam_poloziek[$kluc]; }
    public function getZoznamPoloziekTovaru() { return $this->zoznam_poloziek; }
    
}
