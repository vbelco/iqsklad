<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zobrazovac
 *
 * @author u
 */
class Zobrazovac {
    private $dokumenty; //pole dokumentov typ Prijemka, Vydajka, Vyroba
    private $typ_dokumentov; // charakteriyuje o ake dokumenty sa jedna: prijemky, vydajky, vyroba, IDE TIEZ O NAZVY TABULIEK
    private $max_na_stranku; // polcet poloziek na stranku
    private $zorad_podla; // priznak podla ktoreho budeme zoradovat
    
    public function __construct($zoradpodla = "datum_vytvorenia DESC", $maxnastranku = 20 ) {
        $this->zorad_podla = $zoradpodla;
        $this->max_na_stranku = $maxnastranku;
    }
    
    public function nacitajStranku( $typ_dokumentu ) { //nacitanie zoznamu dokumentov podla  parametrov
        global $uzivatel;
        global $database;
        $organizacia = new Organizacia();
        $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
        switch ( $typ_dokumentu ){
            case "prijemky":
            case "vydajky":
            case "vyrobky":
                $this->typ_dokumentov = $typ_dokumentu;
                //vseob dotaz pri vztvoreni
                $sql_dotaz = "SELECT * FROM ".$this->typ_dokumentov." WHERE organizacia_id = '".$organizacia->getId()."' ORDER BY ".$this->zorad_podla." LIMIT ".$this->max_na_stranku;
                $query = $database->select($sql_dotaz);
                $i=0;
                while ($vysl = $database->fetch_array($query) ){
                    $this->dokumenty[] = new Prijemka($vysl["cislo"], $vysl["organizacia_id"]);
                    $this->dokumenty[$i]->setDokumentId($vysl["id"]);//nastavenie referencneho id z databazy, aby sme ho vedeli pouzit na nacitavanie detailov z datab.
                    $datum = new DateTime( $vysl["datum_vytvorenia"] );
                    $this->dokumenty[$i]->setDatumVytvorenia( $datum );
                    $i++;
                }
            break;
        }//end switch
    }//end 
    
    //nacitanie jedneho dokumentu podla jeho id a typu
    public function nacitajDetaily($typ_dokumentu, $id_dokumentu) {
        global $uzivatel;
        global $database;
        
        $this->typ_dokumentov = $typ_dokumentu;
        //dotaz na nacitanie dokumentu 
        $sql_dotaz = "SELECT * FROM ".$this->typ_dokumentov." WHERE id='".$id_dokumentu."'";
        $query = $database->select($sql_dotaz);
        $vysl = $database->fetch_array($query);
        
        
        switch ( $typ_dokumentu ){    
            case "prijemky":
                $this->dokumenty = new Prijemka($vysl["cislo"], $vysl["organizacia_id"]);
                $this->dokumenty->setDokumentId($vysl["id"]);//nastavenie referencneho id z databazy, aby sme ho vedeli pouzit na nacitavanie detailov z datab.
                $datum = new DateTime( $vysl["datum_vytvorenia"] );
                $this->dokumenty->setDatumVytvorenia( $datum );
                $this->dokumenty->setPoznamka($vysl["poznamka"]);
                //teraz nacitanie vsetkych poloziek prijemky
                $sql_dotaz = "SELECT * FROM prijemka_tovar WHERE prijemka_id='".$id_dokumentu."'";
                $query = $database->select($sql_dotaz);
                while ($vysl = $database->fetch_array($query)) {
                    $this->dokumenty->nacitajPolozku($vysl["tovar_id"], $vysl["pocet"]);
                }
            break;
                
            case "vydajky":
                $this->dokumenty = new Vydajka($vysl["cislo"], $vysl["organizacia_id"]);
                $this->dokumenty->setDokumentId($vysl["id"]);//nastavenie referencneho id z databazy, aby sme ho vedeli pouzit na nacitavanie detailov z datab.
                $datum = new DateTime( $vysl["datum_vytvorenia"] );
                $this->dokumenty->setDatumVytvorenia( $datum );
                $this->dokumenty->setPoznamka($vysl["poznamka"]);
                $sql_dotaz = "SELECT * FROM vydajka_tovar WHERE vydajka_id='".$id_dokumentu."'";
                $query = $database->select($sql_dotaz);
                while ($vysl = $database->fetch_array($query)) {
                    $this->dokumenty->nacitajPolozku($vysl["tovar_id"], $vysl["pocet"]);
                }
            break;
            
            case "vyrobky":
                $this->dokumenty = new Vyrobka($vysl["cislo"], $vysl["organizacia_id"]);
                $this->dokumenty->setDokumentId($vysl["id"]);//nastavenie referencneho id z databazy, aby sme ho vedeli pouzit na nacitavanie detailov z datab.
                $datum = new DateTime( $vysl["datum_vytvorenia"] );
                $this->dokumenty->setDatumVytvorenia( $datum );
                $this->dokumenty->setPoznamka($vysl["poznamka"]);
                $sql_dotaz = "SELECT * FROM vyrobka_tovar WHERE vyrobka_id='".$id_dokumentu."'";
                $query = $database->select($sql_dotaz);
                while ($vysl = $database->fetch_array($query)) {
                    $this->dokumenty->nacitajPolozku($vysl["tovar_id"], $vysl["pocet"]);
                }
            break;
        }//end switch
    }
    
    public function zobrazDokumenty()
    {
        echo "<table>";
        echo "<tr> <td>Číslo</td> <td>Dátum</td> <td></td>  </tr>";
        //prebehne nacitane dokumenty v triede
        foreach ($this->dokumenty as $dokument){
            echo "<tr>";
            echo "<td>".$dokument->getCislo()."</td>";
            echo "<td>".$dokument->getdatumVytvorenia()."</td>";
            echo "<td> <form name='detaily_zobrazenych_dokumentov' >";
            echo "     <input type='hidden' name='dokument_id' value='".$dokument->getDokumentId()."' >";
            echo "     <input type='hidden' name='typ_dokumentu' value='".$this->typ_dokumentov."' >";
            echo "     <input type='hidden' name='pageaction' value='zobraz-detaily-dokumentu'>";
            echo "     <input type='submit' name='submit' value='detaily...' >";
            echo "     </form>";
            echo "</td>";
            echo "</tr>";
        }//end foreach
    }
    
    public function ZobrazDetailyDokumentu()
    {
        global $database;
        global $uzivatel;
    
        echo "<table>";
        echo "<tr> <td>Názov</td> <td>Počet</td> <td>Kód</td> <td>Popis</td>  </tr>";
        //prebehne nacitane dokumenty v triede
        foreach ($this->dokumenty->getZoznamPoloziekTovaru() as $kluc => $hodnota){
            //nacitanie udajov o jednom tovare
            $sql_dotaz = "SELECT * FROM tovar WHERE id='".$kluc."'";
            $query = $database->select($sql_dotaz);
            $vysl = $database->fetch_array($query);
            echo "<tr>";
            echo "<td>".$vysl["name"]."</td>";
            echo "<td>".$hodnota."</td>";
            echo "<td>".$vysl["kod"]."</td>";
            echo "<td>".$vysl["popis"]."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    public function getTypDokumentu() {
        switch ( $this->typ_dokumentov ){ //kvoli vzstupu aby bol poriadne po sloveky
            case "prijemky":
                return "Príjemka";
            break;
            case "vydajky":
                return "Výdajka";
            break;
            case "vyroba":
                return "Výroba";
            break;
        }//end switch
    }
    
    public function getCisloDokumentov() {
        if (is_array($this->dokumenty) ){
            foreach ($this->dokumenty as $dokument){
                $navrat[] = $dokument->getCislo();
            }
        } else {
            $navrat = $this->dokumenty->getCislo();
        }
        return $navrat;
    }
    
    public function getDokument() { return $this->dokumenty; } //spristupnenie dokumentu a jeho vlastnosti
    
    public function getZoradPodla($zorad) {  //zoradenie dokumentov
        $this->$zorad_podla = $zorad;
    }
}
