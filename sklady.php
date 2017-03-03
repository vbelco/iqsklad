<?php
// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladn index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}
?>
<h1>Vaše sklady</h1>
<a href="index.php?pageaction=pridaj_sklad" >Pridaj sklad</a>
<div >
<?php
//vypisanie aktualne definovanych skladov pre uzivatela
global $database; //spristupni databazove funckie
global $uzivatel; // trieda aktualneho uzivatela

$sklady = array(); //pole skladov
$i = 0;
$query = $database->select("SELECT * FROM sklad WHERE organizacia_id = '".$uzivatel->getIdOrganizacie()."'");
?>
    <table>
        <tr>
            <th></th>
            <th>Názov skladu</th>
            <th>Popis skladu</th>
        </tr>
<?php
while ($vysl = $database->fetch_array($query)){ //prebehne vsetky nacitane sklady uzivatela
    $sklady[$i] = new Sklad();
    $sklady[$i]->nacitaj($vysl["id"]); //nacitanie udajov skladu z databazy
    $i++;
}
foreach($sklady as $sklad){ //zbehne cele pole skladov
    echo "<tr>";
                  //prva bunka je signalizacia ci je sklad aktivne vybrany
                  echo "<td>";
                  if ( $sklad->is_active() ){ //sklad je aktivny
                      echo "aktívny";    
                  } else{
                      echo "<form>";
                      echo "<input type='hidden' name='sklad_id' value='".$sklad->getId()."'>";    
                      echo "<input type='hidden' name='action' value='aktivuj_sklad'>";
                      echo "<input type='hidden' name='pageaction' value='sklady'>";
                      echo "<input type='submit' name='submit' value='Aktivuj'>";
                      echo "</form>";
                  }
                  echo "</td>";
                  // ak je editovanie tak vypise formular na upravy skladu, inak vypise len parametre skladu
                   if ( isset($_REQUEST["action"]) && 
                        ($_REQUEST["action"] == "edituj_sklad") && 
                        ($_REQUEST["sklad_id"] == $sklad->getId()) 
                       ){
                      echo "<form>
                            <td>
                               <input type='text' name='meno_skladu' value='".$sklad->getName()."'>
                            </td>
                            <td>
                               <input type='text' name='popis_skladu' value='".$sklad->getDescription()."'>    
                            </td>
                               <input type='hidden' name='sklad_id' value='".$sklad->getId()."'>    
                               <input type='hidden' name='action' value='edituj_sklad_proceed'>
                               <input type='hidden' name='pageaction' value='sklady'>
                            <td>
                                <input type='hidden' name='pageaction' value='sklady'>
                                <input type='submit' name='submit' value='Potvrď'>
                            
                            </form>
                            
                            <form>
                                <input type='hidden' name='pageaction' value='sklady'>
                                <input type='submit' name='submit' value='Zruš'>
                            </form>
                            </td>
                            ";  
                   } else { //vypisanie riadku skladu
                      echo "    <td>".$sklad->getName()."</td>";  
                      echo "    <td>".$sklad->getDescription()."</td>";
                      echo "    <td>
                                    <form>
                                    <input type='hidden' name='sklad_id' value='".$sklad->getId()."'>
                                    <input type='hidden' name='action' value='edituj_sklad'>
                                    <input type='hidden' name='pageaction' value='sklady'>
                                    <input type='submit' name='submit' value='Edituj'>
                                    </form> 
                                </td>";
                   }
    echo "    <td>";
                  if(//tuna je otazka o potvrdeni zmazania tohoto skladu
                        isset($_REQUEST["action"]) && 
                        ($_REQUEST["action"] == "zmaz_sklad") && 
                        ($_REQUEST["sklad_id"] == $sklad->getId())
                         ){
                           echo "Skutočne chcete zmazať tento sklad?"; 
                           echo "<form>
                                    <input type='hidden' name='sklad_id' value='".$sklad->getId()."'>
                                    <input type='hidden' name='action' value='zmaz_sklad_proceed'>
                                    <input type='hidden' name='pageaction' value='sklady'>
                                    <input type='submit' name='submit' value='Ano'>
                                 </form>
                                 <form>
                                    <input type='hidden' name='pageaction' value='sklady'>
                                    <input type='submit' name='submit' value='Nie'>
                                 </form>";
                      }//end if
                   else{
                      echo"<form>
                              <input type='hidden' name='sklad_id' value='".$sklad->getId()."'>
                              <input type='hidden' name='action' value='zmaz_sklad'>
                              <input type='hidden' name='pageaction' value='sklady'>
                              <input type='submit' name='submit' value='Zmaž'>
                           </form>";    
                   }  
    echo"      </td>";
    echo "</tr>";
}
?>
    </table>
</div>