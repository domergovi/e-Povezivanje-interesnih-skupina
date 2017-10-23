<?php
    include ("./baza.class.php");
    session_start();
    
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

if (isset($_GET["kuponID"])){
    $idKupon=  intval($_GET["kuponID"]);
        unset($_SESSION["odabraniKuponi"][$idKupon]);
    }

if (isset($_POST["tipkaPotvrdiOdabir"])){
    $veza = new Baza();
    $veza->spojiDB();
    
    $sql="SELECT max(id_kosarica) FROM kosarica";
    $rezultat=$veza->selectDB($sql);
    $vracenaKosarica=$rezultat->fetch_array();
    $idKosarica=intval($vracenaKosarica[0])+1;
    $idKorisnik=intval($_SESSION["aktivniKorisnik"]);
    $datumVrijeme=PomakniVrijeme();
    
    $generiraniKod=sha1($idKorisnik."--".$datumVrijeme);
    
    $odabraniKuponi=$_SESSION['odabraniKuponi'];
    if ($odabraniKuponi!=null){
        $sumaBodova=0;
            foreach($odabraniKuponi as $kuponID => $nazivKupona){
                $sql="SELECT potrebni_bodovi FROM kupon_clanstva WHERE id_kupon='$kuponID'";
                $rezultat=$veza->selectDB($sql);
                $vraceniBodovi=$rezultat->fetch_array();
                $bodovi=intval($vraceniBodovi[0]);
                $sumaBodova=$sumaBodova+$bodovi;

            }
        $steceniBodovi=intval($_SESSION["steceniBodovi"]);
        $potroseniBodovi=intval($_SESSION["potroseniBodovi"]);

        if ($steceniBodovi>=$sumaBodova){
            echo '<p id=ispravno>Transakcija uspješno izvršena!</p>';
            $sql="INSERT INTO kosarica (id_kosarica,id_korisnik,datum_odabira) "
                . "VALUES ('".$idKosarica."','".$idKorisnik."','".$datumVrijeme."')";
            $veza->updateDB($sql);

            foreach($odabraniKuponi as $kuponID => $nazivKupona){
                $sql="INSERT INTO kupovina (id_kosarica,id_kupon,datum_kupovine,generirani_kod) "
                        . "VALUES('".$idKosarica."','".$kuponID."','".$datumVrijeme."','".$generiraniKod."')";
                $veza->updateDB($sql);
                $id_kupona = intval($kuponID);
                unset($_SESSION["odabraniKuponi"][$id_kupona]);
            }
            
            
            $sql="UPDATE korisnik SET ukupno_steceni_bodovi=ukupno_steceni_bodovi-'$sumaBodova', "
                    . "ukupno_potroseni_bodovi=ukupno_potroseni_bodovi+'$sumaBodova' WHERE id_korisnik='$idKorisnik'";
            $veza->updateDB($sql);

            $oduzmiBodove=intval($_SESSION["steceniBodovi"])-$sumaBodova;
            $_SESSION["steceniBodovi"]=$oduzmiBodove;

            $dodajBodove=intval($_SESSION["potroseniBodovi"])+$sumaBodova;
            $_SESSION["potroseniBodovi"]=$dodajBodove;
            
            
            
            $sql="INSERT INTO `log_bodovi`(`id_korisnik`, `datum_akcije`, `id_akcije`) VALUES ('".$idKorisnik."','".$datumVrijeme."',4)";
            $veza->updateDB($sql);

            $sql="UPDATE `korisnik` SET ukupno_steceni_bodovi=ukupno_steceni_bodovi+1 WHERE id_korisnik='$idKorisnik'";
            $veza->updateDB($sql);

            $dodajBod=intval($_SESSION["steceniBodovi"])+1;
            $_SESSION["steceniBodovi"]=$dodajBod;
        }

        else{
            echo '<p id=message>Nemate dovoljan broj bodova za kupovinu odabranih kupona!</p>';
        }
    }
    else{echo '<p id=message>Košarica je prazna!</p>';}
    
    $veza->zatvoriDB();
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Košarica</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Početna stranica">
        <meta name="author" content="Domagoj Ergović">
        <meta name="keywords" content="skupine,komentar,webdip">
        <link rel="stylesheet" type="text/css" href="css/domergovi.css">
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
        
        <h1 id="glavniNaslovKosarica">KOŠARICA</h1>
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
        
        
        <section id="sekcijaKupon">
            <form id="pregledKupona" method="post" name="pregledKosarice" action="kosarica.php" novalidate>
                <h2 id="podnaslovKuponKosarica">ODABRANI KUPONI</h2>
                <?php

                    $veza = new Baza();
                    $veza->spojiDB();
                    $korisnik=$_SESSION["aktivniKorisnik"];
                    
                    if (isset($_SESSION["odabraniKuponi"])){
                        $odabraniKuponi=$_SESSION['odabraniKuponi'];
                        foreach($odabraniKuponi as $kuponID => $nazivKupona){
                            echo "<div id='paragrafKupon' style='float: left;'>";
                            echo "<figure id='slikaKupona'>";
                                echo "<img id='kupon' src='slike/sale.png' alt='slikaKupona' width='200' height='200'>";
                            echo "<figcaption id='figcaptKupon'>Kupon: <br><a href='pregledKupona.php?pregled=$kuponID'>$nazivKupona</a></figcaption><br>";
                            echo "<a id='makniIzKosarice' href='kosarica.php?kuponID=$kuponID'>Ukloni iz košarice</a>";
                            echo "</figure></div>";
                        }
                    }
                    echo "<input id='tipkaPotvrdiOdabir' type='submit' value='Obavi transakciju' name='tipkaPotvrdiOdabir'>";
                    
                    $veza->zatvoriDB();
                ?>
            </form>
        </section>
       
    </body>
</html>
