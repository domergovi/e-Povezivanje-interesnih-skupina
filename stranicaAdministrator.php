<?php
    include ("./baza.class.php");
    $veza = new Baza();
    $veza->spojiDB();
    
    session_start();
    
    if (!isset($_SESSION["aktivniKorisnik"])){
        header("Location: prijava.php");
    }
    
    
    $veza->zatvoriDB();
    
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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Administrator</title>
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
        
        <h1 id="glavniNaslovKorisnik">ADMINISTRATOR</h1>
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
        
        <section id="sekcijaModeriraj">
            <form id="stranicaModerator" enctype="multipart/form-data" method="post" name="korisnikPodaci" action="stranicaAdministrator.php" novalidate>
                <h2 id="podnaslovModerator">NOVI KUPON</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    
                    echo"<p>
                    <label for='nazivKupona'>Naziv kupona: </label>
                    <input type='text' id='nazivKupona' name='nazivKupona'><br>
                    
                    <label for='datoteka'>Slika kupona: </label>
                    <input name='datoteka' type='file' /><br>
                    
                    <input id='tipka_promijeni' type='submit' value='Kreiraj kupon' name='kreirajKupon'></p><br>";
                 
                        
                        
                 if (isset($_POST["kreirajKupon"])){
                     $nazivKupon=$_POST["nazivKupona"];
                     
                     $tmpNaziv = $_FILES['datoteka']['tmp_name'];
                     $nazivDatoteke = $_FILES['datoteka']['name'];
                     $velicinaDatoteke = $_FILES['datoteka']['size'];
                     $tipDatoteke = $_FILES['datoteka']['type'];
                        
                     $file=fopen($tmpNaziv,'r');
                     $sadrzajDatoteke=fread($file, filesize($tmpNaziv));
                     $sadrzajDatoteke=addslashes($sadrzajDatoteke);
                     fclose($file);
                        
                     $sql="INSERT INTO `kupon_clanstva`(`naziv`, `pdf_dokument`,datum_aktivacije) VALUES ('$nazivKupon','$sadrzajDatoteke','')";
                     $veza->updateDB($sql);
                     
                 }
                        
                        
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        
        <section id="sekcijaModeriraj">
            <form id="stranicaModerator" method="post" name="stranicaAdministrator" action="stranicaAdministrator.php" novalidate>
                <h2 id="podnaslovModerator">NOVO PODRUČJE INTERESA</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    
                    echo"<p>
                    <label for='podrucje'>Područje interesa: </label>
                    <input type='text' id='podrucje' name='podrucje'><br>
                    
                    <label for='moderator'>Moderator: </label>
                    <select id='moderator' name='moderator' size='1'>";
                        $sql="SELECT id_korisnik,korisnicko_ime FROM korisnik WHERE id_tip_korisnik='2'";
                        $rezultat=$veza->selectDB($sql);
                        
                        while (list($id,$korime) = $rezultat->fetch_array()) {
                            echo "<option value='$id'>$korime</option>";
                        }
                        echo "</select><br><br>
                    
                    <input id='tipka_promijeni' type='submit' value='Kreiraj' name='dodajPodrucje'></p><br>";
                        
                        
                     if (isset($_POST["dodajPodrucje"])){
                         if (isset($_POST["podrucje"])){
                             $podrucje=$_POST["podrucje"];
                             $moderator=$_POST["moderator"];
                             
                             $sql="INSERT INTO podrucje_interesa (moderator,naziv) VALUES ('$moderator','$podrucje')";
                             $veza->updateDB($sql);
                             
                             echo '<p id="ispravno">Uspješno dodano novo područje interesa!</p>';
                         }
                         else{
                             echo '<p id="message">Niste unijeli naziv područja interesa!</p>';
                         }
                     }
            
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        
        <?php
            $veza = new Baza();
            $veza->spojiDB();

            $sql="SELECT korisnik.id_korisnik,korisnik.korisnicko_ime 
                FROM korisnik
                WHERE korisnik.id_korisnik 
                IN (SELECT prijava_1.id_korisnik 
                FROM prijava_1)";
            $rezultat=$veza->selectDB($sql);

                    
            print "<table id=tablicaModerator><tr><td>KORISNIK</td><td>BLOKIRAJ</td><td>ODBLOKIRAJ</td></tr>\n";

            while (list($idKorisnik,$korime) = $rezultat->fetch_array()) {
                print "<tr><td>$korime</td><td><a href='stranicaAdministrator.php?blokiraj=$idKorisnik'>Blokiraj korisnika</a></td><td><a href='stranicaAdministrator.php?odblokiraj=$idKorisnik'>Odblokiraj korisnika</a></td></tr>\n";
            }
            print "</table>\n";
            
            if (isset($_GET["blokiraj"])){
                $korisnik=$_GET["blokiraj"];
                
                $sql="UPDATE prijava_1 SET zabranjen_pristup=1 WHERE id_korisnik='$korisnik'";
                $veza->updateDB($sql);
                
            }
            
            if (isset($_GET["odblokiraj"])){
                $korisnik=$_GET["odblokiraj"];
                
                $sql="UPDATE prijava_1 SET zabranjen_pristup=0 WHERE id_korisnik='$korisnik'";
                $veza->updateDB($sql);
                
            }        
            $veza->zatvoriDB();
        ?>
        
        
    </body>
</html>

