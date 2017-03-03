<?php
class User {
    private $id;
    private $login;
    private $password;
    private $priezvisko;
    private $meno;
    private $email;
    private $organizacia; //id organizacie do ktorej uzivatel patri
    private $opravnenia; //pole opravneni uzivatela
    
    function __construct() {
        $this->opravnenia = array(); //vztvorenie pola aby to funkcie poznali
    }
        
    public function is_registered(){
        global $database;
        global $message;
        //compare if user is registered in database
        $vysl = $database->select("SELECT * FROM user WHERE email = '".$this->email."'") or die(mysql_error());
        if ( $database->pocet_riadkov($vysl) ){
            $polozka = $database->fetch_array($vysl);
            $this->id = $polozka["id"];
            //kontrola hesiel
            if (isset($this->password) && isset($polozka["password"])) {
                $stack = explode(':', $polozka["password"]);
                if (sizeof($stack) != 2) return false;
                if (md5($stack[1] . $this->password) == $stack[0]) { //mame spavneho uzivatela, natiahneme udaje z databazy
                    $this->meno = $polozka["meno"];
                    $this->priezvisko = $polozka["priezvisko"];
                    $this->email = $polozka["email"];
                    return true;
                }
            }
            $message->add( "nespravne heslo");
            return false;
        } 
        else $message->add("uzivatel nieje evidovany v databaze");    
    }
    
    public function existInDatabase(){
        global $database;
        $zdroj = $database->select("SELECT * FROM user WHERE email = '".$this->email."'");
        if ( $database->pocet_riadkov($zdroj) ){
            return true; //uzivatel je v databaze
        }
        return false; //uzivatel nieje v databaze
    }
    
    public function storeToDatabase(){
           global $database;
           //create encrypted passwdord from plain passwd from datab
           $password = '';
            for ($i=0; $i<10; $i++) {   $password .= rand();    }
            $salt = substr(md5($password), 0, 2);
            $password = md5($salt . $this->password) . ':' . $salt;
            //budeme vkladat aj s id pokial je definovany
            if (isset($this->id)){
                $array_data = array("id" => $this->id,
                                "id_organizacia" => $this->organizacia,
                                "login" => $this->login,
                                "password" => $password,
                                "priezvisko" => $this->priezvisko,
                                "meno" => $this->meno,
                                "email" => $this->email,
                                 );
            }
            else{ //vkladame bez id, musime ho potom preniest do triedy
                $array_data = array(
                                "id_organizacia" => $this->organizacia,
                                "login" => $this->login,
                                "password" => $password,
                                "priezvisko" => $this->priezvisko,
                                "meno" => $this->meno,
                                "email" => $this->email,
                               );
            }
            $database->insert("user", $array_data);
            $this->id = $database->getInsertId();
    }
    
    public function updateInDatabase(){
          global $database;
          global $message;
          $array_data = array(  "meno" => $this->meno,
                                "priezvisko" => $this->priezvisko,
                              );
            if ( $database->update("user", $array_data, "id", $this->id ) ){
                return true;
            }
            else{
                return false;
            }
      }
    
    public function getId(){ return $this->id; }
    public function getIdOrganizacie() {
        global $database;
        if (!isset( $this->organizacia ) ){
            $query = $database->select("SELECT id_organizacia FROM user WHERE id='".$this->id."'");
            $vysl = $database->fetch_array($query);
            $this->organizacia = $vysl["id_organizacia"];
        }
        return $this->organizacia;
    }
    public function getMeno(){ return $this->meno; }
    public function getPriezvisko(){ return $this->priezvisko; }
    public function getEmail() { return $this->email; }
    
   
    public function setID($id) { $this->id = $id;    }
    public function setOrganizacia($id_organizacie) { $this->organizacia = $id_organizacie;    }
    public function setLogin($login) {$this->login = $login;}
    public function setPasswd($passwd) {$this->password = $passwd;}
    public function setMeno($meno) {$this->meno = $meno;}
    public function setPriezvisko($priez) {$this->priezvisko = $priez;}
    public function setEmail($email) {$this->email = $email;}
    public function setLPMPE($login, $password, $meno, $priezvisko, $email){
        $this->login = $login;
        $this->password = $password;
        $this->priezvisko = $priezvisko;
        $this->meno = $meno;
        $this->email = $email;
    }
    
    
    public function setPasswordMenoPriezviskoEmail( $password, $meno, $priezvisko, $email){
        $this->password = $password;
        $this->priezvisko = $priezvisko;
        $this->meno = $meno;
        $this->email = $email;
    }
    
    public function checkName($name){
        global $message;
        if (!strlen($name)){
            $message->add("parameter must not be zero");
            return false;
        }
        if (!preg_match('/\A(
                         [\x09\x0A\x0D\x20-\x7E]
                       | [\xC2-\xDF][\x80-\xBF]
                       |  \xE0[\xA0-\xBF][\x80-\xBF]
                       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
                       |  \xED[\x80-\x9F][\x80-\xBF]
                       |  \xF0[\x90-\xBF][\x80-\xBF]{2}
                       | [\xF1-\xF3][\x80-\xBF]{3}
                       |  \xF4[\x80-\x8F][\x80-\xBF]{2}
                       )*\z/x', $name)) {
            $message->add("Only letters and white space allowed");
            return false;
        }
        return true;
    }
    
    public function checkEmail ($email){
        global $message;
        if (!strlen($email)){
            $message->add("parameter must not be zero");
            return false;
        }
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
            return false;
        }
        return true;  
    }
    
    public function checkLogin($login){
        global $message;
        if (!strlen($login)){
            $message->add("parameter must not be zero");
            return false;
        }
        if (!preg_match("/^[a-zA-Z ]*$/",$login)) {
            return false;
        }
        return true;
    }
    
    public function checkPassword($password){
        global $message;
        if (strlen($password) < 8){
            $message->add("password must be at least 8 characters long");
            return false;
        }
        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
            $message->add("password does not meet criteria");
            return false;
        }
        return true;
    }
    
    //natiahne pole opravneni pre tohto uzivatela
    public function loadOpravnenia() {
        global $database;
        $i = 0;
        $query = $database->select("SELECT * FROM user_opravnenia WHERE user_id = '".$this->id."'");
        while ($vysl = $database->fetch_array($query)){ 
            $this->opravnenia[$i] = $vysl["opravnenie_id"];
            $i++;
        }
    }
    
    public function loadFromDatabase ($user_id){
        global $database;
        $query = $database->select("SELECT * FROM user WHERE id = '".$user_id."'");
        $vysl = $database->fetch_array($query);
        $this->id = $user_id;
        $this->login = $vysl["login"];
        $this->meno = $vysl["meno"];
        $this->priezvisko = $vysl["priezvisko"];
        $this->email = $vysl["email"];
        $this->organizacia = $vysl["id_organizacia"];
        $this->loadOpravnenia();
    }
    
    public function checkCredentials(){
        global $message;
        if ( !($this->checkName($this->meno) ) ){
                $message->add("pole Meno musí byť správne vyplnene");
                return false;
            }
            if ( !($this->checkName($this->priezvisko) ) ){
                $message->add("pole Priezvisko musi byť spravne vyplnene");
                return false;
            }
            if ( !($this->checkPassword($this->password) ) ){
                $message->add("pole Heslo musi byt spravne vyplnene");
                return false;
            }
            if ( !($this->checkEmail($this->email) ) ){
                $message->add("pole Email musi byt spravne vyplnene");
                return false;
            }
            if ($this->existInDatabase()){
                $message->add("Tento uzivatel uz v databaze existuje");
                return false;
            }
            return true;
    }
    
    public function mamOpravnenie ($opravnenie){
        if (in_array($opravnenie, $this->opravnenia))
                return true;
        else return false;
    }
    
    public function zapisOpravnenie($opravnenie){
        global $database;
        //kontrola ci je take v nasom zozname opravnenie
        if (!in_array($opravnenie, $this->opravnenia)){//nemame taky zaznam, teda zapiseme
            $database->raw_query("INSERT INTO user_opravnenia(user_id, opravnenie_id) VALUES('".$this->id."','".$opravnenie."')");
            //zapiseme aj do objektu
            array_push($this->opravnenia, $opravnenie);
        }
    }
    
    public function vymazOpravnenie($opravnenie){
        global $database;
        //kontrola ci je take opravnenie uz v databaze
        if (in_array($opravnenie, $this->opravnenia)){//MAME taky zaznam, teda VYMAZEME
            $database->raw_query("DELETE FROM user_opravnenia WHERE user_id = '".$this->id."' AND opravnenie_id = '".$opravnenie."'");
            //VYMAZEME aj z objektu
            $key = array_search($opravnenie, $this->opravnenia);
            unset($this->opravnenia[$key]); //vymazanie
        }
    }
}

?>
