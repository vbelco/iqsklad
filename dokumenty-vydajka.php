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
<h2>Výdajky</h2>
<?php
//vytvori starndartny zobrazovac, s prednastavenymi hodnotami
// zoradenie podla datumu od najnovsieho, pocet riadkov na stranku je 20
$zobrazovac = new Zobrazovac(); //vytvori starndartny zobrazovac rep prijemky, ide aj o NAZOV DATAB TABULKY
$zobrazovac->nacitajStranku("vydajky");
$zobrazovac->zobrazDokumenty();
?>