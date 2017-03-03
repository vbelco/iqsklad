<?php

//funckia na yistenie, ci je uzivatel prihlaseny
function is_registered(){
    if ( isset($_SESSION["user"]) ) return true;
    else return false;
}

//funkcia na otestovanie vstupu od uzivatela, vycistenie bielzch znakov, odstanenie lomitiek a tranformovanie specianzch ynakov na ich html 
function test_input($data) {
  //$data = trim($data);
  //$data = stripslashes($data);
  //$data = htmlspecialchars($data);
  //return $data;
    //a teray vsetko v kope
    return htmlspecialchars( stripslashes( trim($data) ) );
}

function najdi_aktivny_sklad(){
    global $database;
    $user = new User();
    $user->setID($_SESSION["user"]); //yak;adne nastavenie id v triede uzivatela
    $organizacia_id = $user->getIdOrganizacie(); //zistenie id prisluchajucej ornaziyacie pre uzivatela
    $result = $database->select("SELECT * FROM sklad WHERE aktivny=1 AND organizacia_id = '".$organizacia_id."'");
    $polozka = $database->fetch_array($result);
    $sklad = new Sklad();
    $sklad->nacitaj($polozka["id"]);
    return $sklad;
}

//unkcia nacita vsetky sklady uzivatela a vrati ich ako pole
// index bude id skladu a hodnota bude jeno nazov
function vsetky_sklady(){
    global $database;
    $user = new User();
    $user->setID($_SESSION["user"]); //yak;adne nastavenie id v triede uzivatela
    $organizacia_id = $user->getIdOrganizacie(); //zistenie id prisluchajucej ornaziyacie pre uzivatela
    $sklady = array();
    $query = $database->select("SELECT * FROM sklad WHERE organizacia_id = '".$organizacia_id."'");
    while ($vysl = $database->fetch_array($query)){ //prebehne vsetky nacitane sklady uzivatela
        $sklady[$vysl["id"]]=$vysl["name"];
    }
    return $sklady;
}

//funcia vypise dropdownbox do formulara
// $nazov urcuje hodnotu name dropdown boxu
//vstup je pole v tvare [index] = hodnota
// index urcuje hodnotu
// hodnota urcuje text zobrazujuci sa v boxe
// $vybrana_polozka urcuje predvolenu volbu je porovnavana s indexom
function generuj_dropdown_box ($nazov, $pole, $vybrana_polozka = null){
    $selected = "";
    echo "<select name='".$nazov."'>";
    foreach ($pole as $index => $hodnota){
        if ($vybrana_polozka == $index) $selected = "selected";
        echo "<option value='".$index."' ".$selected.">".$hodnota."</option>";
        $selected = "";
    }//end foreach
    echo "</select>";
}

//funkcia na najdenie vsetkzch uzivatelov v aktualnej organizacii
// vystup je pole uzivatelov array => user_id, meno
function vsetci_uzivatelia () {    
    global $database;
    global $uzivatel; //aktivny uzivatel
    
    $organizacia_id = $uzivatel->getIdOrganizacie(); //nacitanie aktualnej organizacie
    $navrat = array();
    
    $query = $database->select("SELECT * FROM user WHERE id_organizacia = '".$organizacia_id."' ");
    while ($vysl = $database->fetch_array($query)){ //prebehne vsetky nacitane sklady uzivatela
        $navrat[$vysl["id"]]=$vysl["meno"]." ".$vysl["priezvisko"];
    }//end while
    
    return $navrat;
}

//funkcia natiahne z databazy zoznam vsetkych definovanych opravneni
//vystup le pole opravneni arra=> id_opravnenia, nazov opravnenia
function vsetky_opravnenia(){
    global $database;
    $navrat = array();
    
    $query = $database->select("SELECT * FROM opravnenia WHERE 1 ");
    while ($vysl = $database->fetch_array($query)){ //prebehne vsetky nacitane sklady uzivatela
        $navrat[$vysl["id"]]=$vysl["nazov"];
    }//end while
    
    return $navrat;
}
?>
