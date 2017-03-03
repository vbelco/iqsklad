<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Item
 *
 * @author Viktor Belko <vbelco@hotmail.sk>
 */
class Item{
    protected $type_id;  //identifikator typu polozky
    protected $id; //identifikator polozky
    protected $name; //nazov polozky
    protected $popis; //popis polozky
    protected $count; //pocet poloziek
    protected $min_stav; // minimalny stav na sklade
    protected $registracny_kod; //registracny kod, napr EAN
    
    protected $dodatocne_parametre; //dalsie parametre tovaru, ktore si uzivatel definuje sa a u kazdeho tovaru mozu byt ine
    
    public function loadItem($item_id){  //loads from database values based on item id
        global $database;
        $vysl = $database->select("SELECT * FROM tovar WHERE id='".$item_id."'") or die (mysql_error());
        $polozka = $database->fetch_array($vysl) or die(mysql_error());
        $this->id = $polozka["id"];
        $this->name = $polozka["name"];
        $this->popis = $polozka["popis"];
        $this->count = $polozka["count"];
        $this->min_stav = $polozka["min_stav"];
    }
    
    public function insertNewItem(){ //store item into database
        global $database;
        $udaje = array(   
                          "name" => $this->name ,
                          "popis" => $this->popis ,
                          "count" => $this->count,
                          "min_stav" => $this->min_stav); 
        $database->insert("tovar", $udaje);
    }
    
    public function updateItem(){ //update item into database
        global $database;
        $udaje = array(   
                          "name" => $this->name ,
                          "popis" => $this->popis ,
                          "count" => $this->count,
                          "min_stav" => $this->min_stav); 
        $database->update("tovar", $udaje, "id", $this->id);//ulozime do tabulky item pole ulozene v udaje podla identifikatora id s aktualnou hodnotou
    }
    
    public function getAll(){   //return array containing all variables
        return array("id" => $this->id,
                     "name" => $this->name ,
                     "popis" => $this->popis ,
                     "count" => $this->count,
                     "min_stav" => $this->min_stav);  
    }
    
    public function setName($name){ $this->name = $name;   }
    public function setPopis($desc){ $this->popis = $desc;   }
    public function setCount($c){ $this->count = $c;   }
    public function setMinStav($ms) { $this->min_stav = $ms; }
    
    public function getId(){ return $this->id;   }
    public function getName(){ return $this->name;   }
    public function getPopis(){ return $this->popis;   }
    public function getCount(){ return $this->count;   }
    public function getRegistracnyKod(){ return $this->registracny_kod;   }
    public function getMinStav() { return $this->min_stav; }
    
    public function zistiFixnyParameter($nazov_parametra){
        global $database;
        global $aktivny_sklad;
        $vysl = $database->select("SELECT * FROM  fixne_parametre_tovaru_udaje 
                                   WHERE nazov_polozky='".$nazov_parametra."' 
                                        AND sklad_id = '". $aktivny_sklad->getId() ."'" ) or die (mysql_error());
        $polozka = $database->fetch_array($vysl) or die(mysql_error());
        $this->id = $polozka["id"];
    }
    
    public function zistiCiSomVyrobok() {
        global $database;
        $query = $database->raw_query( "SELECT * FROM tovar_vyrobok WHERE tovar_vyrobok_id = '".$this->id."'" );
        if ( $database->pocet_riadkov($query) >= 1  ) return true;
        else return false;
    }
    
    public function stavNaSklade(){ 
        $navrat = "";
        if ( ($this->count) < ($this->min_stav) ){
            $navrat = "stav='minimalny_stav' ";
        }    
        if ( $this->count < 0){
            $navrat = " stav='zaporny' ";
        }
            
        return $navrat;
    }
}

?>
