<?php

/*
 * trieda urcena na manipulaciu so strankami, hlavne presmerovanie a dalsie potrebne veci
 */

/**
 * Description of Page
 *
 * @author Viktor
 */
class Page {
    public function redirect($page){
       if ( isset($page) ){ //ak je zaday paranete $page tak potom presmeruje na tuto stranku
            $retaz = "";
            $pom = explode('/', $_SERVER["PHP_SELF"], -1); //odrezanie nazvu skriptu
            foreach ($pom as $cast){
                $retaz .= $cast; 
            }
            $current = "Location: http://".$_SERVER['SERVER_NAME']."/".$retaz."/".$page;  
        }
        else { //ked nieje zadany parameter $page tak presmeruje na index.php
            $retaz = "";
            $pom = explode('/', $_SERVER["PHP_SELF"], -1); //odrezanie nazvu skriptu
            foreach ($pom as $cast){
                $retaz .= $cast; 
            }
            $current = "Location: http://".$_SERVER['SERVER_NAME']."/".$retaz."/index.php";
        }
        header($current);     
    }
    
    
}

?>
