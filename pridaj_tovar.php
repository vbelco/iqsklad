<?php

// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladny index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}

global $database; //spristupni databazove funckie
global $uzivatel; // trieda aktualneho uzivatela
global $aktivny_sklad; //trieda aktivne zvoleneho skladu
$organizacia = new Organizacia();
$organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );

?>
<h1>Skladová príjemka číslo:<?php echo $organizacia->getCisloNovejPrijemky(); ?></h1>
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
                       <input type='hidden' name='action' value='aktivuj_sklad' >    
                       <input type='hidden' name='pageaction' value='pridaj_tovar'>
                 </form></li>";
    }
}
?>
</ul>
<div>
        Poznámka:
        <button type='button' onclick='zavolajAjaxFunkciuPridajPoznamkuPrijemky()'>Zaraď...</button>
        <textarea  id="textarea_poznamka_prijemky" name='poznamka_prijemky'></textarea>
        <div id="poznamka_prijemky"></div>
        <script>
            function zavolajAjaxFunkciuPridajPoznamkuPrijemky(){
               var poznamka = document.getElementById('textarea_poznamka_prijemky').value;
               var url = 'route_ajax.php?action=aktualizuj_poznamku_prijemky_proceed&poznamka=' + poznamka;
               ajaxFunkcie(url,aktualizujPoznamkuPrijemky);
            }
            function aktualizujPoznamkuPrijemky(xhttp) {
                document.getElementById('poznamka_prijemky').innerHTML = xhttp.responseText;
            }     
        </script>
</div> 
<div>
    <table>
    <?php
    //nacitanie volitelnych poloziek tovaru
    $array_volitelne_parametre = $organizacia->get_volitelne_parametre();

    //zobrazime formular na vlozenie noveho tovaru s parametrami definovanymi v datab.
       //hlavicka formulara
       echo "<tr>";
       echo "<td></td>";
       echo "<td><b>V príjemke</b></td>";
       echo "<td>Počet</td>";
       echo "<td>Nazov</td>";
       echo "<td>Akt. stav</td>";
       if( $organizacia->fixny_parameter("description") ) { echo "<td>Popis</td>"; }
       if( $organizacia->fixny_parameter("kod") ) { echo "<td>Registračný kód</td>"; } 
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
    //zadefinovanie aktualneho poctu v prijemke
    $aktualny_pocet = isset( $_SESSION["zoznam_poloziek"][ $vysl["id"] ] ) ? $_SESSION["zoznam_poloziek"][ $vysl["id"] ] : ""; //kvoli deklaracie premennej, aby nevypisoval hlasnu o nedeklarovani
    echo"<tr>";
    echo "<td><button type='button' onclick='zavolajAjaxFunkciu_".$vysl["id"]."()'>Zaraď...</button></td>";
    echo "<td><b><div id='pocet_".$vysl["id"]."'>".$aktualny_pocet."</div></b></td>";
    echo "<td><input  id=\"inputPocet_".$vysl["id"]."\" type='text' name='pocet' value='' size='4'></td>";//hodnota je z aktualnej prijemky
    //nacitanie udajov 
    echo "<script> "
    . "     
            function zavolajAjaxFunkciu_".$vysl["id"]."(){
               var pocet = document.getElementById('inputPocet_".$vysl["id"]."').value;
               var url = 'route_ajax.php?action=aktualizuj_tovar_prijemky_proceed&item_id=".$vysl["id"]."&pocet=' + pocet;
               ajaxFunkcie(url,aktualizujTovarPrijemky_".$vysl["id"].")
            }
            function aktualizujTovarPrijemky_".$vysl["id"]."(xhttp) {
                document.getElementById('pocet_".$vysl["id"]."').innerHTML = xhttp.responseText;
            }     
         </script>";
    
    echo"<td>".$vysl["name"]."</td>";
    echo"<td>".$vysl["count"]."</td>";
    if( $organizacia->fixny_parameter("description") ) { echo "<td>".$vysl["popis"]."</td>"; }
    if( $organizacia->fixny_parameter("kod") ) { echo "<td>".$vysl["kod"]."</td>"; } 
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
        <input type='hidden' name='action' value='zapis_tovar_prijemky_proceed'>
        <input type='hidden' name='pageaction' value='items'>
        <input type="submit" name="submit" value="Zapíš príjemku" >
    </form>
    <form>
        <input type='hidden' name='action' value='zrus_tovar_prijemky_proceed'>
        <input type='hidden' name='pageaction' value='items'>
        <input type="submit" name="submit" value="Zruš" >
    </form>
</div>