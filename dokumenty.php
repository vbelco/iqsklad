<?php
global $database; //spristupni databazove funckie
global $uzivatel; // trieda aktualneho uzivatela
global $aktivny_sklad; //trieda aktivne zvoleneho skladu
// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladn index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}
?>
<h1>Dokumenty </h1>
<a href="index.php?pageaction=dokumenty&typ_dokumentu=prijemky" >Príjemky</a>
<a href="index.php?pageaction=dokumenty&typ_dokumentu=vydajky" >Výdajky</a>
<a href="index.php?pageaction=dokumenty&typ_dokumentu=vyrobky" >Výroba</a>
<div>
<?php
if ( isset( $_REQUEST["typ_dokumentu"] ) ){
    include "dokumenty-zoznam.php";
}//end if isset REQUEST
?>
</div>