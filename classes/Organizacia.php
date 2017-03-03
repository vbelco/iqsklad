<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Organizacia
 *
 * @author u
 */
class Organizacia {
    protected $id;
    protected $nazov;
    protected $ulica;
    protected $psc;
    protected $mesto;
    protected $stat;
    protected $ico;
    protected $dic;
    protected $icdph;
    protected $telefon;
    
    protected $fixne_parametre; //zoznam fixnych parametrov, ktore su uzivatelom povolene pre danu  organizaciu
    protected $volitelne_parametre; //zoznam volitelnych parametrov definovanych uzivatelom pre danu organizaciu    
    
    //cislovanie prijemky, cislo sa sklada z troch prismen a siedmych cisel XXXAABBBBB XXX je lubovolne, prednastavene je 'VYD' AA je cislo roku a BBBBB je poradie
    // v databaze sa uklada je XXX ostatne su generovane automaticky
    // plati aj pre vzdajku a ostatne dokumenty
    protected $cislovanie_prijemky; //format cislovania prijemky /format string
    protected $cislo_prijemky; //aktualne cislo prijemky /fortma int
    protected $cislovanie_vydajky;
    protected $cislo_vydajky;
    protected $cislovanie_vyrobky;
    protected $cislo_vyrobky;
    
    protected $aktualny_rok_dokumentov;
    
    protected $sklady; //zoznam skladov definovanych pre tuto organizaciu
   
    public function getID() { return $this->id;   }
    public function getNazov() { return $this->nazov;   }
    public function getUlica() { return $this->ulica;   }
    public function getPsc() { return $this->psc;   }
    public function getMesto() { return $this->mesto;   }
    public function getStat() { return $this->stat;   }
    public function getICO() { return $this->ico;   }
    public function getDIC() { return $this->dic;   }
    public function getICDPH() { return $this->icdph;   }
    public function getTelefon() { return $this->telefon;   }
    public function getCislovaniePrijemky() {return $this->cislovanie_prijemky; }
    public function getCislovanieVydajky() {return $this->cislovanie_vydajky; }
    public function getCislovanieVyrobky() {return $this->cislovanie_vyrobky; }
    
    public function getCisloNovejPrijemky() {
        $nove_cislo = date("y").sprintf("%05d", $this->cislo_prijemky + 1 );
        return $this->cislovanie_prijemky.$nove_cislo; 
        
    }
    public function getCisloNovejVydajky() {
        $nove_cislo = date("y").sprintf("%05d", $this->cislo_vydajky + 1 );
        return $this->cislovanie_vydajky.$nove_cislo; 

    }
    public function getCisloNovejVyrobky() {
        $nove_cislo = date("y").sprintf("%05d", $this->cislo_vyrobky + 1 );
        return $this->cislovanie_vyrobky.$nove_cislo; 

    }

    public function setId($id) { $this->id = $id;   }
    public function setCislovaniePrijemky($cislovanie){ $this->cislovanie_prijemky = $cislovanie; }
    public function setCisloPrijemky($cislo) {$this->cislo_prijemky = $cislo;}
    public function pridajCisloPrijemky(){ $this->cislo_prijemky++; }
    
    public function setCislovanieVydajky($cislovanie){ $this->cislovanie_vydajky = $cislovanie; }
    public function setCisloVydajky($cislo) {$this->cislo_vydajky = $cislo;}
    public function pridajCisloVydajky(){ $this->cislo_vydajky++; }
    
    public function setCislovanieVyrobky($cislovanie){ $this->cislovanie_vyrobky = $cislovanie; }
    public function setCisloVyrobky($cislo) {$this->cislo_vyrobky = $cislo;}
    public function pridajCisloVyrobky(){ $this->cislo_vyrobky++; }
    
    public function setVsetkyUdaje($nazov, $ulica, $psc, $mesto, $stat, $ico, $dic, $icdph, $telefon) {
        $this->nazov = $nazov;
        $this->ulica = $ulica;
        $this->psc = $psc;
        $this->mesto = $mesto;
        $this->stat = $stat;
        $this->ico = $ico;
        $this->dic = $dic;
        $this->icdph = $icdph;
        $this->telefon = $telefon;
    }
    
    public function setAktualnyRokDokumentov() {
        $this->aktualny_rok_dokumentov = date("Y");
    }
    
    public function loadFromDatabase($id) {
        global $database;
        $query = $database->select("SELECT * FROM organizacia WHERE id = '".$id."'");
        $vysl = $database->fetch_array($query);
        $this->id = $id;
        $this->nazov = $vysl["nazov"];
        $this->ulica = $vysl["ulica"];
        $this->psc = $vysl["psc"];
        $this->mesto = $vysl["mesto"];
        $this->stat = $vysl["stat"];
        $this->ico = $vysl["ico"];
        $this->dic = $vysl["dic"];
        $this->icdph = $vysl["icdph"];
        $this->telefon = $vysl["telefon"];
        $this->cislo_prijemky = $vysl["cislo_prijemky"];
        $this->cislovanie_prijemky = $vysl["cislovanie_prijemky"];
        $this->cislo_vydajky = $vysl["cislo_vydajky"];
        $this->cislovanie_vydajky = $vysl["cislovanie_vydajky"];
        $this->cislo_vyrobky = $vysl["cislo_vyrobky"];
        $this->cislovanie_vyrobky = $vysl["cislovanie_vyrobky"];
        $this->aktualny_rok_dokumentov = $vysl["aktualny_rok_dokumentov"];
        
        //nacitanie fixnzch parametrov
          $vysl2 = $database->select("SELECT * FROM fixne_parametre_tovaru WHERE organizacia_id='".$this->id."'") or die(mysql_error());
          $i=0;
          while ( $polozka2 = $database->fetch_array($vysl2) ) {
              $this->fixne_parametre[$i] = $polozka2["nazov_parametra"];
              $i++;
          }
          //nacitanie volitelnzch parametrov
          $vysl3 = $database->select("SELECT * FROM parametre_tovaru WHERE organizacia_id='".$this->id."'") or die(mysql_error());
          while ( $polozka3 = $database->fetch_array($vysl3) ) {
              $this->volitelne_parametre[ $polozka3["id"] ] = $polozka3["nazov"];
          }
        
        //nacitanie zoznamu skladov pre tuto organizaciu
        $vysl4 = $database->select("SELECT * FROM sklad WHERE organizacia_id='".$this->id."'") or die(mysql_error()); 
        while ( $polozka4 = $database->fetch_array($vysl4) ) {
              $this->sklady[ $polozka4["id"] ] = $polozka4["name"];
          }
    }
    
    public function fixny_parameter($nazov_fix_parametra){
          /*
           * zisti ci fixny parameter je zvoleny pre tento sklad
           * ak ano, vrati true, ak nie vrati false
           * vstup> nazov fixneho parametra
           */
          if( !count($this->fixne_parametre) ) return false; //pokial este nemame ziadne hodnoty v poli
          return in_array($nazov_fix_parametra, $this->fixne_parametre);
      }//end function fixny_parameter
      
    public function delete_fixny_parameter ($nazov_fix_parametra){
          /*
           * vymaze fixny parameter pre tento sklad
           * vstup> nazov fixneho parametra
           */
          global $database;
          //vymazanie z aktualneho skladu
          $this->fixne_parametre = array_diff($this->fixne_parametre, array($nazov_fix_parametra) );
          //este vymazeme z datbazy
          $database->raw_query("DELETE FROM fixne_parametre_tovaru WHERE organizacia_id= '".$this->id."'
                                                                   AND nazov_parametra = '".$nazov_fix_parametra."'");
    }
    
    public function add_fixny_parameter ($nazov_fix_parametra){
          /*
           * prida fixny parameter pre tento sklad
           * vstup> nazov fixneho parametra
           */
          global $database;
          // vlozi do databazy polozku
          $hodnoty = array("organizacia_id" =>  $this->id,
                           "nazov_parametra" =>  $nazov_fix_parametra   );
          $database->insert( "fixne_parametre_tovaru", $hodnoty );
          $insert_id = $database->getInsertId();
          //vlozi do pola parametrov tento novy
          $this->volitelne_parametre[$insert_id] = $nazov_fix_parametra;
    }
    
    public function add_volitelny_parameter ($nazov_volitelneho_parametra){
          /*
           * prida volitelny parameter pre tento sklad
           * vstup> nazov parametra
           */
          global $database;
          //este vlozi do databazy polozku
          $hodnoty = array(  "organizacia_id" => $this->id,
                            "nazov" =>  $nazov_volitelneho_parametra   
                           );
          $database->insert( "parametre_tovaru", $hodnoty );
          //vlozenie noveho parametra do objektu
          $this->volitelne_parametre[ $database->getInsertId() ] = $nazov_volitelneho_parametra;
          
          //if( !count($this->parametre) ){ //pokial este nemame ziadne hodnoty v poli
          //    $this->parametre[0] = $nazov_volitelneho_parametra;
          //}
          //else {
          //  array_push($this->parametre, $nazov_volitelneho_parametra); //prida do objektu do pola fixnych parametrov
          //}
          
    }
      
      public function get_volitelne_parametre() {
          /*
           * vrati pole volitelnych parametrov
           */
          return $this->volitelne_parametre;
      }
      
      public function set_volitelny_parameter( $parameter_id, $nazov_parametra ) {
          /*
           * zmeni volitelny parameter
           */
           $this->volitelne_parametre[$parameter_id] = $nazov_parametra;
      }
      public function update_volitelny_parameter( $parameter_id, $nazov_parametra ){
          /*
           * upravi volitelne parametre v databaze
           */
          global $database;
          $array_data = array(  "nazov" =>   $nazov_parametra );
          
          if ( $database->update( "parametre_tovaru", $array_data, "id", $parameter_id ) ){
                return true;
          }
          
      } 
      
      public function delete_volitelny_parameter ($parameter_id){
          /*
           * ZMAZE volitelne parametre v databaze
           */
          global $database;
          $database->delete('parametre_tovaru', $parameter_id);
          
          unset($this->volitelne_parametre[$parameter_id]); //vymazanie parametra z databzy
          return true;
      }
    
    public function updateInDatabase(){
          global $database;
          $array_data = array(
                                "nazov" => $this->nazov,
                                "ulica" => $this->ulica,
                                "psc" => $this->psc,
                                "mesto" => $this->mesto,
                                "stat" => $this->stat,
                                "ico" => $this->ico,
                                "dic" => $this->dic,
                                "icdph" => $this->icdph,
                                "telefon" => $this->telefon,
                                "cislovanie_prijemky" => $this->cislovanie_prijemky,
                                "cislo_prijemky" => $this->cislo_prijemky,
                                "cislovanie_vydajky" => $this->cislovanie_vydajky,
                                "cislo_vydajky" => $this->cislo_vydajky,
                                "cislovanie_vyrobky" => $this->cislovanie_vyrobky,
                                "cislo_vyrobky" => $this->cislo_vyrobky,
                                "aktualny_rok_dokumentov" => $this->aktualny_rok_dokumentov
                               );
            if ( $database->update("organizacia", $array_data, "id", $this->id ) ){
                return true;
            }
            else{
                return false;
            }
      }
    
    public function storeTodatabase() {
        global $database;
        //budeme vkladat aj s id pokial je definovany
        if (isset($this->id)){
            $array_data = array("id" => $this->id,
                                "nazov" => $this->nazov,
                                "ulica" => $this->ulica,
                                "psc" => $this->psc,
                                "mesto" => $this->mesto,
                                "stat" => $this->stat,
                                "ico" => $this->ico,
                                "dic" => $this->dic,
                                "icdph" => $this->icdph,
                                "telefon" => $this->telefon,
                                "cislovanie_prijemky" => $this->cislovanie_prijemky,
                                "cislo_prijemky" => $this->cislo_prijemky,
                                "cislovanie_vydajky" => $this->cislovanie_vydajky,
                                "cislo_vydajky" => $this->cislo_vydajky,
                                "cislovanie_vyrobky" => $this->cislovanie_vyrobky,
                                "cislo_vyrobky" => $this->cislo_vyrobky,
                                "aktualny_rok_dokumentov" => $this->aktualny_rok_dokumentov
                                 );
        }
        else{ //vkladame bez id, musime ho potom preniest do triedy
                $array_data = array(
                                "nazov" => $this->nazov,
                                "ulica" => $this->ulica,
                                "psc" => $this->psc,
                                "mesto" => $this->mesto,
                                "stat" => $this->stat,
                                "ico" => $this->ico,
                                "dic" => $this->dic,
                                "icdph" => $this->icdph,
                                "telefon" => $this->telefon,
                                "cislovanie_prijemky" => $this->cislovanie_prijemky,
                                "cislo_prijemky" => $this->cislo_prijemky,
                                "cislovanie_vydajky" => $this->cislovanie_vydajky,
                                "cislo_vydajky" => $this->cislo_vydajky,
                                "cislovanie_vyrobky" => $this->cislovanie_vyrobky,
                                "cislo_vyrobky" => $this->cislo_vyrobky,
                                "aktualny_rok_dokumentov" => $this->aktualny_rok_dokumentov
                               );
        }
        $database->insert("organizacia", $array_data);
        $this->id = $database->getInsertId();
        
        return $this->id; //vratime tuto hodnotu aby sa mohl aulozit do triedy uzivatela napr.
    }
    
    //fnkcia vzkona kontrolu na prechod na novy rok
    //v pripade prechodu na novy rok vrati true ked sme este v tom istom roku tak vrati false
    public function kontrolaPrechoduRoku() {
        echo $this->aktualny_rok_dokumentov ." > ". date("Y");
        if ( $this->aktualny_rok_dokumentov != date("Y") ) {
            return true;
        }
        else { return false; }
    }
    
    public function resetujCiselnikyDokumentov() {
        $this->cislo_prijemky = 0;
        $this->cislo_vydajky = 0;
        $this->cislo_vyrobky = 0;
    }
    
}
