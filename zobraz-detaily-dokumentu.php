<?php
global $database; //spristupni databazove funckie
global $uzivatel; // trieda aktualneho uzivatela
global $aktivny_sklad; //trieda aktivne zvoleneho skladu
// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladn index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}

//nacitanie dokmentu
$dokument = new Zobrazovac();
$dokument->nacitajDetaily( $_REQUEST["typ_dokumentu"] , $_REQUEST["dokument_id"] );

?>
<h2>Detaily dokumentu <?php echo $dokument->getTypDokumentu()." ".$dokument->getCisloDokumentov(); ?> : </h2>
<div>
<a href="index.php?pageaction=dokumenty&typ_dokumentu=<?php echo $_REQUEST["typ_dokumentu"]; ?>" >Naspäť</a>
</div>
<?php
echo "Dátum vytvorenia:". $dokument->getDokument()->getDatumVytvorenia()."<br>";
echo "Poznámka: <br>".$dokument->getDokument()->getPoznamka();
echo "<h3>Zoznam položiek: </h3>";
$dokument->ZobrazDetailyDokumentu();

?>
