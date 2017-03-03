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
<h2>Výroba</h2>
