<?php
  class Sklad {
      private $id;
      private $name;
      private $description;
      private $aktivny;
      private $fixne_parametre;
      private $parametre;

      public function nacitaj($idSkladu){
          global $database;
          global $message;
          $result = $database->select("SELECT * FROM sklad WHERE id='".$idSkladu."'") or die(mysql_error());
          $polozka = $database->fetch_array($result);
          $this->id = $polozka["id"];
          $this->name = $polozka["name"];
          $this->description = $polozka["description"];
          $this->aktivny = $polozka["aktivny"];
         //nacitanie fixnzch parametrov
          $vysl2 = $database->select("SELECT * FROM fixne_parametre_tovaru WHERE sklad_id='".$idSkladu."'") or die(mysql_error());
          $i=0;
          while ( $polozka2 = $database->fetch_array($vysl2) ) {
              $this->fixne_parametre[$i] = $polozka2["nazov_parametra"];
              $i++;
          }
          //nacitanie volitelnzch parametrov
          $vysl3 = $database->select("SELECT * FROM parametre_tovaru WHERE sklad_id='".$idSkladu."'") or die(mysql_error());
          while ( $polozka3 = $database->fetch_array($vysl3) ) {
              $this->parametre[ $polozka3["id"] ] = $polozka3["nazov"];
          }
      }
      
      public function createInDatabase(User $uzivatel){
          global $database;
          $organizacia_id = $uzivatel->getIdOrganizacie(); 
          $array_data = array(
                                "name" => $this->name,
                                "description" => $this->description,
                                "organizacia_id" => $organizacia_id,
                              );
            $database->insert("sklad", $array_data);
            $this->id = $database->getInsertId();
      }
      
      public function updateInDatabase(){
          global $database;
          $array_data = array(  "name" => $this->name,
                                "description" => $this->description,
                              );
          if ( $database->update("sklad", $array_data, "id", $this->id ) ){
                return true;
          }      
      }
      
      public function deleteInDatabase(){
          global $database;
          if ( $database->delete("sklad", $this->id ) ){
                return true;
            }
      }

      public function setName($name){
          $this->name = $name;
      }
      public function getName(){
          return $this->name;
      }
      
      public function setDescription($desc){
          $this->description = $desc;
      }
      public function getDescription(){
          return $this->description;
      }
      
      public function checkName(){
        global $message;
        if (!strlen($this->name)){
            $message->add("Nazov skladu nesmie byt prazdny retazec");
            return false;
        }
        else return true;
      }
      public function checkDescription(){
        global $message;
        if (!strlen($this->description)){
            $message->add("Popis skladu nesmie byt prazdny retazec");
            return false;
        }
        else return true;
      }
      
      public function getId(){
          return $this->id;
      }
      public function setId($id){
          $this->id = $id;
      }
      
      public function activate(){
          /*
           * aktivuje sklad v databaye ako aktivny
           */
         global $database;
         $array = array("aktivny" => true);
         $database->update("sklad", $array, "id", $this->id);
      }
      
      public function deactivate(){
          /*
           * aktivuje sklad v databaye ako aktivny
           */
         global $database;
         $array = array("aktivny" => false);
         $database->update("sklad", $array, "id", $this->id);
      }
      
      public function is_active(){
          /*
           * aktivuje sklad v databaye ako aktivny
           */
         global $database;
         $vysl = $database->select("SELECT * FROM sklad WHERE id='".$this->id."'") or die(mysql_error());
         $polozka = $database->fetch_array($vysl) or die(mysql_error());
         if ( $polozka["aktivny"] == 1){
             return true;
         } else{
             return false;
         }
      }//end function is_active
      
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
          $database->raw_query("DELETE FROM fixne_parametre_tovaru WHERE sklad_id= '".$this->id."'
                                                                   AND nazov_parametra = '".$nazov_fix_parametra."'");
      }
      
      public function add_fixny_parameter ($nazov_fix_parametra){
          /*
           * prida fixny parameter pre tento sklad
           * vstup> nazov fixneho parametra
           */
          global $database;
          // vlozi do databazy polozku
          $hodnoty = array("sklad_id" =>  $this->id,
                           "nazov_parametra" =>  $nazov_fix_parametra   );
          $database->insert( "fixne_parametre_tovaru", $hodnoty );
          $insert_id = $database->getInsertId();
          //vlozi do pola parametrov tento novy
          $this->parametre[$insert_id] = $nazov_fix_parametra;
          
          //if( !count($this->fixne_parametre) ){ //pokial este nemame ziadne hodnoty v poli
          //    $this->fixne_parametre[0] = $nazov_fix_parametra;
          // }
          //else {
          //  array_push($this->fixne_parametre, $nazov_fix_parametra); //prida do objektu do pola fixnych parametrov
          //}
          
      }
      
      public function add_volitelny_parameter ($nazov_volitelneho_parametra){
          /*
           * prida volitelny parameter pre tento sklad
           * vstup> nazov parametra
           */
          global $database;
          //este vlozi do databazy polozku
          $hodnoty = array(  "sklad_id" => $this->id,
                            "nazov" =>  $nazov_volitelneho_parametra   
                           );
          $database->insert( "parametre_tovaru", $hodnoty );
          //vlozenie noveho parametra do objektu
          $this->parametre[ $database->getInsertId() ] = $nazov_volitelneho_parametra;
          
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
          return $this->parametre;
      }
      
      public function set_volitelny_parameter( $parameter_id, $nazov_parametra ) {
          /*
           * zmeni volitelny parameter
           */
           $this->parametre[$parameter_id] = $nazov_parametra;
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
          
          unset($this->parametre[$parameter_id]); //vymazanie parametra z databzy
          return true;
      }
  }   