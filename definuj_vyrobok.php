<?php
global $database; //spristupni databazove funckie
global $uzivatel; // trieda aktualneho uzivatela
global $aktivny_sklad; //trieda aktivne zvoleneho skladu
// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladny index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}
$vyrobok_id = $_REQUEST["vyrobok_id"];

$vyrobok = new Item(); //deklaracia tovaru
$vyrobok->loadItem($vyrobok_id); //nahratie informacii z databazy o tovare

?>
<h1>Definovanie výrobku:<?php echo $vyrobok->getName(); ?></h1>
<ul id="zoznam_skladov">
<?php
//nacitame sklady na prepinac, prednastaveny bude aktivny sklad
$query = $database->select("SELECT * FROM sklad WHERE organizacia_id = '".$uzivatel->getIdOrganizacie()."'");
while ($vysl = $database->fetch_array($query)){ //prebehne vsetky nacitane sklady uzivatela
    //kontrola na aktualnost skladu
    if ($aktivny_sklad->getId() == $vysl["id"] ){ // pri aktivnom sklade vypiseme len nazov
       echo "<li>".$vysl["name"]."</li>";
    }
    else {
       echo "<li><form><input type='submit' name='submit' value='".$vysl["name"]."' >
                       <input type='hidden' name='sklad_id' value='".$vysl["id"]."' >
                       <input type='hidden' name='vyrobok_id' value='".$vyrobok->getId()."' >    
                       <input type='hidden' name='action' value='aktivuj_sklad' >    
                       <input type='hidden' name='pageaction' value='definuj_vyrobok'>
                 </form></li>";
    }
}
?>
</ul>
<?php
//pokial uz bol definovany ako vyrobok predtym, nahrame jeho uz v databaze ulozene udaje, ale len ked este nemame aktivovane session
if ( !(isset ($_SESSION["zoznam_poloziek_definicia"]))  &&  $vyrobok->zistiCiSomVyrobok() ){
    $vyrobok = new Vyrobok(); //premazanie udajov
    $vyrobok->loadItem($vyrobok_id); //nahra zakladne udaje o vyrobku
    $vyrobok->loadTovarFromDatabase(); //nahra tovary do vyrobku
    $vyrobok->nahrajPolozkyDoSession("zoznam_poloziek_definicia"); //nahra uz predtym zvolene udaje do Session
    ?>
    <h2>Aktualna definicia vyrobku:</h2>
    <table>
    <?php
    $pole_tovar = $vyrobok->getPoleTovarovMena();
    foreach ( $pole_tovar as $id_tovaru => $pole){
    echo "<tr><td>".$pole["sklad"]." </td><td>-></td><td>".$pole["meno"]." </td><td>:</td><td> ".$pole["pocet"]."</td></tr>";
    }
    ?>
    </table>
<?php
}//end if
else{ //zatial tovar nema zapisanu definiciu ako vyrobok
?>
    <div>Tento tovar zatiaľ nemá definíciu výrobku!</div>
<?php
}
?>
<div> V nasledujúcom formulári definujte tovar, z ktorého sa skladá predmetný výrobok.</div>
<div>
    <table>
    <form name="definuj_vyrobok">
    <?php
    //nacitanie volitelnych poloziek tovaru
    $array_volitelne_parametre = $aktivny_sklad->get_volitelne_parametre();

    //zobrazime formular na vlozenie noveho tovaru s parametrami definovanymi v datab.
       //hlavicka formulara
       echo "<tr>";
       echo "<td></td>";
       echo "<td><b>V definícii</b></td>";
       echo "<td>Množstvo</td>";
       echo "<td>Názov</td>";
       echo "<td>Počet</td>";
       if( $aktivny_sklad->fixny_parameter("description") ) { echo "<td>Popis</td>"; }
       if( $aktivny_sklad->fixny_parameter("kod") ) { echo "<td>Registračný kód</td>"; } 
       if ( isset($array_volitelne_parametre) ){
        foreach ($array_volitelne_parametre as $kluc => $parameter){
               echo "<td>".$parameter."</td>";
        }
       }//end if isset
       echo "<td></td>";
       echo "</tr>";
       //telo formulara
        
//nacitanie tovaru v aktualnom sklade
$query = $database->select("SELECT * FROM tovar WHERE sklad_id = '".$aktivny_sklad->getId()."'". ""
        . "                                       AND aktivny = 1 ORDER BY name ASC");
while ($vysl = $database->fetch_array($query)){ //prebehne vsetok nacitany tovar 
    //vypisanie fixnych parametrov
    $aktualny_pocet = isset( $_SESSION["zoznam_poloziek_definicia"][ $vysl["id"] ] ) ? $_SESSION["zoznam_poloziek_definicia"][ $vysl["id"] ] : ""; //kvoli deklaracie premennej, aby nevypisoval hlasnu o nedeklarovani
    echo"<tr>";
    echo "<form>";
    echo "<td><input type='submit' name='submit' value='Zaraď'></td>";
    echo "<td><b>".$aktualny_pocet."</b></td>";
    echo "<td><input type='text' name='pocet' value='' size='4'></td>";//hodnota je z aktualnej prijemky
    echo "<input type='hidden' name='vyrobok_id' value='".$vyrobok->getId()."' >"; 
    echo "<input type='hidden' name='item_id' value='".$vysl["id"]."'>";
    echo "<input type='hidden' name='action' value='aktualizuj_definiciu_proceed'>";
    echo "<input type='hidden' name='pageaction' value='definuj_vyrobok'>";
    echo "</form>";
    echo"<td>".$vysl["name"]."</td>";
    echo"<td>".$vysl["count"]."</td>";
    if( $aktivny_sklad->fixny_parameter("description") ) { echo "<td>".$vysl["popis"]."</td>"; }
    if( $aktivny_sklad->fixny_parameter("kod") ) { echo "<td>".$vysl["kod"]."</td>"; } 
    //vypisanie volitelnych parametrov
    if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
        foreach ($array_volitelne_parametre as $kluc => $parameter){
            //nacitanie parametrov z datab
            $query2=$database->select("SELECT hodnota FROM tovar_volitelne_parametre WHERE tovar_id = '".$vysl["id"]."' "
                                                                          . "AND parameter_tovaru_id = '".$kluc."'");
            $vysl2=$database->fetch_array($query2);
            echo "<td>".$vysl2["hodnota"]."</td>";
        }
    }//end if count()
    echo"</tr>";
    }//end if isset action
    //---------bezne vypisanie end ----------------------------
?>
    </table>
    <form>
<?php
        //budeme variantne bud zapisovat novu definiciu alebo budemem opravovat uz existujucu definiciu
        if ( $vyrobok->zistiCiSomVyrobok() ){
            echo "<input type='hidden' name='action' value='update_definiciu_proceed'>"; //menime existujucu
        } else {
            echo "<input type='hidden' name='action' value='zapis_definiciu_proceed'>"; // pridavame novu
        }
?>
        <input type='hidden' name='vyrobok_id' value='<?php echo $vyrobok->getId(); ?>' > 
        <input type='hidden' name='pageaction' value='items'>
        <input type="submit" name="submit" value="Zapíš definíciu" >
    </form>
    <form>
        <input type='hidden' name='action' value='zrus_definiciu_proceed'>
        <input type='hidden' name='pageaction' value='items'>
        <input type="submit" name="submit" value="Zruš" >
    </form>
</div>