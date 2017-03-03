<?php
// stranka zobrazi sa len ked uzivatel nieje prihlaseny, inak nema vyznam
if ( !is_registered() ) {
?>
<h1>Vytvorenie účtu</h1>
<form name="createaccount" action="index.php" >
Priezvisko:<input type="text" name="priezvisko" /> * <br>
Meno:<input type="text" name="meno" /> * <br>
Prihlasovací email:<input type="text" name="email" value="@" /> *  <br>
Heslo:<input type="password" name="passwd" /> * (min. 8 znakov: písmená a čísla) <br>
Heslo znovu: <input type="password" name="passwd_znovu" /> * <br>
<hr>
<h2>Organizácia</h2>
Názov: <input type="text" name="nazov_organizacie" /> * <br>
Ulica: <input type="text" name="ulica_organizacie" />  <br>
PSČ: <input type="text" name="psc_organizacie" /> 
Mesto: <input type="text" name="mesto_organizacie" />  <br>
Štát: <input type="text" name="stat_organizacie" />  <br>
IČO: <input type="text" name="ico_organizacie" />  <br>
DIČ: <input type="text" name="dic_organizacie" />  <br>
IČ DPH: <input type="text" name="ic_dph_organizacie" />  <br>
Telefón: <input type="text" name="telefon_organizacie" />  <br>
<input type="hidden" name="action" value="create_account" />
<input type="hidden" name="pageaction" value="create_account" />
<input type="submit" name="submit" value="OK" />
</form>
<div id="info"> * povinné údaje </div>
<?php
}//end if is_registered
?>
