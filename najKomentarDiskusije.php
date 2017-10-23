<!DOCTYPE html>
<html>
    <head>
        <title>E-povezivanje interesnih skupina</title>
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
        
        <h1 id="glavniNaslovDiskusije">POPIS DISKUSIJA</h1>
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
        
        
        <section>        
                    <?php
                        include ("baza.class.php");
                        $veza = new Baza();
                        $veza->spojiDB();

                        if (isset($_GET['pregledaj'])){
                            $podrucje = $_GET['pregledaj'];
                            $trenutnoDatumVrijeme=PomakniVrijeme();
                            
                            $sql="SELECT naziv,(SELECT COUNT(*) FROM komentar WHERE komentar.id_diskusija=diskusija.id_diskusija) as broj,datum_deaktivacije "
                                    . "FROM diskusija "
                                    . "WHERE id_podrucje='$podrucje' and datum_deaktivacije>'$trenutnoDatumVrijeme'"
                                    . "ORDER BY broj DESC "
                                    . "LIMIT 0,3";
                            $rezultat=$veza->selectDB($sql);



                            print "<table id=tablicaKorisnici><tr><td>DISKUSIJA</td><td>BROJ KOMENTARA</td></tr>\n";

                                while (list($naziv,$brojKomentara) = $rezultat->fetch_array()) {
                                    print "<tr><td>$naziv</td><td>$brojKomentara</td></tr>\n";
                                }
                                print "</table>\n";
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
        </section>
        
    </body>
</html>

