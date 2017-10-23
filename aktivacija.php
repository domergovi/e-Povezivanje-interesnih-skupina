<?php
//aktivacijski kod za registraciju
include ("baza.class.php");
function ProvjeraPostojanja(){
    $veza = new Baza();
    $veza->spojiDB();

    $aktivacijski_kod=$_POST['aktivacija'];

    $sql="SELECT aktivacijski_kod,id_korisnik FROM korisnik WHERE aktivacijski_kod='$aktivacijski_kod'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    if ($povratnaInf!=""){
        $korisnik=$povratnaInf[1];
        $datumVrijeme=PomakniVrijeme();
        
 
        
        $sql="UPDATE korisnik SET aktivan='1' WHERE aktivacijski_kod='$aktivacijski_kod'";
        $rezultat=$veza->updateDB($sql);
        
        $sql="INSERT INTO `log_bodovi`(`id_korisnik`, `datum_akcije`, `id_akcije`) VALUES ('".$korisnik."','".$datumVrijeme."',2)";
        $veza->updateDB($sql);
            
        $sql="UPDATE `korisnik` SET ukupno_steceni_bodovi=ukupno_steceni_bodovi+5 WHERE id_korisnik='$korisnik'";
        $veza->updateDB($sql);
        
        header('Location: prijava.php');
    }
    else{
        echo '<p id=message>Neispravan aktivacijski kod!</p>';
    }
    
    $veza->zatvoriDB();
}

function VrijemeZaAktivaciju()
{
    header('Location: aktivacija.php');
    $veza = new Baza();
    $veza->spojiDB();
    
    $provjera=false;
    
    $aktivacijski_kod=$_POST['aktivacija'];

    $sql="SELECT datumvrijeme_aktkod FROM korisnik WHERE aktivacijski_kod='$aktivacijski_kod'";
    $rezultat=$veza->selectDB($sql);
    $povratnaInf=$rezultat->fetch_array();
    $veza->zatvoriDB();
    
    $aktivacijaDo=strtotime($povratnaInf[0])+18000;
    $trenutnoVrijeme=strtotime(PomakniVrijeme());
    
    if ($trenutnoVrijeme>$aktivacijaDo)
    {   
        $provjera=true;
        
        $veza = new Baza();
        $veza->spojiDB();
        $sql="SELECT korisnicko_ime,email FROM korisnik WHERE aktivacijski_kod='$aktivacijski_kod'";
        $rezultat=$veza->selectDB($sql);
        $povratnaInf=$rezultat->fetch_array();
        
        $novoVrijemeDatum=PomakniVrijeme();
        $email=$povratnaInf[1];
        $korime=$povratnaInf[0];
        $datum=sha1(date("d-m-Y"));
        $vrijeme=sha1(time());
        $korisnik=sha1($korime);
        $noviAktivacijskiKod=sha1($datum."--".$vrijeme."--".$korisnik);
        
        $sql="UPDATE korisnik SET aktivacijski_kod='$noviAktivacijskiKod', datumvrijeme_aktkod='$novoVrijemeDatum' WHERE korisnicko_ime='$korime'";
        $veza->updateDB($sql);
        
        $link="http://";
        $link.=$_SERVER["HTTP_HOST"];
        $link.=$_SERVER["REQUEST_URI"];
        
        $mail_to = $email;
        $mail_from = "e-Povezivanje interesnih skupina";
        $mail_subject = "Aktivacijski link - novi aktivacijski kod";
        
        $mail_body = $link . "\n\n Novi aktivacijski kod: ".$noviAktivacijskiKod;
        
        
        if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
        else {
            echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
        }
        
        $veza->zatvoriDB();
        
    }
    
    else{
        $provjera=false;
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


if (isset($_POST["posalji"])){
    if (VrijemeZaAktivaciju()==true){
        echo "<p id=message>Prijašnji aktivacijski kod je istekao! Molimo provjerite e-mail kako bi dobili novi aktivacijski kod.</p>";
    }
    else{
        ProvjeraPostojanja();
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Aktivacija</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Početna stranica">
        <meta name="author" content="Domagoj Ergović">
        <meta name="keywords" content="skupine,komentar,webdip">
        <link rel="stylesheet" type="text/css" href="css/domergovi.css">
        <title></title>
    </head>
    <body>
        <p id="ispravno">Kod za aktivaciju traje 5 sati, nakon čega se, ukoliko ne aktivirate račun na vašu e-mail adresu šalje novi kod!</p>
        
        <header>
            <figure id="slikaZaglavlje">
                <img src="slike/homeButton.png" usemap="#mapaZaglavlje" alt="SlikaZaglavlja" width="200">
                <map name="mapaZaglavlje">
                    <area href="pocetna.php" shape="circle" alt="krug" coords="99,72,67" target="_blank"/>
                </map>
                <figcaption hidden="hidden">Slika zaglavlja</figcaption>
            </figure>
        </header>
        
        <h1 id="glavniNaslovAktivacija">AKTIVACIJA</h1>
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
                session_start();
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
            <form id="registracija" method="post" name="aktivacija" action="aktivacija.php" novalidate="novalidate">
                <p>
                    <label for="aktivacija">Unesite kod: </label>
                    <input type="text" id="aktivacija" name="aktivacija"><br>
                    
                    <br>
                    <input id="tipka_aktiviraj" type="submit" value="Pošalji podatke" name="posalji">
                </p>
            </form>
        </section>
        
        
    </body>
</html>

