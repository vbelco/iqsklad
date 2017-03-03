<?php
// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladn index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}
?>
<h1>Nastavenia</h1>
<a href="index.php?pageaction=settings&subpageaction=kontaktne_udaje" >Kontaktné údaje</a>
<a href="index.php?pageaction=settings&subpageaction=nastavenie_tovaru" >Nastavenie tovaru</a>
<a href="index.php?pageaction=settings&subpageaction=nastavenie_dokumentov" >Nastavenie dokumentov</a>
<a href="index.php?pageaction=settings&subpageaction=nastavenie_opravneni" >Nastavenie opravneni</a>
<div >
<?php
 if ( isset($_REQUEST["subpageaction"]) ){
     switch($_REQUEST["subpageaction"]){
         
         case "nastavenie_tovaru":
             global $uzivatel;
             $organizacia = new Organizacia();
             $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
?>
             <h2>Nastavenie tovaru</h2>
             <div>Nastavte položky, ktoré budete potrebovať pri evidencii v aktualnom sklade</div>
             <h3>Preddefinované položky</h3>
             <form>
                 <input type="checkbox" name="description" value="1" <?php if( $organizacia->fixny_parameter("description") ){ echo "checked";} ?> > Popis <br>
                 <input type="checkbox" name="kod" value="1" <?php if( $organizacia->fixny_parameter("kod") ){ echo "checked";} ?> > Registračný kód <br>
                 <input type="hidden" name="pageaction" value="settings">
                 <input type="hidden" name="subpageaction" value="nastavenie_tovaru">
                 <input type="hidden" name="action" value="nastavenie_tovaru_fixne_proceed">
                 <input type="submit" name="submit" value="Potvrď">
             </form>
             
             <h3>Voliteľné položky</h3>
             <div>Pridajte vlastné položky pre tovar v sklade</div>
             <form>
                 <input type="text" name='nazov'>
                 <input type="hidden" name="pageaction" value="settings">
                 <input type="hidden" name="subpageaction" value="nastavenie_tovaru">
                 <input type="hidden" name="action" value="nastavenie_tovaru_pridaj_volitelne_proceed">
                 <input type="submit" name="submit" value="Pridaj">
             </form>
             <table>
                 <?php
                $array_volitelne_parametre = $organizacia->get_volitelne_parametre();
                
                if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
                    foreach ( $organizacia->get_volitelne_parametre() as $key => $volitelny_parameter ){
                       echo "<tr>";  
                         // ak je editovanie tak vypise formular na uprav nazvu parametra, inak vypise len parametre skladu
                       if ( isset($_REQUEST["action"]) && 
                            ($_REQUEST["action"] == "zmen_volitelny_parameter_formular") && 
                            ($_REQUEST["parameter_id"] == $key ) 
                           ){
                          echo "<form>
                                <td>
                                   <input type='text' name='nazov_parametra' value='".$volitelny_parameter."'>
                                </td>
                                   <input type='hidden' name='parameter_id' value='".$key."'>    
                                   <input type='hidden' name='action' value='zmen_volitelny_parameter_proceed'>
                                <td>
                                    <input type='hidden' name='pageaction' value='settings'>
                                    <input type='hidden' name='subpageaction' value='nastavenie_tovaru'>
                                    <input type='submit' name='submit' value='Potvrď'>                            
                                </form>
                                <form>
                                    <input type='hidden' name='pageaction' value='settings'>
                                    <input type='hidden' name='subpageaction' value='nastavenie_tovaru'>
                                    <input type='submit' name='submit' value='Zruš'>
                                </form>
                                </td>
                                ";  
                       } else { //vypisanie riadku skladu
                         echo "   <td> ".$key." | ".$volitelny_parameter." </td>
                                  <td>
                                  <form>
                                     <input type='hidden' name='parameter_id' value='".$key."'>
                                     <input type='hidden' name='action' value='zmen_volitelny_parameter_formular'>
                                     <input type='hidden' name='pageaction' value='settings'>
                                     <input type='hidden' name='subpageaction' value='nastavenie_tovaru'>
                                     <input type='submit' name='submit' value='Edituj'>
                                  </form>
                                  </td>";
                       };
                       echo"
                                  <td>";
                                  if(//tuna je otazka o potvrdeni zmazania tohoto skladu
                                 isset($_REQUEST["action"]) && 
                                 ($_REQUEST["action"] == "zmaz_volitelny_parameter_formular") && 
                                 ($_REQUEST["parameter_id"] == $key )
                                ){
                                echo "Skutočne chcete zmazať tento parameter?"; 
                                echo "<form>
                                         <input type='hidden' name='parameter_id' value='".$key."'>
                                         <input type='hidden' name='action' value='zmaz_volitelny_parameter_proceed'>
                                         <input type='hidden' name='pageaction' value='settings'>
                                         <input type='hidden' name='subpageaction' value='nastavenie_tovaru'>
                                         <input type='submit' name='submit' value='Ano'>
                                      </form>
                                      <form>
                                         <input type='hidden' name='pageaction' value='settings'>
                                         <input type='hidden' name='subpageaction' value='nastavenie_tovaru'>
                                         <input type='submit' name='submit' value='Nie'>
                                      </form>";
                                  }//end if
                                  else{
                                     echo"<form>
                                             <input type='hidden' name='parameter_id' value='".$key."'>
                                             <input type='hidden' name='action' value='zmaz_volitelny_parameter_formular'>
                                             <input type='hidden' name='pageaction' value='settings'>
                                             <input type='hidden' name='subpageaction' value='nastavenie_tovaru'>
                                             <input type='submit' name='submit' value='Zmaž'>
                                          </form>";    
                                  }//end else  
                         echo" </td>
                             </tr>";
                     }//end foreach
                }// end if su definovane volitelne parametre
                ?>
             </table>
<?php
         break;
         
         case "kontaktne_udaje":
         default: //aj prednastavene zobrazenie
             global $uzivatel;
             $organizacia = new Organizacia();
             $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
?>
            <form>
                <h2>Kontaktné údaje</h2>
                Meno: <input type="text" name="meno" value="<?php echo $uzivatel->getMeno(); ?>" > <br>
                Priezvisko: <input type="text" name="priezvisko" value="<?php echo $uzivatel->getPriezvisko(); ?>" > <br>
                Email: <input type="text" name="email" value="<?php echo $uzivatel->getEmail(); ?>" disabled > <br>
                <hr>
                <h2>Organizácia</h2>
                Názov: <input type="text" name="nazov_organizacie"  value="<?php echo $organizacia->getNazov(); ?>" /> * <br>
                Ulica: <input type="text" name="ulica_organizacie" value="<?php echo $organizacia->getUlica(); ?>" />  <br>
                PSČ: <input type="text" name="psc_organizacie" value="<?php echo $organizacia->getPsc(); ?>"  /> 
                Mesto: <input type="text" name="mesto_organizacie" value="<?php echo $organizacia->getMesto(); ?>" />  <br>
                Štát: <input type="text" name="stat_organizacie" value="<?php echo $organizacia->getStat(); ?>" />  <br>
                IČO: <input type="text" name="ico_organizacie" value="<?php echo $organizacia->getICO(); ?>" />  <br>
                DIČ: <input type="text" name="dic_organizacie" value="<?php echo $organizacia->getDIC(); ?>" />  <br>
                IČ DPH: <input type="text" name="ic_dph_organizacie" value="<?php echo $organizacia->getICDPH(); ?>" />  <br>
                Telefón: <input type="text" name="telefon_organizacie" value="<?php echo $organizacia->getTelefon(); ?>" />  <br>
                <input type="hidden" name="pageaction" value="settings">
                <input type="hidden" name="subpageaction" value="kontaktne_udaje">
                <input type="hidden" name="action" value="kontaktne_udaje_proceed">
                <input type="submit" name="submit" value="Potvrď">
            </form>
<?php
         break;
     
         case "nastavenie_dokumentov":
             global $database;
             global $uzivatel;
             $organizacia = new Organizacia();
             $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
?>
            <h2>Nastavenie číslovania</h2>
            <h3>Číslovanie príjemky.</h3>
            Číslo príjemky má tvar xyzAAbbbbb (xyz je ľubovoľné, AA sú posledné dve cifry aktuálneho roku a bbbbb je poradové číslo). <br>
            Hodnoty AAbbbbb sú generované automaticky. V prípade zmeny počas kalendárneho roka, budú čísla generované odznova. <br>
            <form>    
                <?php $cislo = $organizacia->getCislovaniePrijemky(); ?>
                <input type="text" name="prijemka" value="<?php echo $cislo; ?>" size="3" maxlength="5">
                <input type="hidden" name="pageaction" value="settings">
                <input type="hidden" name="subpageaction" value="nastavenie_dokumentov">
                <input type="hidden" name="action" value="cislovanie_prijemky_proceed">
                <input type="submit" name="submit" value="Potvrď">
            </form>
            
            <h3>Číslovanie výdajky.</h3>
            Číslo výdajky má tvar xyzAAbbbbb (xyz je ľubovoľné, AA sú posledné dve cifry aktuálneho roku a bbbbb je poradové číslo). <br>
            Hodnoty AAbbbbb sú generované automaticky. V prípade zmeny počas kalendárneho roka, budú čísla generované odznova. <br>
            <form>    
                <?php $cislo = $organizacia->getCislovanieVydajky(); ?>
                <input type="text" name="vydajka" value="<?php echo $cislo; ?>" size="3" maxlength="5">
                <input type="hidden" name="pageaction" value="settings">
                <input type="hidden" name="subpageaction" value="nastavenie_dokumentov">
                <input type="hidden" name="action" value="cislovanie_vydajky_proceed">
                <input type="submit" name="submit" value="Potvrď">
            </form>
            
            <h3>Číslovanie výrobky.</h3>
            Číslo výrobky má tvar xyzAAbbbbb (xyz je ľubovoľné, AA sú posledné dve cifry aktuálneho roku a bbbbb je poradové číslo). <br>
            Hodnoty AAbbbbb sú generované automaticky. V prípade zmeny počas kalendárneho roka, budú čísla generované odznova. <br>
            <form>    
                <?php $cislo = $organizacia->getCislovanieVyrobky(); ?>
                <input type="text" name="vyrobka" value="<?php echo $cislo; ?>" size="3" maxlength="5">
                <input type="hidden" name="pageaction" value="settings">
                <input type="hidden" name="subpageaction" value="nastavenie_dokumentov">
                <input type="hidden" name="action" value="cislovanie_vyrobky_proceed">
                <input type="submit" name="submit" value="Potvrď">
            </form>
<?php
         break;
     
         case "nastavenie_opravneni":
?>php            
             <h2>Nastavenie oprávnení užívateľov</h2>
             <form name="opravnenia" action="index.php">
<?php        
             if ( isset($_REQUEST["action"]) && ($_REQUEST["action"] == "nastavenie_opravneni_zobraz" ) ){
                 $predvolene = $_REQUEST["uzivatel"];
             }   
             else {
                 $predvolene = null;
             }
             $zoznam_uzivatelov = vsetci_uzivatelia();   
             generuj_dropdown_box("uzivatel",$zoznam_uzivatelov, $predvolene);
?>
             <input type="hidden" name="pageaction" value="settings">
             <input type="hidden" name="subpageaction" value="nastavenie_opravneni">
             <input type="hidden" name="action" value="nastavenie_opravneni_zobraz">
             <input type="submit" name="submit" value="Potvrď">
             </form>
<?php        //zobrazime opravnenia pre daneho uzivatela                      
             if ( isset($_REQUEST["action"]) && ($_REQUEST["action"] == "nastavenie_opravneni_zobraz" ) ) {
                 $aktualny_uzivatel = new User();
                 $aktualny_uzivatel->loadFromDatabase($_REQUEST["uzivatel"]); //natiahne opravnenia pre tohto konrektneho uzivatela aj s opravneniami
                 $zoznam_opravneni = vsetky_opravnenia();
                 
                 //sekcia spracovania formularu ak tento bol odklepnuty
                 if ( isset($_REQUEST["spracovanie"]) && ($_REQUEST["spracovanie"] == "nastavenie_opravneni_proceed" ) ) {
                    foreach ($zoznam_opravneni as $kluc => $hodnota){
                       //kontrola ci je zaskrtnuty
                        if (isset($_REQUEST[$kluc]) ){ //je zaskrtnuty
                            $aktualny_uzivatel->zapisOpravnenie($kluc);
                        }
                        else { //nieje zaskrtnuty
                            $aktualny_uzivatel->vymazOpravnenie($kluc);
                        }
                    }//end foreach
                 }//end if isset spracovanie formulara
                 
                 
                 //vypiseme formular s opravneniami pre daneho uzivatela
?>
                 <form name="opravnenia" action="index.php">
<?php
                    foreach ($zoznam_opravneni as $kluc => $hodnota){
?>
                        <input type="checkbox" name="<?php echo $kluc; ?>" value="1" <?php if( $aktualny_uzivatel->mamOpravnenie($kluc) ){ echo "checked";} ?> > <?php echo $hodnota; ?> <br>
<?php                     
                    }//end foreach
?>               <input type="hidden" name="pageaction" value="settings">
                 <input type="hidden" name="subpageaction" value="nastavenie_opravneni">
                 <input type="hidden" name="action" value="nastavenie_opravneni_zobraz">
                 <input type="hidden" name="spracovanie" value="nastavenie_opravneni_proceed">
                 <input type="hidden" name="uzivatel" value="<?php echo $_REQUEST["uzivatel"]; ?>">
                 <input type="submit" name="submit" value="Potvrď">     
                 </form>
<?php             
             }

         break;
     
     }//end switch
 }//end if isset subpageaction
?>
</div>