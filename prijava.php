<?php

include ("baza.class.php");


if($_SERVER["HTTPS"] != "on"){
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

session_start();

function Skripta(){
    $server=$_SERVER["HTTP_HOST"];
    $putanja=$_SERVER["PHP_SELF"];
    
    $link="http://";
    $link.=$server;
    $link.=str_replace(basename($putanja),"",$putanja);
    $link.="prijava.php";
    
    return $link;
}

//generiraj i provjeri ispravnost nove lozinke 2 velika, 2 mala i broj
function NovaLozinka(){
$malaSlova = "abcdefghijklmnopqrstuvwxyz";
$velikaSlova = strtoupper($malaSlova);
$brojevi = "0123456789";
$znakovi = "";

$znakovi.=$malaSlova;
$znakovi.=$velikaSlova;
$znakovi.=$brojevi;

$duljina=10;
 
$duljina2 = strlen($znakovi);
$sifra = '';
 
while (!(preg_match("/\w*[A-Z]\w*[A-Z]/",$sifra) && 
      preg_match("/\w*[a-z]\w*[a-z]/",$sifra) && 
      preg_match("/\w*[0-9]/",$sifra))){
            for ($i=0;$i<$duljina;$i++){
                $sifra .= substr($znakovi, rand(0, $duljina2-1), 1);
            }
            $sifra = str_shuffle($sifra);
}
return $sifra;
}

//kreiraj kolacic za prikaz zadnjeg prijavljenog korisnika, traje 30 dana
function Kolacic($korisnik){
    $kolacNaziv = "Korisnik";
    $kolacId = $korisnik;
    $kolacVrijeme = strtotime(PomakniVrijeme());
    $kolacVrijediDo = $kolacVrijeme + 60 * 60 * 24 * 30;
    setcookie($kolacNaziv, $kolacId, $kolacVrijediDo);
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
} elseIf (strpos(strtolower($preglednik), "firefox/")) {
    // FIREFOX
    $preglednik="Mozilla Firefox";
} elseIf (strpos(strtolower($preglednik), "safari/") and strpos(strtolower($preglednik), "opr/")==false and strpos(strtolower($preglednik), "chrome/")==false) {
    // SAFARI
    $preglednik="Safari";
} else {
    //INTERNET EXPLORER
    $preglednik="Internet Explorer";
}

return $preglednik;
}

function BlokiranKorisnik(){
    $veza = new Baza();
    $veza->spojiDB();
    $provjera=true;
    
    $korime=$_POST['korime'];
    
    $sql="SELECT prijava_1.zabranjen_pristup as zabrana
          FROM korisnik, prijava_1
          WHERE korisnik.id_korisnik=prijava_1.id_korisnik AND korisnik.korisnicko_ime='$korime'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    $zabrana=intval($povratnaInf[0]);
    
    if ($zabrana==1){
        $provjera=false;
    }
    else{
        $provjera=true;
        }
    
    $veza->zatvoriDB();
    return $provjera;
}

// prijava opcenito 1,2 koraka i blokiranje
function ProvjeraKorisnika(){
    $veza = new Baza();
    $veza->spojiDB();  

    $korime=$_POST['korime'];
    $lozinka=$_POST['lozinka'];
    $petminutniKod=sha1($korime."--".$lozinka);
    
    $sql="SELECT koraci_prijave,email,id_korisnik,aktivan,korisnicko_ime,id_tip_korisnik,ime,prezime,"
            . "lozinka,ukupno_steceni_bodovi,ukupno_potroseni_bodovi "
            . "FROM korisnik WHERE korisnicko_ime='$korime' and lozinka='$lozinka'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    $koraci=$povratnaInf[0];
    $email=$povratnaInf[1];
    $id_korisnik=$povratnaInf[2];
    $aktivan=$povratnaInf[3];
    $korisnikKolacic=$povratnaInf[4];
    $tipKorisnika=$povratnaInf[5];
    $ime=$povratnaInf[6];
    $prezime=$povratnaInf[7];
    $lozinkaKorisnik=$povratnaInf[8];
    $steceniBodovi=$povratnaInf[9];
    $potroseniBodovi=$povratnaInf[10];
    
    $datumVrijeme=PomakniVrijeme();
    $preglednik=DajImePreglednika();
    
    $zabranjenPristupNe=0;
    $zabranjenPristupDa=1;
    
    
        if ($povratnaInf!=""){
            if ($aktivan==0)
                {
                echo "<p id=message>Potrebno je aktivirati korisnički račun!</p>";
                }
            if ($povratnaInf[0]=='1' && $aktivan==1){
                echo "<p id=ispravno>Uspješna prijava</p>";

                header('Location: profilKorisnika.php');

                $_SESSION["aktivniKorisnik"]=$id_korisnik;
                $_SESSION["tipKorisnik"]=$tipKorisnika;
                $_SESSION["ime"]=$ime;
                $_SESSION["prezime"]=$prezime;
                $_SESSION["email"]=$email;
                $_SESSION["lozinka"]=$lozinkaKorisnik;
                $_SESSION["steceniBodovi"]=$steceniBodovi;
                $_SESSION["potroseniBodovi"]=$potroseniBodovi;
                $_SESSION["aktivan"]=$aktivan;
                $_SESSION["korime"]=$korisnikKolacic;
                $_SESSION["koraci"]=$koraci;

                //prijavljeni korisnik u kolacic
                if(isset($_COOKIE['Korisnik'])){
                    unset($_COOKIE['Korisnik']);
                }
                Kolacic($korisnikKolacic);

                $korisnikID=$_SESSION["aktivniKorisnik"];
                $datumVrijeme=PomakniVrijeme();
                $preglednik=DajImePreglednika();
                $skripta=Skripta();

                $sql="INSERT INTO `log_bodovi`(`id_korisnik`, `datum_akcije`, `id_akcije`) VALUES ('".$korisnikID."','".$datumVrijeme."',1)";
                $veza->updateDB($sql);

                $sql="UPDATE `korisnik` SET ukupno_steceni_bodovi=ukupno_steceni_bodovi+1 WHERE id_korisnik='$korisnikID'";
                $veza->updateDB($sql);
                
                $dodajBodove=intval($_SESSION["steceniBodovi"])+1;
                $_SESSION["steceniBodovi"]=$dodajBodove;

                $sql="INSERT INTO `log_aplikacije`(`id_korisnik`, `datum_pristupa`, `skripta`, `preglednik`) VALUES ('".$korisnikID."','".$datumVrijeme."','".$skripta."','".$preglednik."')";
                $veza->updateDB($sql);
                
                $sql="SELECT id_korisnik FROM prijava_1 WHERE id_korisnik='$id_korisnik'";
                $rezultat=$veza->selectDB($sql);
                $vraceniKorisnik=$rezultat->fetch_array();

                if ($vraceniKorisnik!="") //sve ostale prijave osim prve prijava 1 korak
                {
                    $sql="UPDATE prijava_1 SET datum_prijave='$datumVrijeme', zabranjen_pristup='$zabranjenPristupNe' WHERE id_korisnik='$id_korisnik'";
                    $veza->updateDB($sql);

                    $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$id_korisnik."','".$datumVrijeme."','UPDATE prijava_1')";
                    $veza->updateDB($sql);
                }
                else //prva prijava u sustav prijava 1 korak
                {
                    $sql= "INSERT INTO prijava_1 (id_korisnik,datum_prijave,zabranjen_pristup) VALUES ('".$id_korisnik."','".$datumVrijeme."','".$zabranjenPristupNe."')";
                    $veza->updateDB($sql);

                    $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$id_korisnik."','".$datumVrijeme."','INSERT INTO prijava_1')";
                    $veza->updateDB($sql);
                }

                $sql="INSERT INTO `log_aplikacije_sustav`(`id_korisnik`, `datum_pristupa`, `datum_odjave`) VALUES ('".$id_korisnik."','".$datumVrijeme."','')";
                $veza->updateDB($sql);



            }
            if ($povratnaInf[0]=='2' && $aktivan==1){

                //korisnik u kolacic
                if(isset($_COOKIE['Korisnik'])){
                    unset($_COOKIE['Korisnik']);
                }
                Kolacic($korisnikKolacic);

                $datumVrijemeAktivacija=PomakniVrijeme();
                $datumVrijemeKraj=strtotime($datumVrijemeAktivacija)+300;
                $datumVrijemeDeaktivacija=date("Y-m-d H:i:s",$datumVrijemeKraj);

                $sql="SELECT id_korisnik FROM prijava_2 WHERE id_korisnik=$id_korisnik";
                $rezultat=$veza->selectDB($sql);
                $vraceniKorisnik=$rezultat->fetch_array();

                if ($vraceniKorisnik!="")
                {
                    $sql="SELECT datum_deaktivacije FROM prijava_2 WHERE id_korisnik=$id_korisnik";
                    $rezultat=$veza->selectDB($sql);
                    $vraceniDatum=$rezultat->fetch_array();
                    $datumVrijemeDeaktTablica=strtotime($vraceniDatum[0]);
                    $trenutnoVrijeme=strtotime(PomakniVrijeme());

                    //proslo vrijeme od 5 minuta za aktivaciju prijave 2
                    if ($trenutnoVrijeme>$datumVrijemeDeaktTablica){
                        echo '<p id=message>Vrijeme za aktivaciju koda je isteklo! Provjerite e-mail kako bi mogli unijeti novi kod.</p>';

                        $novipetminutniKod=sha1($korime."--".$trenutnoVrijeme);
                        $mail_to = $email;
                        $mail_from = "E-povezivanje interesnih skupina";
                        $mail_subject = "Novi petminutni kod za prijavu u sustav";
                        $mail_body = "Novi petminutni kod: ".$novipetminutniKod;

                        if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
                        else {
                            echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
                        }

                        
                        $sql="UPDATE prijava_2 SET datum_aktivacije='$datumVrijemeAktivacija',datum_deaktivacije='$datumVrijemeDeaktivacija',pet_minutni_kod='$novipetminutniKod' WHERE id_korisnik='$id_korisnik'";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$id_korisnik."','".$datumVrijemeAktivacija."','UPDATE prijava_2')";
                        $veza->updateDB($sql);
                    }


                    else{
                        echo '<p id=ispravno>Prijašnji petminutni kod još uvijek vrijedi!</p>';
                    } 
                }
                else //Korisnik prvi put dobiva petminutni kod
                {
                    $mail_to = $email;
                    $mail_from = "E-povezivanje interesnih skupina";
                    $mail_subject = "Petminutni kod za prijavu u sustav";
                    $mail_body = "Petminutni kod: ".$petminutniKod;
                    
                    if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
                    else{
                         echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
                        }
                        
                    $datumVrijeme=  PomakniVrijeme();
                    $sql= "INSERT INTO prijava_1 (id_korisnik,datum_prijave,zabranjen_pristup) VALUES ('".$id_korisnik."','".$datumVrijeme."','0')";
                    $veza->updateDB($sql);

                    $sql= "INSERT INTO prijava_2 (id_korisnik,datum_aktivacije,datum_deaktivacije,pet_minutni_kod) VALUES ('".$id_korisnik."','".$datumVrijemeAktivacija."','".$datumVrijemeDeaktivacija."','".$petminutniKod."')";
                    $veza->updateDB($sql);

                    $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$id_korisnik."','".$datumVrijemeAktivacija."','INSERT INTO prijava_2')";
                    $veza->updateDB($sql);


                }

                echo"<section id=sekcijaRegistracija>
                    <form id='registracija' method='post' name='prijavaKod' action='prijava.php'>
                        <p>
                            <label for='jed_kod'>Petminutni kod: </label>
                            <input type='text' id='jed_kod' name='jed_kod'><br>
                            <input id='tipka_aktiviraj' type='submit' value='Pošalji kod' name='aktiviraj'>
                        <p>
                     </form>
                     </section>";

            }
       }

        if($povratnaInf==""){
            echo '<p id=message>Ne ispravan unos korisničkog imena ili lozinke!</p>';

            //zabiljezi krivu prijavu u tablicu
            $sql= "INSERT INTO `uspjesna_prijava` (korisnicko_ime, uspjesna_prijava) VALUES ('".$korime."','1')";
            $veza->updateDB($sql);

            $sql= " SELECT COUNT(korisnicko_ime) FROM `uspjesna_prijava` WHERE korisnicko_ime='$korime' and uspjesna_prijava='1'";
            $rezultat=$veza->selectDB($sql);
            $povratnaInf=$rezultat->fetch_array();

            if ($povratnaInf[0]%3==0){
                 echo '<p id=message>Pažnja! Ovo je treća neuspješna prijava u sustav te će Vaš račun ukoliko postoji od sada biti blokiran!</p>';

                 $sql="SELECT id_korisnik FROM korisnik WHERE korisnicko_ime='$korime'";
                 $rezultat=$veza->selectDB($sql);
                 $vraceniKorisnik=$rezultat->fetch_array();
                 $id=$vraceniKorisnik[0];
                 $datumVrijeme=PomakniVrijeme();

                 if ($vraceniKorisnik!="")
                 {
                    $sql="SELECT id_korisnik FROM prijava_1 WHERE id_korisnik='$id'";
                    $rezultat=$veza->selectDB($sql);
                    $vraceniKorisnikPrijava=$rezultat->fetch_array();
                    $skripta=Skripta();

                    if ($vraceniKorisnikPrijava!=""){
                        $sql="UPDATE prijava_1 SET zabranjen_pristup='$zabranjenPristupDa',datum_prijave='$datumVrijeme' WHERE id_korisnik='$id'";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije`(`id_korisnik`, `datum_pristupa`, `skripta`, `preglednik`) VALUES ('".$id."','".$datumVrijeme."','".$skripta."','".$preglednik."')";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije_ostalo`(`id_korisnik`, `datum_pristupa`, `opis_radnje`) VALUES ('".$id."','".$datumVrijeme."','Blokiran korisnički račun')";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$id."','".$datumVrijeme."','UPDATE prijava_1')";
                        $veza->updateDB($sql);
                    }
                    else{
                        $sql= "INSERT INTO prijava_1 (id_korisnik,datum_prijave,zabranjen_pristup) VALUES ('".$id."','".$datumVrijeme."','".$zabranjenPristupDa."')";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije`(`id_korisnik`, `datum_pristupa`, `skripta`, `preglednik`) VALUES ('".$id."','".$datumVrijeme."','".$skripta."','".$preglednik."')";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije_ostalo`(`id_korisnik`, `datum_pristupa`, `opis_radnje`) VALUES ('".$id."','".$datumVrijeme."','Blokiran korisnički račun')";
                        $veza->updateDB($sql);

                        $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$id."','".$datumVrijeme."','INSERT INTO prijava_1')";
                        $veza->updateDB($sql);
                    }

                 }
                 else
                 {
                    echo '<p id=message>Korisnik kojeg ste 3 puta neuspješno unijeli ne postoji u bazi!</p>';
                 }
            }
        }
    
    $veza->zatvoriDB();
}

//jel upisan ispravan petominutni kod 
function ProvjeraPostojanjaPetMinKoda(){
    $veza = new Baza();
    $veza->spojiDB();

    $petMinutniKod=$_POST['jed_kod'];

    $sql="SELECT pet_minutni_kod,id_korisnik FROM prijava_2 WHERE pet_minutni_kod='$petMinutniKod'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    $korisnikID=$povratnaInf[1];
    $datumVrijeme=PomakniVrijeme();
    $preglednik=DajImePreglednika();
    
    
    if ($povratnaInf!=""){
        $sql="SELECT id_tip_korisnik,korisnicko_ime,ime,prezime,email,lozinka,ukupno_steceni_bodovi,"
                . "ukupno_potroseni_bodovi,aktivan,koraci_prijave FROM korisnik WHERE id_korisnik='$korisnikID'";
        $rezultat=$veza->selectDB($sql);
        $vraceniKorisnik=$rezultat->fetch_array();
        $tipKorisnika=$vraceniKorisnik[0];
        $korime=$vraceniKorisnik[1];
        $ime=$vraceniKorisnik[2];
        $prezime=$vraceniKorisnik[3];
        $email=$vraceniKorisnik[4];
        $lozinkaKorisnik=$vraceniKorisnik[5];
        $steceniBodovi=$vraceniKorisnik[6];
        $potroseniBodovi=$vraceniKorisnik[7];
        $aktivan=$vraceniKorisnik[8];
        $koraci=$vraceniKorisnik[9];
        
        $_SESSION["aktivniKorisnik"]=$korisnikID;
        $_SESSION["tipKorisnik"]=$tipKorisnika;
        $_SESSION["ime"]=$ime;
        $_SESSION["prezime"]=$prezime;
        $_SESSION["email"]=$email;
        $_SESSION["lozinka"]=$lozinkaKorisnik;
        $_SESSION["steceniBodovi"]=$steceniBodovi;
        $_SESSION["potroseniBodovi"]=$potroseniBodovi;
        $_SESSION["aktivan"]=$aktivan;
        $_SESSION["korime"]=$korime;
        $_SESSION["koraci"]=$koraci;
        
        $skripta=Skripta();
        $korisnik=$_SESSION["aktivniKorisnik"];
        
        $sql="UPDATE prijava_1 SET datum_prijave='$datumVrijeme', zabranjen_pristup='0' WHERE id_korisnik='$korisnik'";
                        $veza->updateDB($sql);
        
        $sql="INSERT INTO `log_bodovi`(`id_korisnik`, `datum_akcije`, `id_akcije`) VALUES ('".$korisnikID."','".$datumVrijeme."',1)";
        $veza->updateDB($sql);
            
        $sql="UPDATE `korisnik` SET ukupno_steceni_bodovi=ukupno_steceni_bodovi+1 WHERE id_korisnik='$korisnikID'";
        $veza->updateDB($sql);
        
        $dodajBodove=intval($_SESSION["steceniBodovi"])+1;
        $_SESSION["steceniBodovi"]=$dodajBodove;
        
        $sql="INSERT INTO `log_aplikacije`(`id_korisnik`, `datum_pristupa`, `skripta`, `preglednik`) VALUES ('".$korisnikID."','".$datumVrijeme."','".$skripta."','".$preglednik."')";
        $veza->updateDB($sql);
        
        $sql="INSERT INTO `log_aplikacije_sustav`(`id_korisnik`, `datum_pristupa`, `datum_odjave`) VALUES ('".$korisnikID."','".$datumVrijeme."','')";
        $veza->updateDB($sql);
        
        header('Location: profilKorisnika.php');
    }
    else{
        echo '<p id=message>Neispravano unesen petominutni kod!</p>';
        
    }
    
    $veza->zatvoriDB();
}

function PosaljiNovuLozinku(){
    $veza = new Baza();
    $veza->spojiDB();
    
    $novaLozinka= NovaLozinka();
    
    $korisnickoIme=$_POST['korime'];
    $kriptiranaLozinka=sha1($korisnickoIme."--".$novaLozinka);
    
    $sql="SELECT email,id_korisnik FROM korisnik WHERE korisnicko_ime='$korisnickoIme'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    $email=$povratnaInf[0];
    $korisnikID=$povratnaInf[1];
    $datumVrijeme=PomakniVrijeme();
    $preglednik=DajImePreglednika();
    $skripta=Skripta();
    
    if ($povratnaInf!=""){
        $mail_to = $email;
        $mail_from = "E-povezivanje interesnih skupina";
        $mail_subject = "Zahtjev za novom lozinkom";
        $mail_body = "Nova lozinka: ".$novaLozinka;

        if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
        else{
            echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
        }
        
        $sql="UPDATE korisnik SET lozinka='$novaLozinka',kriptirana_lozinka='$kriptiranaLozinka' WHERE korisnicko_ime='$korisnickoIme'";
        $veza->updateDB($sql);
        
        $sql="INSERT INTO `log_aplikacije`(`id_korisnik`, `datum_pristupa`, `skripta`, `preglednik`) VALUES ('".$korisnikID."','".$datumVrijeme."','".$skripta."','".$preglednik."')";
        $veza->updateDB($sql);
        
        $sql="INSERT INTO `log_aplikacije_ostalo`(`id_korisnik`, `datum_pristupa`, `opis_radnje`) VALUES ('".$korisnikID."','".$datumVrijeme."','Zahtjev za novom lozinkom')";
        $veza->updateDB($sql);
        
        $sql="INSERT INTO `log_aplikacije_baza`(`id_korisnik`, `datum_pristupa`, `upit`) VALUES ('".$korisnikID."','".$datumVrijeme."','UPDATE korisnik')";
        $veza->updateDB($sql);
        
        
        echo '<p id=ispravno>Vaša lozinka uspješno je promijenjena i poslana na Vašu e-mail adresu!</p>';
    }

    else{
        echo '<p id=message>Uneseni korisnik ne postoji u bazi podataka!</p>';
    }
    
    $veza->zatvoriDB();
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

function KolacicNeregistriraniKorisnik(){
    $kolacNaziv = "neregistriraniKorisnik";
    $kolacId = "neregistriraniKorisnik";
    $kolacVrijeme = time();
    $kolacVrijediDo = $kolacVrijeme + 60 * 60 * 24 * 3;
    setcookie($kolacNaziv, $kolacId, $kolacVrijediDo);
}



if (isset($_POST["shvacam"])){
        KolacicNeregistriraniKorisnik();
        header("Location: prijava.php");
}


if (isset($_POST["zaborav"])){
    PosaljiNovuLozinku();
}

if (isset($_POST["posalji"])){
    if (BlokiranKorisnik()==false){
        header('Refresh: 3; url=pocetna.php');
        echo '<p id=message>Vaš račun je blokiran stoga ne možete pristupiti sustavu!</p>';
    }
    else{
        if (isset($_SESSION["aktivniKorisnik"])){
        echo '<p id=message>Već postoji korisnik prijavljen u sustavu!</p>';
        }
        else{
            ProvjeraKorisnika();
        }
    }
}

if (isset($_POST["aktiviraj"])){
    ProvjeraPostojanjaPetMinKoda();
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Prijava</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Početna stranica">
        <meta name="author" content="Domagoj Ergović">
        <meta name="keywords" content="skupine,komentar,webdip">
        <link rel="stylesheet" type="text/css" href="css/domergovi.css">
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
        
        <h1 id="glavniNaslov">PRIJAVA</h1>
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
        
        
        <section id="sekcijaPrijava">
            <form id="prijava" method="post" name="prijava" action="prijava.php">
                <p>
                    <label for="korime">Korisničko ime: </label>
                    <input type="text" id="korime" name="korime" value="<?php if(isset($_COOKIE['Korisnik'])){ echo ($_COOKIE['Korisnik']);}?>"><br>
                    
                    <label for="lozinka">Lozinka: </label>
                    <input type="password" id="lozinka" name="lozinka"><br><br>

                    <!--<label>Zapamti me: </label><br>
                    <input type="radio" name="zapamti" value="DA" checked="checked" id="zapamti_da">
                    <label for="zapamti_da">DA</label><br>

                    <input type="radio" name="zapamti" value="NE" id="zapamti_ne">
                    <label for="zapamti_ne">NE</label>-->
                                        
                    <input id="prijavi_me" type="submit" value=" Prijavi se " name="posalji"><br>
                    <input id="prijavi_me" type="submit" value=" Zaboravio/la sam lozinku" name="zaborav"><br>
                    <a id="registriraj_me" href="registracija.php">REGISTRACIJA</a>
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
        
    </body>
</html>
