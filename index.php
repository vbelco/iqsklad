<?php
include "definition.php";//zakladne nastavenia

include "route.php";// stranka na spracovanie odpovedi z formularov

?>

<!DOCTYPE html>
<html lang="sk" >
    <head>
        <meta charset="UTF-8" />
        <title></title>
        <link rel="stylesheet" href="style.css" /> <!-- zakladny styl pre vsetko -->
    </head>
    
    <script>
    /*  AJAX FUNKCIONALITA */
    function ajaxFunkcie(url, cfunc) {
       var xhttp;
       xhttp=new XMLHttpRequest();
       xhttp.onreadystatechange = function() {
       if (xhttp.readyState == 4 && xhttp.status == 200) {
          cfunc(xhttp);
       }
       };
       xhttp.open("GET", url, true);
       xhttp.send();
    }

    </script>
    
    <body>
        <?php
        $message->vypis(); //vypisanie sprav v triede
        
        require_once 'header.php';
        
        //presmerovac na yaklade parametrov, bud od uyivatela alebo vnutorne presmerovania stranok
        global $presmerovac;
        if (isset($_REQUEST["pageaction"])) $presmerovac = $_REQUEST["pageaction"];
        
        switch ($presmerovac){
                case "create_account": //stranka na registrovanie uzivatela
                    include "create_account.php";
                break;
                case "sklady": //stranka definovanie skladov pre uzivatela
                    include "sklady.php";
                break;
                case "pridaj_sklad":
                    include "sklady_pridaj.php";
                break;
                case "settings":
                    include "settings.php";
                break;
                case "items":
                    include "items.php";
                break;
                case "pridaj_tovar":
                    include "pridaj_tovar.php";
                break;
                case "vydaj_tovar":
                    include "vydaj_tovar.php";
                break;
                case "vyrob_tovar":
                    include "vyrob_tovar.php";
                break;
                case "dokumenty":
                    include "dokumenty.php";
                break;
                case "zobraz-detaily-dokumentu":
                    include "zobraz-detaily-dokumentu.php";
                break;
                case "definuj_vyrobok":
                    include "definuj_vyrobok.php";
                break;
            
                default:
                break;
                    
         }//end switch

//uzatvorenie spojenia na db server
$database->close();         
         
        ?>
    </body>
</html>
