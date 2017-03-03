<?php
include "definition.php";//zakladne nastavenia

if ( isset($_REQUEST["action"]) ){
    switch ($_REQUEST["action"]){
        //zaradi tovar do aktualnej prijemky
        case "aktualizuj_tovar_prijemky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualny zoznam tovaru v aktualnej prijemke
            $tovar_id = $_REQUEST["item_id"];
            $pocet = $_REQUEST ["pocet"]; // tu by mali bzt kontrolz na vstup, teda ci bolo zadane cislo a nie nejake bludy
            if ( !isset ($_SESSION["zoznam_poloziek"][$tovar_id] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["zoznam_poloziek"][$tovar_id] = "";
            }
            $_SESSION["zoznam_poloziek"][$tovar_id] += $pocet; //ulozenie hodnoty tovaru do session 
            echo $_SESSION["zoznam_poloziek"][$tovar_id];
        break;  
        
        //zaradi tovar do aktualnej vydajky
        case "aktualizuj_tovar_vydajky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualny zoznam tovaru v aktualnej vydajke
            $tovar_id = $_REQUEST["item_id"];
            $pocet = $_REQUEST ["pocet"]; // tu by mali bzt kontrolz na vstup, teda ci bolo zadane cislo a nie nejake bludy
            if ( !isset ($_SESSION["zoznam_poloziek_vydajka"][$tovar_id] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["zoznam_poloziek_vydajka"][$tovar_id] = "";
            }
            $_SESSION["zoznam_poloziek_vydajka"][$tovar_id] += $pocet; //ulozenie hodnoty tovaru do session 
            echo $_SESSION["zoznam_poloziek_vydajka"][$tovar_id];
        break;
        
        //zaradi tovar do aktualnej vyroby
        case "aktualizuj_tovar_vyrobky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualny zoznam tovaru v aktualnej vydajke
            $tovar_id = $_REQUEST["item_id"];
            $pocet = $_REQUEST ["pocet"]; // tu by mali bzt kontrolz na vstup, teda ci bolo zadane cislo a nie nejake bludy
            if ( !isset ($_SESSION["zoznam_poloziek_vyrobka"][$tovar_id] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["zoznam_poloziek_vyrobka"][$tovar_id] = "";
            }
            $_SESSION["zoznam_poloziek_vyrobka"][$tovar_id] += $pocet; //ulozenie hodnoty tovaru do session 
            echo $_SESSION["zoznam_poloziek_vyrobka"][$tovar_id];
        break;
        
        case "aktualizuj_poznamku_prijemky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualnu poznamku prijemky
            $poznamka = $_REQUEST["poznamka"];
            if ( !isset ($_SESSION["prijemka_poznamka"] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["prijemka_poznamka"] = "";
            }
            $_SESSION["prijemka_poznamka"] = $poznamka; //ulozenie hodnoty tovaru do session 
            echo $_SESSION["prijemka_poznamka"];
        break;
        
        case "aktualizuj_poznamku_vyrobky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualnu poznamku prijemky
            $poznamka = $_REQUEST["poznamka"];
            if ( !isset ($_SESSION["vyrobka_poznamka"] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["vyrobka_poznamka"] = "";
            }
            $_SESSION["vyrobka_poznamka"] = $poznamka; //ulozenie hodnoty tovaru do session 
            echo $_SESSION["vyrobka_poznamka"];
        break;
    }
}
