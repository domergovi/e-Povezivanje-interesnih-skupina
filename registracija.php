<?php
include("baza.class.php");
require_once('./rechapta/recaptchalib.php');
session_start();
//recaptcha provjera pri registraciji
function provjeraRecaptcha()
{
  $response = $_POST["g-recaptcha-response"];
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => '6LcGgx8UAAAAADLu8bjERhjvICwANBHptdihi8Ay',
		'response' => $_POST["g-recaptcha-response"]
	);
}

//provjera znakova u IMENU ( { # ! " / \
function ProvjeraImena(){
    $ime=$_POST["ime"];
    $provjera = true;
    $duljina = strlen($ime);
    $nedozvoljeniZnakovi = array("(",")","{","}","'","!","#","“","\\","/");
    $duljina2 = sizeof($nedozvoljeniZnakovi);
    
    for ($i=0; $i<$duljina; $i++){
        for ($j=0; $j<$duljina2; $j++){
            if ($ime[$i]==$nedozvoljeniZnakovi[$j]){
                $provjera=false;
                }
            }
        }

    return $provjera;
}

//provjera znakova u PREZIMENU ( { # ! " / \
function ProvjeraPrezimena(){
    $prezime=$_POST["prez"];
    $provjera = true;
    $duljina = strlen($prezime);
    $nedozvoljeniZnakovi = array("(",")","{","}","'","!","#","“","\\","/");
    $duljina2 = sizeof($nedozvoljeniZnakovi);
    
    for ($i=0; $i<$duljina; $i++){
        for ($j=0; $j<$duljina2; $j++){
            if ($prezime[$i]==$nedozvoljeniZnakovi[$j]){
                $provjera=false;
                }
            }
        }

    return $provjera;
}

//provjera znakova u KORISNIČKO IME ( { # ! " / \
function ProvjeraKorImena(){
    $korime=$_POST["korime"];
    $provjera = true;
    $duljina = strlen($korime);
    $nedozvoljeniZnakovi = array("(",")","{","}","'","!","#","“","\\","/");
    $duljina2 = sizeof($nedozvoljeniZnakovi);
    
    for ($i=0; $i<$duljina; $i++){
        for ($j=0; $j<$duljina2; $j++){
            if ($korime[$i]==$nedozvoljeniZnakovi[$j]){
                $provjera=false;
                }
            }
        }

    return $provjera;
}


//provjera znakova u EMAIL ( { # ! " / \
function ProvjeraEmaila(){
    $email=$_POST["email"];
    $provjera = true;
    $duljina = strlen($email);
    $nedozvoljeniZnakovi = array("(",")","{","}","'","!","#","“","\\","/");
    $duljina2 = sizeof($nedozvoljeniZnakovi);
    
    for ($i=0; $i<$duljina; $i++){
        for ($j=0; $j<$duljina2; $j++){
            if ($email[$i]==$nedozvoljeniZnakovi[$j]){
                $provjera=false;
                }
            }
        }

    return $provjera;
}


//provjera znakova u LOZINKA ( { # ! " / \
function ProvjeraLozinke1(){
    $lozinka1=$_POST["lozinka1"];
    $provjera = true;
    $duljina = strlen($lozinka1);
    $nedozvoljeniZnakovi = array("(",")","{","}","'","!","#","“","\\","/");
    $duljina2 = sizeof($nedozvoljeniZnakovi);
    
    for ($i=0; $i<$duljina; $i++){
        for ($j=0; $j<$duljina2; $j++){
            if ($lozinka1[$i]==$nedozvoljeniZnakovi[$j]){
                $provjera=false;
                }
            }
        }

    return $provjera;
}

//provjera znakova u POTVRDI LOZINKE
function ProvjeraLozinke2(){
    $lozinka2=$_POST["lozinka2"];
    $provjera = true;
    $duljina = strlen($lozinka2);
    $nedozvoljeniZnakovi = array("(",")","{","}","'","!","#","“","\\","/");
    $duljina2 = sizeof($nedozvoljeniZnakovi);
    
    for ($i=0; $i<$duljina; $i++){
        for ($j=0; $j<$duljina2; $j++){
            if ($lozinka2[$i]==$nedozvoljeniZnakovi[$j]){
                $provjera=false;
                }
            }
        }

    return $provjera;
}

//provjera LOZINKE dva velika, dva mala, broj i duljina 5,15
function IspravnostLozinke(){
    $lozinka=$_POST["lozinka1"];
    $provjera=true;
    $provjera1=true;
    $provjera2=true;
    $provjera3=true;
    $duljina=strlen($lozinka);
    
    if (preg_match("/\w*[A-Z]\w*[A-Z]/",$lozinka)){
        $provjera1=true;
    }
    else{
        $provjera1=false;
    }
    
    if (preg_match("/\w*[a-z]\w*[a-z]/",$lozinka)){
        $provjera2=true;
    }
    else{
        $provjera2=false;
    }
    
    if (preg_match("/\w*[0-9]/",$lozinka)){
        $provjera3=true;
    }
    else{
        $provjera3=false;
    }
    
    if ($provjera1==false || $provjera2==false || $provjera3==false || $duljina>15 || $duljina<5){
        $provjera=false;
    }
    
    return $provjera;
}

//provjera postojanja EMAIL-a u bazi
function ProvjeraPostojanjaEmaila(){
    $provjera=true;
    $veza = new Baza();
    $veza->spojiDB();

    $email=$_POST['email'];

    $sql="SELECT email FROM korisnik WHERE email='$email'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    if ($povratnaInf[0]!=""){
        $provjera=false;
    }
    else
    {
        $provjera=true;
    }
    
    $veza->zatvoriDB();
    return $provjera;
}

//provjera duljine KORISNIČKOG IMENA
function ProvjeraDuljineKorIme(){
    $korisnickoIme=$_POST["korime"];
    $provjera = true;
    $duljina = strlen($korisnickoIme);
    
    if ($duljina<5)
    {
        $provjera=false;
    }  
    else
    {
        $provjera=true; 
    }
        

    return $provjera;
}

//slanje KORISNIKA u BAZU i generiranje aktivacijskog koda - slanje na mail
function SlanjeUBazu(){
    $veza = new Baza();
    $veza->spojiDB();
    
    echo "<p id=ispravno>Uspješno ste se registrirali! Molimo provjerite e-mail kako bi aktivirali svoj račun.</p>";
    
    $tip_korisnika=3;
    $ime=$_POST["ime"];
    $prezime=$_POST["prez"];
    $korime=$_POST["korime"];
    $email=$_POST["email"];
    $lozinka=$_POST["lozinka1"];
    $prijavaKorak=$_POST["koraci"];
    $aktivan=0;
    $datumVrijemeReg=PomakniVrijeme();
    
    $salt=sha1(time());
    $kriptirana_lozinka=sha1($salt." -- ".$lozinka);
    
    $datum=sha1(date("d-m-Y"));
    $vrijeme=sha1(time());
    $korisnik=sha1($korime);
    $aktivacijski_kod=sha1($datum."--".$vrijeme."--".$korisnik);
    
    $sql= "INSERT INTO `korisnik` (id_tip_korisnik,ime,prezime,korisnicko_ime,lozinka,kriptirana_lozinka,"
            . "email,datumvrijeme_registracije,datumvrijeme_aktkod,koraci_prijave,aktivacijski_kod,aktivan,ukupno_steceni_bodovi,ukupno_potroseni_bodovi) VALUES"
            . " ('".$tip_korisnika."','".$ime."','".$prezime."','".$korime."','".$lozinka."','".$kriptirana_lozinka."',"
            . "'".$email."','".$datumVrijemeReg."','".$datumVrijemeReg."','".$prijavaKorak."','".$aktivacijski_kod."','".$aktivan."',0,0)";
    
    
    $server=$_SERVER["HTTP_HOST"];
    $putanja=$_SERVER["PHP_SELF"];
    
    $link="http://";
    $link.=$server;
    $link.=str_replace(basename($putanja),"",$putanja);
    $link.="aktivacija.php";
    
    $mail_to = $email;
    $mail_from = "e-Povezivanje interesnih skupina";
    $mail_subject = "Aktivacijski link";
    $mail_body = $link. ", Aktivacijski kod: ".$aktivacijski_kod;
    
    if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
    else {
        echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
    }
    $veza->updateDB($sql);
    
    $veza->zatvoriDB();
}

//ISPIS POGRESKE
function ProvjeraGreske(){
    $konacnaProvjera=true;
    if(ProvjeraImena()==false){
        echo "<p id=message>Upozorenje: Polje IME ne smije sadržavati nijedan od znakova ( ) { } ' ! # “ \ /</p>";
        $konacnaProvjera=false;
    }
    if(ProvjeraPrezimena()==false){
        echo "<p id=message>Upozorenje: Polje PREZIME ne smije sadržavati nijedan od znakova ( ) { } ' ! # “ \ /</p>";
        $konacnaProvjera=false;
    }
    if(ProvjeraKorImena()==false){
        echo "<p id=message>Upozorenje: Polje KORISNIČKO IME ne smije sadržavati nijedan od znakova ( ) { } ' ! # “ \ /</p>";
        $konacnaProvjera=false;
    }
    if(ProvjeraEmaila()==false){
        echo "<p id=message>Upozorenje: Polje EMAIL ne smije sadržavati nijedan od znakova ( ) { } ' ! # “ \ /</p>";
        $konacnaProvjera=false;
    }
    if(ProvjeraLozinke1()==false){
        echo "<p id=message>Upozorenje: Polje LOZINKA ne smije sadržavati nijedan od znakova ( ) { } ' ! # “ \ /</p>";
        $konacnaProvjera=false;
    }
    if(ProvjeraLozinke2()==false){
        echo "<p id=message>Upozorenje: Polje POTVRDA LOZINKE ne smije sadržavati nijedan od znakova ( ) { } ' ! # “ \ /</p>";
        $konacnaProvjera=false;
    }
    if (empty($_POST["ime"])){
        echo "<p id=message>Upozorenje: Polje IME je obavezno unijeti!</p>";
        $konacnaProvjera=false;
    }
    if (empty($_POST["prez"])){
        echo "<p id=message>Upozorenje: Polje PREZIME je obavezno unijeti!</p>";
        $konacnaProvjera=false;
    }
    if (empty($_POST["korime"])){
        echo "<p id=message>Upozorenje: Polje KORISNIČKO IME je obavezno unijeti!</p>";
        $konacnaProvjera=false;
    }
    if (empty($_POST["email"])){
        echo "<p id=message>Upozorenje: Polje EMAIL je obavezno unijeti!</p>";
        $konacnaProvjera=false;
    }
    if (empty($_POST["lozinka1"])){
        echo "<p id=message>Upozorenje: Polje LOZINKA je obavezno unijeti!</p>";
        $konacnaProvjera=false;
    }
    if (empty($_POST["lozinka2"])){
        echo "<p id=message>Upozorenje: Polje POTVRDA LOZINKE je obavezno unijeti!</p>";
        $konacnaProvjera=false;
    }
    if (IspravnostLozinke()==false){
         echo "<p id=message>Upozorenje: Lozinka mora sadržavati barem 2 velika slova, 2 mala slova i 1 broj!</p>";
         $konacnaProvjera=false;
    }
    if (ProvjeraPostojanjaEmaila()==false){
        echo "<p id=message>Upozorenje: Uneseni e-mail se već koristi! Molimo ponovite unos.</p>";
        $konacnaProvjera=false;
    }
    if (ProvjeraDuljineKorIme()==false)
        {
        echo "<p id=message>Upozorenje: Korisničko ime mora sadržavati minimalno 5 znakova.</p>";
        $konacnaProvjera=false;
        }
    if ($konacnaProvjera!=false){
        SlanjeUBazu();
    }               
}

function PomakniVrijeme(){
    $veza = new Baza();
    $veza->spojiDB();
    
    $sql="SELECT pomak_vremena FROM konfiguracija_sustava WHERE id_konfiguracije_sustava=(SELECT max(id_konfiguracije_sustava) FROM  konfiguracija_sustava)";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    $pomak=intval($povratnaInf[0]);
    $pomaknutoVrijeme=date("Y-m-d H:i:s", (time()+$pomak*60*60));
    
    $veza->zatvoriDB();
    
    return $pomaknutoVrijeme;
}

function DajImePreglednika()
{

$preglednik=$_SERVER['HTTP_USER_AGENT'];

if (strpos(strtolower($preglednik), "safari/") and strpos(strtolower($preglednik), "opr/")) {
    // OPERA
    $preglednik="Opera";
} elseIf (strpos(strtolower($preglednik), "safari/") and strpos(strtolower($preglednik), "chrome/")) {
    // CHROME
    $preglednik="Chrome";
} elseIf (strpos(strtolower($preglednik), "msie")) {
    // INTERNET EXPLORER
    $preglednik="Internet Explorer";
} elseIf (strpos(strtolower($preglednik), "firefox/")) {
    // FIREFOX
    $preglednik="Mozilla Firefox";
} elseIf (strpos(strtolower($preglednik), "safari/") and strpos(strtolower($preglednik), "opr/")==false and strpos(strtolower($preglednik), "chrome/")==false) {
    // SAFARI
    $preglednik="Safari";
} else {
    // OUT OF DATA
    $preglednik="NIJEDAN OD PONUDJENIH";
}

return $preglednik;
}

function KolacicNeregistriraniKorisnik(){
    $kolacNaziv = "neregistriraniKorisnik";
    $kolacId = "neregistriraniKorisnik";
    $kolacVrijeme = time();
    $kolacVrijediDo = $kolacVrijeme + 60 * 60 * 24 * 3;
    setcookie($kolacNaziv, $kolacId, $kolacVrijediDo);
}

if (isset($_POST["shvacam"])){
        KolacicNeregistriraniKorisnik();
        header("Location: registracija.php");
}

if (isset($_POST["posalji"]))
{
    provjeraRecaptcha();
    ProvjeraImena();
    ProvjeraPrezimena();
    ProvjeraKorImena();
    ProvjeraEmaila();
    ProvjeraLozinke1();
    ProvjeraLozinke2();
    IspravnostLozinke();
    ProvjeraPostojanjaEmaila();
    ProvjeraDuljineKorIme();
    ProvjeraGreske();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registracija</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Početna stranica">
        <meta name="author" content="Domagoj Ergović">
        <meta name="keywords" content="skupine,komentar,webdip">
        <link rel="stylesheet" type="text/css" href="css/domergovi.css">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <title></title>
    </head>
    <body>
        <header>
            <figure id="slikaZaglavlje">
                <img src="slike/homeButton.png" usemap="#mapaZaglavlje" alt="SlikaZaglavlja" width="200">
                <map name="mapaZaglavlje">
                    <area href="pocetna.php" shape="circle" alt="krug" coords="99,72,67" target="_blank"/>
                </map>
                <figcaption hidden="hidden">Slika zaglavlja</figcaption>
            </figure>
        </header>
        
        <h1 id="glavniNaslovRegistracija">REGISTRACIJA</h1>
        <h2 id="sporedniNaslov">E - POVEZIVANJE INTERESNIH SKUPINA</h2>
        
        <nav>
            <ul class="meni">
                <li>
                    <a href="pocetna.php">POČETNA</a>
                </li>
                <li>
                    <a href="registracija.php">REGISTRACIJA</a>
                </li>
                <li>
                    <a href="prijava.php">PRIJAVA</a>
                </li>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        echo '<li><a href="profilKorisnika.php">PROFIL</a></li>';
                        echo '<li><a href="pregledDiskusija.php">DISKUSIJE</a></li>';
                        echo '<li><a href="pregledKupona.php">KUPONI</a></li>';
                        echo '<li><a href="kosarica.php">KOŠARICA</a></li>';
                    }
                ?>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        if (intval($_SESSION["tipKorisnik"])==2 || intval($_SESSION["tipKorisnik"])==1){
                            echo '<li><a style="color: green;" href="stranicaModerator.php">MODERATOR</a></li>';
                        }
                    }
                ?>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        if (intval($_SESSION["tipKorisnik"])==1){
                            echo '<li><a style="color: blue;" href="pomakVremena.php">POMAK VREMENA</a></li>';
                            echo '<li><a style="color: blue;" href="stranicaAdministrator.php">ADMIN</a></li>';
                            echo '<li><a style="color: blue;" href="privatno/korisnici.php">KORISNICI</a></li>';
                            echo '<li><a style="color: blue;" href="dnevnik.php">DNEVNIK</a></li>';
                            echo '<li><a style="color: blue;" href="korisnikBodovi.php">BODOVI KORISNIKA</a></li>';
                        }
                    }
                ?>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        echo '<li><a style="color: red;" href="odjava.php">ODJAVA</a></li>';
                    }
                ?>
            </ul>
        </nav>
        
        
        <section id="sekcijaRegistracija">        
            <form id="registracija" method="post" name="registracija" action="registracija.php" novalidate>
                <p>
                    <label for="ime">Ime: </label>
                    <input type="text" id="ime" name="ime"><br>
                
                    <label for="prez">Prezime: </label>
                    <input type="text" id="prez" name="prez"><br>
                    
                    <label for="korime">Korisničko ime: </label>
                    <input type="text" id="korime" name="korime"><br>
                    
                    <label for="email">Email adresa: </label>
                    <input type="email" id="email" name="email"><br>
                    
                    <label for="lozinka1">Lozinka: </label>
                    <input type="password" id="lozinka1" name="lozinka1"><br>
                
                    <label for="lozinka2">Ponovi lozinku: </label>
                    <input type="password" id="lozinka2" name="lozinka2"><br>
                    
                    <label>Prijava u: </label>
                        <p id="paragraf_prijava">
                            <input type="radio" name="koraci" value="1" id="1korak">
                            <label for="1korak">1_korak</label><br>

                            <input type="radio" name="koraci" value="2" id="2korak">
                            <label for="2korak">2_koraka</label>
                        </p> 
                    
                    <br>
                    <input id="tipka_registracija" type="submit" value=" Pošalji podatke " name="posalji" disabled>
                    <br>
                    <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="6LcGgx8UAAAAAIVEgT2RcOOMyNczmTnXmJHzd8a8"></div>
                </p>   
            </form>
        </section>
        
        <?php
        if (!isset($_SESSION["aktivniKorisnik"]) && (!isset($_COOKIE["neregistriraniKorisnik"]))){
            echo "<br><br><form id='kolacicNeregistrirani' method='post' name='kolacicNeregistrirani' action='pocetna.php'>"
            . "<p id='tekstShvacam'>"
            . "Ova stranica koristi kolačiće za neregistrirane korisnike. "
            . "Nastavkom pregleda ove web stranice slažete se s korištenjem kolačića i prihvaćate uvjete korištenja. "
            . "<input id='shvacamKolac' type='submit' value='Shvaćam' name='shvacam'>"
            . "</p></form>";
        }
        ?>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript" src="js/domergovi_jquery.js"></script>
        
    </body>
</html>


