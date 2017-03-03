<?php
if ( isset($_REQUEST["action"]) ){
    switch ($_REQUEST["action"]){
        case "action_user_login":
            global $uzivatel;
            global $message;
            //kontrola vstup udajov
            $uzivatel = new User();
            $uzivatel->setEmail( test_input($_REQUEST["email"]) );
            $uzivatel->setPasswd( test_input($_REQUEST["passwd"]) ); //vytvorenie uzovatela
            if ( $uzivatel->is_registered() ) {
                $_SESSION["user"]=$uzivatel->getId();
                $uzivatel->loadFromDatabase($_SESSION["user"]); //natiahnutie ostatnzch udajov o uzivatelovi
                $organizacia = new Organizacia();
                $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
                //kontrola na prechod na novy rok = resetovanie ciselnikov dokumentov
                if ( $organizacia->kontrolaPrechoduRoku() ){
                    $organizacia->setAktualnyRokDokumentov(); //nastavi aktualnz rok dokumentov
                    $organizacia->resetujCiselnikyDokumentov();
                    //prenesei ymenu do databazy
                    $organizacia->updateInDatabase();
                    $message->add("System presiel na novy rok! Boli resetovane ciselniky dokumentov.");
                }
                $page->redirect(); //po uspesnom prihlaseni redirekneme, aby sa nam nacitali udaje o uzivatelovi
            }
            else $message->add("uzivatel nieje registrovany");
            //prihlasenie
            //$page->redirect(); // tato funkcia nejako moc nefunguje, pretoye sa straca prihlasenie, session zaberie ale stratia sa informacie po uzivatelovi
        break;
        
        case "action_user_logout":
            unset($uzivatel);
            unset ($_SESSION["user"]);
        break;
        
        case "create_account":   
            $sklad = new Sklad();
            $uzivatel = new User();
            $organizacia = new Organizacia();
            global $page;
            global $aktivny_sklad;
            $passwd = test_input ($_REQUEST["passwd"]);
            $passwd_znovu = test_input ( $_REQUEST["passwd_znovu"] );
            $meno = test_input ($_REQUEST["meno"]);
            $priezvisko = test_input ($_REQUEST["priezvisko"]);
            $email =  test_input ($_REQUEST["email"]);
            $uzivatel->setPasswordMenoPriezviskoEmail($passwd,$meno,$priezvisko,$email);
            
            //udaje o organizacii
            $nazov = test_input ($_REQUEST["nazov_organizacie"]);
            $ulica = test_input ($_REQUEST["ulica_organizacie"]);
            $psc = test_input ($_REQUEST["psc_organizacie"]);
            $mesto = test_input ($_REQUEST["mesto_organizacie"]);
            $stat = test_input ($_REQUEST["stat_organizacie"]);
            $ico = test_input ($_REQUEST["ico_organizacie"]);
            $dic = test_input ($_REQUEST["dic_organizacie"]);
            $icdph = test_input ($_REQUEST["ic_dph_organizacie"]);
            $telefon = test_input ($_REQUEST["telefon_organizacie"]);
            
            if ( $passwd != $passwd_znovu ){ //kontrola ci su hesla rovnake
                $message->add("Heslá musia byť totožné");
            }
            else if ( $uzivatel->checkCredentials() === true ) {  //zadane udaje zodpovedaju poziadavkam
                $organizacia->setVsetkyUdaje($nazov, $ulica, $psc, $mesto, $stat, $ico, $dic, $icdph, $telefon);
                $organizacia->setAktualnyRokDokumentov();
                $organizacia->setCislovaniePrijemky("PRJ"); //defaultne nastavenie cislovania dokumentov
                $organizacia->setCislovanieVydajky("VYD"); //defaultne nastavenie cislovania dokumentov
                $organizacia->setCislovanieVyrobky("VYR"); //defaultne nastavenie cislovania dokumentov
                
                $id_organizacie = $organizacia->storeTodatabase();
                $uzivatel->setOrganizacia($id_organizacie); 
                
                $uzivatel->storeToDatabase(); //vlozenie uzivatela do databazy
                $message->add("Uzivatel bol vytvoreny");
                $_SESSION["user"] = $uzivatel->getId(); //prihlasenie uzivatela
                $sklad->setName("sklad");
                $sklad->setDescription("Prednastaveny sklad");
                $sklad ->createInDatabase($uzivatel); //vytvorenie skladu v databaze pre vytvaraneho uzivatela
                $sklad->activate(); //aktivuje tento sklad ako prednastavenz na pracu
                //nacitanie strankz abz sa aktivoval aktivny sklad
                $page->redirect();
                
            }
        break;
        
        case "pridaj_novy_sklad": //pridanie noveho skladu pre aktualneo uzivatela
            global $uzivatel; //trieda uzivatela, ino o aktualne prihlasenom uzivcatelovi
            global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
            global $page; //trieda stranky, sluzi napr na presmerovanie
            $sklad = new Sklad();
            $sklad->setName( test_input ($_REQUEST["meno_skladu"]) );
            $sklad->setDescription(test_input ($_REQUEST["popis_skladu"]) );
            echo $sklad->getName().":".$sklad->getDescription();
            if ( $sklad->checkName() && $sklad->checkDescription() ){
                $sklad->createInDatabase($uzivatel); //vytvorenie zaznamu v databaze pre aktualneho uzivatela
                $message->add("Sklad bol uspesne pridany"); //toto nefnguje, po nacitani stranky sa sprava strati
                $presmerovac = "sklady";//presmerovanie stranky na stranku yuynamu skladov
            }
            else {
                $message->add("Sklad sa nepodarilo pridat"); //toto nefnguje, po nacitani stranky sa sprava strati
                $presmerovac = "pridaj_sklad";
            }
        break;
        
        case "edituj_sklad_proceed":  // vykonanie zmien parametrov skladu
            global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
            $sklad = new Sklad();
            $sklad->setId( $_REQUEST["sklad_id"] );
            $sklad->setName( test_input ($_REQUEST["meno_skladu"]) );
            $sklad->setDescription(test_input ($_REQUEST["popis_skladu"]) );
            if ( $sklad->updateInDatabase( ) ){
                $message->add("Sklad bol úspešne zmenený");
            }
            else{ $message->add("Sklad nebol znemený"); }
        break;
        
        case "zmaz_sklad_proceed":
            global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
            $sklad = new Sklad();
            $sklad->setId( $_REQUEST["sklad_id"] );
             if ( $sklad->deleteInDatabase( ) ){
                $message->add("Sklad bol vymazaný");
            }
            else{ $message->add("Sklad nebol vymazaný"); }
        break;
        
        case "kontaktne_udaje_proceed":
            global $message;
            global $uzivatel; //trieda uzivatela, ino o aktualne prihlasenom uzivcatelovi
            $meno = test_input ($_REQUEST["meno"]);
            $priezvisko = test_input ($_REQUEST["priezvisko"]);
            //udaje o organizacii
            $nazov = test_input ($_REQUEST["nazov_organizacie"]);
            $ulica = test_input ($_REQUEST["ulica_organizacie"]);
            $psc = test_input ($_REQUEST["psc_organizacie"]);
            $mesto = test_input ($_REQUEST["mesto_organizacie"]);
            $stat = test_input ($_REQUEST["stat_organizacie"]);
            $ico = test_input ($_REQUEST["ico_organizacie"]);
            $dic = test_input ($_REQUEST["dic_organizacie"]);
            $icdph = test_input ($_REQUEST["ic_dph_organizacie"]);
            $telefon = test_input ($_REQUEST["telefon_organizacie"]);
            $organizacia = new Organizacia( $uzivatel->getIdOrganizacie() );//vztvorime si pomocnu organiyaciu
            $organizacia->setVsetkyUdaje($nazov, $ulica, $psc, $mesto, $stat, $ico, $dic, $icdph, $telefon); //nastavime udaje v organizacii
            $organizacia->setId( $uzivatel->getIdOrganizacie() );//nastavenie id abz sme vedeli ktoru organizaciu v databaze mame menit
            if ( $uzivatel->checkName($meno) && $uzivatel->checkName($priezvisko) ) {  //zadane udaje zodpovedaju poziadavkam
                $uzivatel->setMeno($meno);
                $uzivatel->setPriezvisko($priezvisko);
                if ( $uzivatel->updateInDatabase() ){ //vlozenie uzivatela do databazy
                    $organizacia->updateInDatabase();//udpatneme aj udaje o oragnizacii v databaze
                    $message->add("Kontaktné údaje boli zmenené");
                }
                else{
                    $message->add("Zmena údajov nebola úspešná");
                }
            }
        break;
        
        case "aktivuj_sklad":
            global $message;
            global $database;
            global $aktivny_sklad;
            //najde aktivny sklad
            $sklad_old = najdi_aktivny_sklad();
            $sklad_old->deactivate();//deaktivuje starz sklad
            $aktivny_sklad->setId( $_REQUEST["sklad_id"] );
            $aktivny_sklad->activate(); //aktivuje novy sklad v databaze
            $aktivny_sklad = najdi_aktivny_sklad(); //nastavenie vsetkych parametrov skladu z databazy
        break;
    
        case "nastavenie_tovaru_fixne_proceed": //nastavi parametre tovaru, ktore sa budu zadavat pre tovar
            global $uzivatel;
            global $message;
            global $database;
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            
            
            //najskor zistime ci zaznam popis v databaze uz nahodou nieje
            //ak je zaznam v databaze a nemame zaskrtnute, dame ho prec
            if ( $organizacia->fixny_parameter("description") ) {
                if ( !(isset($_REQUEST["description"])) ){
                   $organizacia->delete_fixny_parameter ("description");
                   $message->add("Hodnota bola nastavena");
                }
            }
            else{//ak nieje v databaze a mame zaskrtnute, damo ho databazy
                if ( isset($_REQUEST["description"]) ){
                    $organizacia->add_fixny_parameter ("description");
                    $message->add("Hodnota bola nastavena");
                }
            }
           
            if ( $organizacia->fixny_parameter("kod") ) {
                if ( !(isset($_REQUEST["kod"])) ){
                   $organizacia->delete_fixny_parameter ("kod");
                   $message->add("Hodnota bola nastavena");
                }
            }
            else{
                if ( isset($_REQUEST["kod"]) ){
                    $organizacia->add_fixny_parameter ("kod");
                    $message->add("Hodnota bola nastavena");
                }
            } 
            $page->redirect("index.php?pageaction=settings&subpageaction=nastavenie_tovaru");
        break;
        
        case "nastavenie_tovaru_pridaj_volitelne_proceed": //nastavi parametre tovaru, ktore sa budu zadavat pre tovar
            global $uzivatel;
            global $message;
            global $database;
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            
            $organizacia->add_volitelny_parameter ($_REQUEST["nazov"]);
            $message->add("Hodnota bola nastavena");
        break;
    
        case "zmen_volitelny_parameter_proceed":
            global $uzivatel;
            global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            
            $id_volitelneho_parametra = $_REQUEST["parameter_id"];
            $nazov_volitelneho_parametra = $_REQUEST["nazov_parametra"];
            
            $organizacia->set_volitelny_parameter($id_volitelneho_parametra, $nazov_volitelneho_parametra);         
            if ( $aorganizacia->update_volitelny_parameter( $id_volitelneho_parametra, $nazov_volitelneho_parametra ) ){
                $message->add("Volitelný parameter skladu bol úspešne zmenený");
            }
            else{ $message->add("Parameter nebol zmenený"); }
        break;
        
        case "zmaz_volitelny_parameter_proceed":
            global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
            global $uzivatel;
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            
            $id_volitelneho_parametra = $_REQUEST["parameter_id"];
            
            //vymazanie parametra zo skladu
            if (  $organizacia->delete_volitelny_parameter($id_volitelneho_parametra) ){
                $message->add("Volitelný parameter skladu bol úspešne zmazaný");
            }
            else{ $message->add("Parameter nebol zmazaný"); }
        break;
        
        case "pridaj_tovar": //pridame tovar do databazy pre aktualneho uzivatela do aktualneho skladu
            global $database; //spristupni databazove funckie
            global $uzivatel; // trieda aktualneho uzivatela
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            
            $nazov = $_REQUEST["nazov"]; 
            $pocet = 0; // pri vkladani je vstupnz poce rovny nule
            //zakladne parametre
            $tovar_array = array( "sklad_id" => $aktivny_sklad->getId(),
                                  "name" => $nazov,
                                  "datum_pridania" => "NOW()"
                                );
            //fixne parametre
            if( $organizacia->fixny_parameter("description") ) { $tovar_array["popis"] = $_REQUEST["description"]; }
            if( $organizacia->fixny_parameter("kod") ) { $tovar_array["kod"] = $_REQUEST["kod"]; }
            //vlozime fixne parametre do datab
            $database->insert("tovar", $tovar_array); //pridanie tovaru do databazy
            
            $tovar_id = $database->getInsertId();
            //volitelne parametre
            $array_volitelne_parametre = $organizacia->get_volitelne_parametre();
            
            if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
                foreach ($array_volitelne_parametre as $kluc => $parameter){
                    $tovar_array = array(); //redeklarujeme pole, setrime pamet php
                    $tovar_array["tovar_id"]=$tovar_id;
                    $tovar_array["parameter_tovaru_id"] = $kluc;
                    $tovar_array["hodnota"] = $_REQUEST[$kluc];
                    $database->insert("tovar_volitelne_parametre", $tovar_array); //vlozime volitelne parametre do databazy
                }
            }//end id count()
        break;
        // vykona editovanie tovaru
        case "edituj_tovar_proceed":
            global $database; //spristupni databazove funckie
            global $uzivatel; // trieda aktualneho uzivatela
            global $aktivny_sklad; //trieda aktivne zvoleneho skladu
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            
            $nazov = $_REQUEST["meno"]; // hodnota mena tovaru
            $sklad_id = $_REQUEST["sklad_id"]; //hodnota id skladu z dropdownboxu do ktoreho patri tovar
            $min_stav = $_REQUEST["min_stav"]; //minimalna hodnota stavu a sklade
            //zakladne parametre
            $tovar_array = array( "sklad_id" => $sklad_id,
                                  "name" => $nazov,
                                  "min_stav" => $min_stav
                                );
            //fixne parametre
            if( $organizacia->fixny_parameter("description") ) { $tovar_array["popis"] = $_REQUEST["popis"]; }
            if( $organizacia->fixny_parameter("kod") ) { $tovar_array["kod"] = $_REQUEST["kod"]; }
            //vlozime fixne parametre do datab
            $database->update("tovar", $tovar_array, "id", $_REQUEST["tovar_id"]);
            //volitelne parametre
            $array_volitelne_parametre = $organizacia->get_volitelne_parametre();
            
            if ( count($array_volitelne_parametre) > 0 ){ //vypisovat budeme len ked uy mame nejake volitelne parametre definovane
                foreach ($array_volitelne_parametre as $kluc => $parameter){   
                    $tovar_array = array(); //redeklarujeme pole, setrime pamet php
                    $tovar_array["tovar_id"]=$_REQUEST["tovar_id"];
                    $tovar_array["parameter_tovaru_id"] = $kluc;
                    $tovar_array["hodnota"] = $_REQUEST[$kluc];
                    //ked este nieje hodnota v databaze, tak ju vlozi, inak ju updatne
                    $query = $database->select("SELECT * FROM tovar_volitelne_parametre WHERE tovar_id = '".$_REQUEST["tovar_id"]."'"
                                                                                . " AND parameter_tovaru_id = '".$kluc."'");
                    if ( $database->pocet_riadkov($query) ){
                        $vysl3 = $database->fetch_array($query);
                        $database->update("tovar_volitelne_parametre", $tovar_array, "id" ,$vysl3["id"]); //vlozime volitelne parametre do databazy
                    }
                    else {
                        $database->insert("tovar_volitelne_parametre", $tovar_array);
                    }
                }//end foreach
            }//end if count()
        break;
        
        case "zmaz_tovar_proceed":
            global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
            global $aktivny_sklad;
            
            $tovar_id = $_REQUEST["tovar_id"];
            $tovar_array = array( "aktivny" => 0,
                                  "datum_zmazania" => "NOW()"
                                );
            //vymazanie tovaru zo skladu, tovar sa nevzmaye ale osatane v databaze a by boli zachovane vytahz s osotanymi sucastami databazy
            if (  $database->update("tovar",$tovar_array, "id", $tovar_id ) ){
                $message->add("Tovar v sklade bol vymazany");
            }
            else{ $message->add("Tovar sa nepodarilo zmazat"); }
        break;
        
        case "cislovanie_prijemky_proceed":
           global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
           global $uzivatel;
           global $database;
           $organizacia = new Organizacia();
           $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
           $cislo_prijemky = $_REQUEST["prijemka"];
           $organizacia->setCislovaniePrijemky($cislo_prijemky); //upravi nastavenie uzivatela, prida tam aktualny rok
           if ( $organizacia->updateInDatabase() ) { // zmeni v databaze
               $message->add("Hodnota bola zmenená");
           }
        break;
        
        case "cislovanie_vydajky_proceed":
           global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
           global $uzivatel;
           global $database;
           $organizacia = new Organizacia();
           $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
           $cislo_vydajky = $_REQUEST["vydajka"];
           $organizacia->setCislovanieVydajky($cislo_vydajky); //upravi nastavenie uzivatela, prida tam aktualny rok
           if ( $organizacia->updateInDatabase() ) { // zmeni v databaze
               $message->add("Hodnota bola zmenená");
           }
        break;
        
        case "cislovanie_vyrobky_proceed":
           global $message; //zasobnik na spravy co sa vypisuju uzivatelovi
           global $uzivatel;
           global $database;
           $organizacia = new Organizacia();
           $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
           $cislo_vyrobky = $_REQUEST["vyrobka"];
           $organizacia->setCislovanieVyrobky($cislo_vyrobky); //upravi nastavenie uzivatela, prida tam aktualny rok
           if ( $organizacia->updateInDatabase() ) { // zmeni v databaze
               $message->add("Hodnota bola zmenená");
           }
        break;
        
        //zaradi VYROBKY do aktualnej VYROBKY
        case "aktualizuj_tovar_vyrobky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualny zoznam tovaru v aktualnej vydajke
            $tovar_id = $_REQUEST["item_id"];
            $pocet = $_REQUEST ["pocet"]; // tu by mali bzt kontrolz na vstup, teda ci bolo zadane cislo a nie nejake bludy
            if ( !isset ($_SESSION["zoznam_poloziek_vyrobka"][$tovar_id] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["zoznam_poloziek_vyrobka"][$tovar_id] = "";
            }
            $_SESSION["zoznam_poloziek_vyrobka"][$tovar_id] += $pocet; //ulozenie hodnoty tovaru do session 
        break;
    
        //zapise prijemku do databazy
        case "zapis_tovar_prijemky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            if ( isset ($_SESSION["prijemka_poznamka"] ) ){ 
                $poznamka = $_SESSION["prijemka_poznamka"];
            }
            else $poznamka = "";
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            //vytvorenie novej prijemky
            $prijemka = new Prijemka( $organizacia->getCisloNovejPrijemky(), $organizacia->getId() );
            //nacitanie poznamky k prijemke
            $prijemka->setPoznamka($poznamka);
            //nacitanie udajov o tovare do prijemky
            $prijemka->nacitajSessionPolozky($_SESSION["zoznam_poloziek"]);
            //ulozenie prijemky do databazy a YAROVEN AJ uravenie stavov  na skladoch
            $prijemka->storeToDatabase();
            //vymazanie session
            unset($_SESSION["zoznam_poloziek"]);
            $message->add("Prijemka bola ulozena");
            //update cislovanie prijemky
            $organizacia->pridajCisloPrijemky();
            //update udajov uzivatela v databaze
            $organizacia->updateInDatabase();
        break;
    
        //zapise vydajku do databazy
        case "zapis_tovar_vydajky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            //vytvorenie novej vydajky
            $vydajka = new Vydajka( $organizacia->getCisloNovejVydajky(), $organizacia->getId() );
            //nacitanie udajov o tovare do vydajky
            $vydajka->nacitajSessionPolozky($_SESSION["zoznam_poloziek_vydajka"]);
            //ulozenie vydajky do databazy a zaroven update poloziek na sklade
            $vydajka->storeToDatabase();
            //vymazanie session
            unset($_SESSION["zoznam_poloziek_vydajka"]);
            $message->add("Výdajka bola ulozena");
            //update cislovanie prijemky
            $organizacia->pridajCisloVydajky();
            //update udajov uzivatela v databaze
            $organizacia->updateInDatabase();
        break;
        
        //zapise vydajku do databazy
        case "zapis_tovar_vyrobky_proceed":
            global $uzivatel;
            global $database;
            global $message;
            if ( isset ($_SESSION["vyrobka_poznamka"] ) ){ 
                $poznamka = $_SESSION["vyrobka_poznamka"];
            }
            else $poznamka = "";
            $organizacia = new Organizacia();
            $organizacia->loadFromDatabase( $uzivatel->getIdOrganizacie() );
            //vytvorenie novej prijemky
            $vyrobka = new Vyrobka( $organizacia->getCisloNovejVyrobky(), $organizacia->getId() );
            //nacitanie udajov o tovare do vydajky
            $vyrobka->nacitajSessionPolozky($_SESSION["zoznam_poloziek_vyrobka"]);
            //nacitanie poznamky 
            $vyrobka    ->setPoznamka($poznamka);
            //ulozenie vydajky do databazy a zaroven update poloziek na sklade
            $vyrobka->storeToDatabase();
            //vymazanie session
            unset($_SESSION["zoznam_poloziek_vyrobka"]);
            $message->add("Výrobka bola ulozena");
            //update cislovanie prijemky
            $organizacia->pridajCisloVyrobky();
            //update udajov uzivatela v databaze
            $organizacia->updateInDatabase();
        break;
    
        case "zrus_tovar_prijemky_proceed":
            unset($_SESSION["zoznam_poloziek"]);
        break;
    
        case "zrus_tovar_vydajky_proceed":
            unset($_SESSION["zoznam_poloziek_vydajka"]);
        break;
    
        case "zrus_tovar_vyrobky_proceed":
            unset($_SESSION["zoznam_poloziek_vyrobka"]);
        break;
    
        //zaradi tovar do definicie vyrobku, sluzina na vytvorenie zoznamu v session o vyrobku, ktory je zlozeny z tovarov
        case "aktualizuj_definiciu_proceed":
            global $uzivatel;
            global $database;
            global $message;
            // v session budeme ukladat aktualny zoznam tovaru v aktualnej prijemke
            $tovar_id = $_REQUEST["item_id"]; //referencia, ktory tovar je sucastou vyrobku
            $pocet = $_REQUEST ["pocet"]; // tu by mali bzt kontrolz na vstup, teda ci bolo zadane cislo a nie nejake bludy
            if ( !isset ($_SESSION["zoznam_poloziek_definicia"][$tovar_id] ) ){ //pouzijeme ked este nemame pouzitu premennu aby nevypisovalo ze premenna nieje deklarovana
                $_SESSION["zoznam_poloziek_definicia"][$tovar_id] = "";
            }
            $_SESSION["zoznam_poloziek_definicia"][$tovar_id] = $pocet; //ulozenie hodnoty tovaru do session 
        break;
        
        //zapise definiciu vyrobku do databazy, zoberie vytvorene pole v Session a ulozi ho do datab
        case "zapis_definiciu_proceed":
            global $uzivatel;
            global $database;
            global $message;
            $vyrobok_id = $_REQUEST["vyrobok_id"];
            //vytvorenie noveho vyrobku
            $vyrobok = new Vyrobok();
            //vlozenie id vyrobku do triedy
            $vyrobok->loadItem($vyrobok_id);
            //nacitanie udajov o tovare do vyrobkovej definicie
            $vyrobok->pridajVsetkyTovary( $_SESSION["zoznam_poloziek_definicia"] );
            //ulozenie prijemky do vyrobkovej definicie
            $vyrobok->insertToDatabase();
            //vymazanie session
            unset($_SESSION["zoznam_poloziek_definicia"]);
            $message->add("Vyrobok bol definovany");
        break;
        
        //vycisti session abys sa vycistil formular
        case "zrus_definiciu_proceed":
            unset($_SESSION["zoznam_poloziek_definicia"]);
        break;
    
        // UPDATNE polozky v databaze podla SESSION pre definovanie vyrobkov z tovarov
        case "update_definiciu_proceed":
            global $uzivatel;
            global $database;
            global $message;
            $vyrobok_id = $_REQUEST["vyrobok_id"];
            //vytvorenie noveho vyrobku
            $vyrobok = new Vyrobok();
            //vlozenie id vyrobku do triedy
            $vyrobok->loadItem($vyrobok_id);
            //nacitanie udajov o tovare do vyrobkovej definicie
            $vyrobok->pridajVsetkyTovary( $_SESSION["zoznam_poloziek_definicia"] );
            //ulozenie definicie do databazy, upravi existujuce definicie, zmaze tie nulove a vytvori nove definicie
            $vyrobok->updateInDatabase();
            //vymazanie session
            unset($_SESSION["zoznam_poloziek_definicia"]);
            $message->add("Vyrobok bol definovany");
        break;
                
    }
}
