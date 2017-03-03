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
class Database {
    private $link;

    function __construct($server, $user, $passwd, $database) {
        $link = mysql_connect($server, $user, $passwd);
        mysql_query("SET NAMES 'UTF8'"); //nastavenie kodovanie pre spojenie
        if (!$link) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($database) or die("Cannot load database ".mysql_error());
    }//end function construct

    public function select ($query){
        // Perform Query
        $result = mysql_query($query);

        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
        die($message);
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
        $result = mysql_query($sql_query) or die(mysql_error());
        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql_query;
        die($message);
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
        
        $result = mysql_query($sql_query); //vykonanie sql dotazu
        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql_query;
        die($message);
        }
        return true; //return pointer to select query 
    }
    
    public function delete($table, $id){
       // Perform Query
        $result = mysql_query("DELETE FROM ".$table." WHERE id='".$id."'");

        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
        die($message);
        }
        return $result; //return pointer to select query     
    }
    
    public function getInsertId(){
        return mysql_insert_id();
    }
    
    public function close(){
        mysql_close();
    }
    
    public function raw_query ($query){
        // Perform Query
        $result = mysql_query($query);

        // Check result
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
        die($message);
        }
        return $result; //return pointer to select query 
    }//end function select
    
    public function pocet_riadkov ($zdroj){
        return mysql_num_rows($zdroj);
    }
    
    public function fetch_array($zdroj){
        return mysql_fetch_array($zdroj);
    }
    
}//end class Database

?>
