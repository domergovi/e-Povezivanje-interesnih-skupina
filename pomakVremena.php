<?php
    include ("baza.class.php");
    $veza = new Baza();
    $veza->spojiDB();
    
    session_start();
    if (isset($_SESSION["aktivniKorisnik"])){
        $tipKorisnika=intval($_SESSION["tipKorisnik"]);
        if ($tipKorisnika!=1){
            header("Location: prijava.php");
        }   
        
        if (isset($_POST["postavi"])){
        $url = "http://barka.foi.hr/WebDiP/pomak_vremena/pomak.php?format=xml";
        $xml = simplexml_load_file($url);
        $pomak = $xml -> vrijeme -> pomak -> brojSati;
        
        $sql="INSERT INTO `konfiguracija_sustava`(`pomak_vremena`) VALUES ('".$pomak."')";
        $veza->updateDB($sql);
        
        echo '<p id=ispravno>Uspješno postavljen novi pomak vremena!<p>';
        
        $veza->zatvoriDB();
    }
    }
    
    else{
        header("Location: prijava.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Pomak vremena</title>
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
        
        <h1 id="glavniNaslovPomak">POMAK VREMENA</h1>
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
            <form id="registracija" method="post" name="pomakVremena" action="pomakVremena.php" novalidate="novalidate">
                <p>
                    <a id="registriraj_me" target="_blank" href="http://barka.foi.hr/WebDiP/pomak_vremena/vrijeme.html">UNESI POMAK</a>
                    <br><br>
                    <input id="tipka_registracija" type="submit" value="Postavi vrijeme" name="postavi">
                </p>
            </form>
        </section>
        
        
    </body>
</html>
