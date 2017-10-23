<?php
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
        <title>Diskusije i komentari</title>
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
        
        <h1 id="glavniNaslovKorisnik">PREGLED DISKUSIJA</h1>
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
        
        <section id="sekcijaPregledDiskusija">        
            <form id="pregledDiskusija" method="post" name="pregledDiskusija" action="pregledDiskusija.php">
                <p>
                    <?php
                    include ("./baza.class.php");
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    
                    $idKorisnik=$_SESSION["aktivniKorisnik"];
                    $datumVrijemePretplate=PomakniVrijeme();
                    
                    
                    echo "<label for='odabirPodrucje'>Područje interesa: </label>
                          <select id='odabirPodrucje' name='odabirPodrucje'>";
                    
                            $sql="SELECT pi.id_podrucje, pi.naziv "
                                    . "FROM podrucje_interesa as pi, odabir_podrucje_interesa as opi "
                                    . "WHERE pi.id_podrucje=opi.id_podrucje and opi.id_korisnik='$idKorisnik'";
                            $rezultat=$veza->selectDB($sql);
                        
                            while (list($id,$nazivP) = $rezultat->fetch_array()) {
                            echo "<option value='$id'>$nazivP</option>";
                        }
                    echo "</select>"
                        . "<input id='tipkaFiltriraj' type='submit' value='Filtriraj' name='tipkaFiltriraj'>";
                    
                    
                    if (isset($_POST["tipkaFiltriraj"])){
                        $podrucje = intval($_POST['odabirPodrucje']);
                        
                        $sql="SELECT id_diskusija FROM diskusija WHERE id_podrucje='$podrucje' and datum_deaktivacije>'$datumVrijemePretplate'";
                        $rezultat=$veza->selectDB($sql);
                        
                        while (list($diskusijaID) = $rezultat->fetch_array()) {
                             $sql="SELECT id_korisnik FROM pretplaceni_korisnici WHERE id_diskusija='$diskusijaID' and id_korisnik='$idKorisnik'";
                             $rezultat=$veza->selectDB($sql);
                             $vraceniRezultat=$rezultat->fetch_array();
                             $dopustenje=$vraceniRezultat[0];
                             
                             if ($dopustenje!=""){}
                             else{
                                $sql="INSERT INTO `pretplaceni_korisnici`(`id_korisnik`, `id_diskusija`, `datum_pretplate`,zabranjen_pristup) VALUES "
                                        . "('".$idKorisnik."','".$diskusijaID."','".$datumVrijemePretplate."', 0)";
                                $veza->updateDB($sql);
                             }
                        }
                        
                        $sql="SELECT id_diskusija,naziv FROM diskusija WHERE id_podrucje='$podrucje' and datum_deaktivacije>'$datumVrijemePretplate'";
                        $rezultat=$veza->selectDB($sql);

                        print "<table id=tablicaKomentari><tr><td>DISKUSIJA</td><td>KOMENTARI</td></tr>\n";

                        while (list($idDiskusija,$naziv) = $rezultat->fetch_array()) {
                            print "<tr><td>$naziv</td><td><a href='pregledDiskusija.php?diskusija=$idDiskusija'>Pregledaj</a></td></tr>\n";
                        }
                        print "</table>\n";
                    }
                    
                    if (isset($_GET['diskusija'])){
                        $diskusija=intval($_GET['diskusija']);
                        $_SESSION["trenutnaDiskusija"]=$diskusija;
                        $korisnik=$_SESSION["aktivniKorisnik"];
                        
                        
                            $sql="SELECT naziv,pravila_diskusije FROM diskusija WHERE id_diskusija='$diskusija'";
                            $rezultat=$veza->selectDB($sql);
                            $odabranaDiskusija=$rezultat->fetch_array();
                            $nazivDiskusije=$odabranaDiskusija[0];
                            $pravilo=$odabranaDiskusija[1];

                            echo "<p id=naslovDiskusije>DISKUSIJA: $nazivDiskusije</p>";

                            $sql="SELECT korisnik.korisnicko_ime, komentar.datum_objave, komentar.tekst "
                                    . "FROM korisnik,komentar "
                                    . "WHERE komentar.id_diskusija='$diskusija' and komentar.id_korisnik=korisnik.id_korisnik "
                                    . "ORDER BY datum_objave";
                            $rezultat=$veza->selectDB($sql);

                            print "<table id=tablicaKomentari><tr><td>KORISNIK</td><td>DATUM OBJAVE</td><td>KOMENTAR</td></tr>\n";

                            while (list($idKor,$datum,$komentar) = $rezultat->fetch_array()) {
                                print "<tr><td>$idKor</td><td>$datum</td><td>$komentar</td></tr>\n";
                            }
                            print "</table><br>";

                            echo "<p id=komentiranje><label for='komentar'>Komentar: </label>
                                  <textarea type='text' id='komentar' name='komentar' rows=3 cols=58 placeholder='Ovdje upišite komentar...'></textarea><br>
                                  <input id='tipkaKomentiraj' type='submit' value='Komentiraj' name='tipkaKomentiraj'></p>";
                            
                            echo "<p id=praviloDiskusije>PRAVILA: $pravilo</p>";
                        }
          
                        
                        if (isset($_POST["tipkaKomentiraj"])){
                            $trenutniKorisnik=$_SESSION["aktivniKorisnik"];
                            $trenutnoVrijeme=PomakniVrijeme();
                            $komentarKorisnika=$_POST["komentar"];
                            $trenutnaDiskusija=$_SESSION["trenutnaDiskusija"];
                            
                            if ($komentarKorisnika!=null){
                                $sql="SELECT zabranjen_pristup FROM pretplaceni_korisnici WHERE id_korisnik='$trenutniKorisnik'";
                                $rezultat=$veza->selectDB($sql);
                                $odabraniKorisnik=$rezultat->fetch_array();
                                $zabrana=intval($odabraniKorisnik[0]);
                                
                                if ($zabrana==1){
                                    echo '<p id=message>Zabranjeno Vam je komentiranje unutar diskusija!</p>';
                                }
                                else{
                                    $sql="INSERT INTO komentar (id_korisnik, id_diskusija, datum_objave, tekst)"
                                            . "VALUES('".$trenutniKorisnik."','".$trenutnaDiskusija."','".$trenutnoVrijeme."','$komentarKorisnika')";
                                    $veza->updateDB($sql);

                                    $sql="INSERT INTO `log_bodovi`(`id_korisnik`, `datum_akcije`, `id_akcije`) VALUES ('".$trenutniKorisnik."','".$trenutnoVrijeme."',3)";
                                    $veza->updateDB($sql);

                                    $sql="UPDATE `korisnik` SET ukupno_steceni_bodovi=ukupno_steceni_bodovi+2 WHERE id_korisnik='$trenutniKorisnik'";
                                    $veza->updateDB($sql);

                                    $dodajBodove=intval($_SESSION["steceniBodovi"])+2;
                                    $_SESSION["steceniBodovi"]=$dodajBodove;

                                    header("Location: pregledDiskusija.php?diskusija=".$trenutnaDiskusija);
                                }
                            }
                            else{
                                echo "<p id=message>Niste unijeli tekst komentara!</p>";
                            }
                        }
                    $veza->zatvoriDB();
                    ?>

                </p>   
            </form>
        </section>
    </body>
</html>

