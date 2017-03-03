<?php

/*
 * Database class for communicating with database
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author Viktor Belko <vbelco@hotmail.sk>
 */
class Database extends PDO {
    private $link;

    function __construct($dsn, $user, $password) {
       try {
              $link = new PDO($dsn, $user, $password);
       } catch (PDOException $e) {
           echo 'Connection failed: ' . $e->getMessage();
       }
    
    }//end function construct

    public function select ($query){
        // Perform Query
        try {
          $result = $link->query($query);
        }
        catch (PDOException $e) {
          echo "invalid query:" . $e->getMessage(); 
        }

        return $result; //return pointer to select query 
    }//end function select
    
    public function insert($table, $array_values){ //inserts vaiables into table
        $sql_query = "INSERT INTO ".$table." ( ";
        $sql_end = "";
        $i=1; $count = count($array_values);  //pomocne cisleniky
        foreach ($array_values as $key => $value){
            $sql_query .= $key;
            if ($value == "NOW()") { $sql_end .= $value; } //toto je kvoli tomu aby funkcia NOW nebola ohranicena uvodyovkami abz sa spravne vzkonala na serveri
            else { $sql_end .= '"'.$value.'"'; }
            if ($i != $count ){ //abz sa ciarka pridala za kazdym ale nie za poslednym
                $sql_query .=",";
                $sql_end .= ",";
                $i++;
            }
        }
        $sql_query .= " ) VALUES( ".$sql_end." )";
        
        try {
          $result = $link->query($sql_query);
        }
        catch (PDOException $e) {
          echo "invalid query:" . $e->getMessage(); 
        }
        
        return true; //return pointer to select query 
    }//end insert function
    
    public function update($table, $array_values, $identifikator_name, $identifikator_value){
        $sql_query= "UPDATE ".$table." SET ";
        $i=1; $count = count($array_values);  //pomocne cisleniky
        foreach ($array_values as $key => $value){
            if ($value == "NOW()") { $sql_query .= $key. "=".$value; } // kvoli tomu aby funckia NOW() nebola ohranicena uvodzovkami
            else { $sql_query .= $key. "='".$value."'"; }
            if ($i != $count ){ //aby sa ciarka pridala za kazdym ale nie za poslednym
                $sql_query .=", ";                
                $i++;
            }
        }
        $sql_query .= " WHERE ".$identifikator_name." = '".$identifikator_value."'";
        
        try {
          $result = $link->query($sql_query);
        }
        catch (PDOException $e) {
          echo "invalid query:" . $e->getMessage(); 
        }
        return true; //return pointer to select query 
    }
    
    public function delete($table, $id){
       // Perform Query
       try {
          $result = $link->query("DELETE FROM ".$table." WHERE id='".$id."'");
        }
        catch (PDOException $e) {
          echo "invalid query:" . $e->getMessage(); 
        }
        
        return $result; //return pointer to select query     
    }
    
    public function getInsertId(){
        return $link->lastInsertId();
    }
    
    public function close(){
        $link = null;
    }
    
    public function raw_query ($query){
        // Perform Query
        try {
          $result = $link->query($query);
        }
        catch (PDOException $e) {
          echo "invalid query:" . $e->getMessage(); 
        }
        return $result; //return pointer to select query 
    }//end function select
    
    public function pocet_riadkov ($zdroj){
        return $zdroj->fetchColumn(); 
    }
    
    public function fetch_array($zdroj){
        return $zdroj->fetchAll();
    }
    
}//end class Database

?>
