<?php
// stranka zobrazi sa len ked uzivatel JE prihlaseny, inak presmeruje na zakladn index.php
if ( !is_registered() ) {
    global $page;
    $page->redirect();
}

?>
<h1>Pridanie nového skladu</h1>
<div >
    <form action="index.php">
        Názov skladu: <input type="text" name="meno_skladu"> <br />
        Popis skladu: <textarea rows="4" cols="50" name="popis_skladu"></textarea> <br />
        <input type="hidden" name="action" value="pridaj_novy_sklad">
        <input type="submit" name="submit" value="OK">
    </form>
</div>