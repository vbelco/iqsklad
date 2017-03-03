<?php
global $database; //spristupni databazove funckie
global $uzivatel; // trieda aktualneho uzivatela
global $aktivny_sklad; //trieda aktivne zvoleneho skladu
$organizacia = new Organizacia();
$organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );

// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladn index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}
?>
<ul id="zoznam_skladov">
<?php
//nacitame sklady na prepinac, prednastaveny bude aktivny sklad
$query = $database->select("SELECT * FROM sklad WHERE organizacia_id = '".$organizacia->getID()."'");
while ($vysl = $database->fetch_array($query)){ //prebehne vsetky nacitane sklady uzivatela
    //kontrola na aktualnost skladu
    if ($aktivny_sklad->getId() == $vysl["id"] ){ // pri aktivnom sklade vypiseme len nazov
       echo "<li>".$vysl["name"]."</li>";
    }
    else {
       echo "<li><form><input type='submit' name='submit' value='".$vysl["name"]."' >
                       <input type='hidden' name='sklad_id' value='".$vysl["id"]."' >
                       <input type='hidden' name='action' value='aktivuj_sklad' >    
                       <input type='hidden' name='pageaction' value='items'>
                 </form></li>";
    }
}
?>
</ul>
<h1>Tovar na sklade: "<?php echo $aktivny_sklad->getName(); ?>" </h1>
<?php  if($uzivatel->mamOpravnenie(6) ) {?> <a href="index.php?pageaction=pridaj_tovar" >Prijať tovar na sklad</a> <?php } ?>
<?php  if($uzivatel->mamOpravnenie(8) ) {?> <a href="index.php?pageaction=vyrob_tovar" >Vyrobiť tovar...</a> <?php } ?>
<?php  if($uzivatel->mamOpravnenie(7) ) {?> <a href="index.php?pageaction=vydaj_tovar" >Vydať tovar zo skladu</a> <?php } ?>
<div> 
    <table>
	<thead>
    <?php
    //nacitanie volitelnych poloziek tovaru
    $array_volitelne_parametre = $organizacia->get_volitelne_parametre();
    echo "<tr>";
    echo "<td>Nazov(povinny parameter)</td>";
    echo "<td>Počet</td>";
    echo "<td>Min. stav</td>";
    if( $organizacia->fixny_parameter("description") ) { echo "<td>Popis</td>"; }
    if( $organizacia->fixny_parameter("kod") ) { echo "<td>Registračný kód</td>"; }
    if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
        foreach ($array_volitelne_parametre as $kluc => $parameter){
            echo "<td>".$parameter."</td>";
        }
    }//end if count
    echo "<td></td>"; //pre zachovanie poctu stlpcov
    echo "<td></td>"; //pre zachovanie poctu stlpcov
    echo "<td></td>"; //pre zachovanie poctu stlpcov
    echo "</tr>";
    echo "</thead>";
	echo "<tbody>";
    //nacitanie tovaru v aktualnom sklade
    $query = $database->select("SELECT * FROM tovar WHERE sklad_id = '".$aktivny_sklad->getId()."'". ""
        . "                                       AND aktivny = 1 ORDER BY name ASC");
    while ($vysl = $database->fetch_array($query)){ //prebehne vsetok nacitany tovar
        //zistim ci tento tovar je definovanym vyrobkom
        $tovar = new item;
        $tovar->loadItem($vysl["id"]);
        $som_vyrobok = "";
        //nacitanie priznaku vo tovar je vyrobkom (vyrobok sa sklada z jedneho alebo viacerych tovarov)
        if ( $tovar->zistiCiSomVyrobok() ) {
            $som_vyrobok = "(výrobok)";
        }
        //---------EDITOVANIE vypisanie begin ---------------------
        if ( isset($_REQUEST["action"]) && ($_REQUEST["action"] == "edituj_tovar") && ($_REQUEST["tovar_id"] == $vysl["id"]) ){
        //vypisanie fixnych parametrov
        echo"<tr>";
        echo "<form>";
        echo"<td>";
                $nazov = "sklad_id";
                $pole = vsetky_sklady();
                $vybrana_polozka = $aktivny_sklad->getId();
                generuj_dropdown_box ($nazov, $pole, $vybrana_polozka);
        echo"   <input type='text' name='meno' value='".$tovar->getName()."' />".$som_vyrobok;
        echo"</td>";
        echo"<td><input type='text' name='pocet' value='".$tovar->getCount()."' disabled /></td>";
        echo"<td><input type='text' name='min_stav' value='".$tovar->getMinStav()."'/></td>";
        if( $organizacia->fixny_parameter("description") ) { echo "<td><input type='text' name='popis' value='".$tovar->getPopis()."' /></td>"; }
        if( $organizacia->fixny_parameter("kod") ) { echo "<td><input type='text' name='kod' value='".$tovar->getRegistracnyKod()."' /></td>"; } 
        //vypisanie volitelnych parametrov
        if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
            foreach ($array_volitelne_parametre as $kluc => $parameter){
                //nacitanie parametrov z datab
                $query2=$database->select("SELECT hodnota FROM tovar_volitelne_parametre WHERE tovar_id = '".$vysl["id"]."' "
                                                                              . "AND parameter_tovaru_id = '".$kluc."'");
                $vysl2=$database->fetch_array($query2);
                echo "<td><input type='text' name='".$kluc."' value='".$vysl2["hodnota"]."' /></td>";
            }
        }//end if count()
        echo "<td>";
            echo "<input type='hidden' name='tovar_id' value='".$vysl["id"]."'>";
            echo "<input type='hidden' name='action' value='edituj_tovar_proceed'>";
            echo "<input type='hidden' name='pageaction' value='items'>";
            echo "<input type='submit' name='submit' value='Potvrdť'>";
            echo "</form>";
            echo "<form>";
            echo "<input type='hidden' name='pageaction' value='items'>";
            echo "<input type='submit' name='submit' value='Zruš'>";
            echo "</form>";
        echo "</td>";
        echo "<td>";
            echo "<form>";
            echo "<input type='submit' name='submit' value='Zmaž'>";
            echo "</form>";
        echo "</td>";
        echo "<td>";
            echo "<form>";
            echo "<input type='hidden' name='vyrobok_id' value='".$vysl["id"]."'>";
            echo "<input type='hidden' name='pageaction' value='definuj_vyrobok'>";
            echo "<input type='submit' name='submit' value='Výrobok...'>";
            echo "</form>";
        echo "</td>";
        echo"</tr>";    
    }//end while vypisanie tovaru zo skladu
    //---------editovacie vypisanie end -----------------------
    //---------ZMAZANIE riadku begin - potvrdenie---------------
    else if ( isset($_REQUEST["action"]) && ($_REQUEST["action"] == "zmaz_tovar") && ($_REQUEST["tovar_id"] == $vysl["id"]) ){
    //vypisanie fixnych parametrov
    echo"<tr>";
    echo"<td>".$tovar->getName().$som_vyrobok."</td>";
    echo"<td>".$tovar->getCount()."</td>";
    echo"<td><input type='text' name='min_stav' value='".$tovar->getMinStav()."'/></td>";
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
    echo "<td>";
        echo "<form>";
        echo "<input type='hidden' name='tovar_id' value='".$vysl["id"]."'>";
        echo "<input type='hidden' name='action' value='edituj_tovar'>";
        echo "<input type='hidden' name='pageaction' value='items'>";
        echo "<input type='submit' name='submit' value='Edituj'>";
        echo "</form>";
    echo "</td>";
    echo "<td>";
        echo "Skutočne chcete zmazať tento tovar?"; 
        echo "<form>
              <input type='hidden' name='tovar_id' value='".$vysl["id"]."'>
              <input type='hidden' name='action' value='zmaz_tovar_proceed'>
              <input type='hidden' name='pageaction' value='items'>
              <input type='submit' name='submit' value='Ano'>
              </form>
              <form>
              <input type='hidden' name='pageaction' value='items'>
              <input type='submit' name='submit' value='Nie'>
              </form>";
    echo "</td>";
    echo "<td>";
            echo "<form>";
            echo "<input type='hidden' name='vyrobok_id' value='".$vysl["id"]."'>";
            echo "<input type='hidden' name='pageaction' value='definuj_vyrobok'>";
            echo "<input type='submit' name='submit' value='Výrobok...'>";
            echo "</form>";
        echo "</td>";
    echo"</tr>";
    }
    //---------ZMAZANIE riadku end - potvrdenie---------------
    //---------bezne vypisanie begin ----------------------------
    else {
    //vypisanie fixnych parametrov
    echo"<tr ".$tovar->stavNaSklade()." >"; //podmienen formatovanie na yaporne hodnotz a hodnoty tovaru mensie ako su minimalne hodnoty
    echo"<td>".$tovar->getName().$som_vyrobok."</td>";
    echo"<td>".$tovar->getCount()."</td>";
    echo"<td>".$tovar->getMinStav()."</td>";
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
    echo "<td>";  //pridanie noveho tovaru
        echo "<form>";
        echo "<input type='hidden' name='tovar_id' value='".$vysl["id"]."'>";
        echo "<input type='hidden' name='action' value='edituj_tovar'>";
        echo "<input type='hidden' name='pageaction' value='items'>";
        echo "<input type='submit' name='submit' value='Edituj'>";
        echo "</form>";
    echo "</td>";
    echo "<td>"; // vymazanie tovaru
        echo "<form>";
        echo "<input type='hidden' name='tovar_id' value='".$vysl["id"]."'>";
        echo "<input type='hidden' name='action' value='zmaz_tovar'>";
        echo "<input type='hidden' name='pageaction' value='items'>";
        echo "<input type='submit' name='submit' value='Zmaž'>";
        echo "</form>";
    echo "</td>";
    echo "<td>";
            echo "<form>";
            echo "<input type='hidden' name='vyrobok_id' value='".$vysl["id"]."'>";
            echo "<input type='hidden' name='pageaction' value='definuj_vyrobok'>";
            echo "<input type='submit' name='submit' value='Výrobok...'>";
            echo "</form>";
        echo "</td>";
    echo"</tr>";
    }//end if isset action
    //---------bezne vypisanie end ----------------------------
}
    //kontrola na opravnenie pridavat tovar
    if($uzivatel->mamOpravnenie(24) ) {
?>
    <form name="pridaj_tovar">
    <?php
       //telo formulara
       echo "<tr>";
       echo "<td><input type='text' name='nazov' /> </td>";
       echo "<td><input type='text' name='pocet' disabled /> </td>";
       if( $organizacia->fixny_parameter("description") ) { echo "<td><input type='text' name='description' /> </td>"; }
       if( $organizacia->fixny_parameter("kod") ) { echo "<td><input type='text' name='kod' /> </td>"; }
       //volitelne parametre
       if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
        foreach ($array_volitelne_parametre as $kluc => $parameter){
               echo "<td><input type='text' name='".$kluc."' /> </td>";
        }
       }//end if count
    ?>
        <input type="hidden" name="pageaction" value="items" />
        <input type="hidden" name="action" value="pridaj_tovar" />
        <td><input type="submit" name="submit" value="Pridaj"/> </td>
        <td></td>
        </tr>
    </form>
<?php 
    } // end if kontrola opravnenia uzivatela pridavat novy tovar
?>
	</tbody>    
    </table>
</div>