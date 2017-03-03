<div id="header">
    <div id="login">
        <?php
        if ( is_registered() ){ //uzivatel je prihlaseny
            global $uzivatel;
            global $aktivny_sklad;
        ?>
            <div id='welcome_message'> Vitajte <?php echo $uzivatel->getMeno(); ?>  </div>
            <div> Aktívny sklad: 
                <?php  if ( isset($aktivny_sklad) ) { 
                            echo $aktivny_sklad->getName(); 
                        } else {
                            echo "Vyskytla sa chyba pri nacitani aktualneho skladu";
                        }
                ?>  
            </div>
            <form name="logoutbox" action="index.php" >
            <input type="hidden" name="action" value="action_user_logout" />
            <input type="submit" name="submit" value="Logout" />
            </form>
            <ul id="main_menu">
                <li><a href="index.php">Domov</a> </li>
                <?php  if($uzivatel->mamOpravnenie(2) ) {?> <li><a href="index.php?pageaction=sklady">Sklady</a> </li> <?php } ?>
                <?php  if($uzivatel->mamOpravnenie(5) ) {?> <li><a href="index.php?pageaction=items">Tovar</a> </li> <?php } ?>
                <?php  if($uzivatel->mamOpravnenie(9) ) {?> <li><a href="index.php?pageaction=dokumenty">Dokumenty</a> </li> <?php } ?>
                <?php  if($uzivatel->mamOpravnenie(4) ) {?> <li><a href="index.php?pageaction=settings&subpageaction">Nastavenia</a></li> <?php } ?>
            </ul>
            <?php
        }
        else { //uzivatel nieje prihlaseny
            ?>
            <form name="loginbox" action="index.php" >
                Prihlasovací email:<input type="text" name="email" /> <br>
                Heslo:<input type="password" name="passwd" /> <br>
                <input type="hidden" name="action" value="action_user_login" />
                <input type="submit" name="submit" value="OK" />
            </form>
            <a id="odkaz" href="index.php?pageaction=create_account" />Vytvor účet</a>
            <?php
        }
        ?>
    </div>
</div>