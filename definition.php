<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 include "classes/Item.php";
 include "classes/Database.php";   // rozhodovanie ktoru databazovu triedu nacitame
 include "classes/User.php";
 include "classes/Message.php";
 include "classes/Page.php";
 include "classes/Sklad.php";
 include "classes/Prijemka.php";
 include "classes/Vydajka.php";
 include "classes/Vyrobka.php";
 include "classes/Zobrazovac.php";
 include "classes/Vyrobok.php";
 include "classes/Organizacia.php";
 
 include "functions/general.php";
 
 session_start();
 
 global $database;
 //PRODUCTION
 $database  = new Database("46.229.230.242", "ps011500", "ckeqexon", "ps011500db");
 
// DEVELOPING  - PDO
// $dsn = 'mysql:dbname=ps011500db;host=46.229.230.242';
// $user = 'ps011500';
// $passwd = 'ckeqexon';
// $database  = new Database($dsn, $user, $passwd);

 // DEVELOPING - classic mysql
 //$dsn = 'localhost';
 //$dbuser = 'root';
 //$passwd = '';
 //$database_name = 'iqsklad';
 //$database  = new Database($dsn, $dbuser, $passwd, $database_name);
 
 global $uzivatel; //definovanie globalnej premennej repreyentujucej uzivatela
 global $aktivny_sklad;
 if ( is_registered() ){ //uzivatel je prihlaseny
            //natiahnutie udajov o uzivatelovi z databazy
            $uzivatel = new User();
            $uzivatel->loadFromDatabase($_SESSION["user"]); //toto bz som nahradil natiahnutia zo session, bude to rychlejsie
            $aktivny_sklad = najdi_aktivny_sklad(); //nacita do premennej aktivny_sklad vsetkz udaje o sklade
 }
 
 
 
 if ( !isset ($message)){
     global $message;
    $message = new Message(); //definovanie objektu na vypisovanie sprav
 }
 
 global $page;
 $page = new Page(); //definovanie instancie triedy stranky (hlavne presmerovanie)
 
 //definovanie smerovaca na stranky, bud sa naplni poziadavkou od uzivatela, 
 //alebo sa naplni vnutornym presmerovanim po vzkonani operacii
 global $presmerovac;
?>
