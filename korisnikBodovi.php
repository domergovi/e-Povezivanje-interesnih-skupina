<!DOCTYPE html>
<html>
    <head>
        <title>Bodovi korisnika</title>
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
        
        
        <h1 id="glavniNaslovKorisnikBodovi">BODOVI KORISNIKA</h1>
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
            <form id="korisnikBodovi" method="post" name="korisnikBodovi" action="korisnikBodovi.php" novalidate>
            <?php
                include ("./baza.class.php");
                $veza = new Baza();
                $veza->spojiDB();
                    
                    echo "<div id=odabirKorisnika><label for='korisnik'>Korisnik: </label>
                    <select id='korisnik' name='korisnik' size='1'>";
                        $sql='SELECT id_korisnik,korisnicko_ime FROM korisnik';
                        $rezultat=$veza->selectDB($sql);
                        
                        while (list($id,$korime) = $rezultat->fetch_array()) {
                            echo "<option value='$id'>$korime</option>";
                        }
                        echo "</select><input id='tipka_promijeni' type='submit' value='Pregledaj' name='pregledaj'></div>";
                    
                    if (isset($_POST["pregledaj"])){
                        $korisnik=$_POST["korisnik"];
                        
                        $sql="SELECT log_bodovi.id_korisnik,log_bodovi.datum_akcije,vrsta_akcije.naziv,vrsta_akcije.broj_bodova
                                FROM log_bodovi,vrsta_akcije
                                WHERE log_bodovi.id_akcije=vrsta_akcije.id_akcije AND log_bodovi.id_korisnik='$korisnik'"; 
                        $rezultat=$veza->selectDB($sql);

                        print "<table id=tablicaKorisnici><tr><td>ID KORISNIKA</td><td>DATUM AKCIJE</td><td>VRSTA AKCIJE</td><td>BROJ BODOVA</td></tr>\n";

                        while (list($id,$datum,$vrsta, $bodovi) = $rezultat->fetch_array()) {
                            print "<tr><td>$id</td><td>$datum</td><td>$vrsta</td><td>$bodovi</td></tr>\n";
                        }
                        print "</table>";
                        
                        $sql="SELECT ukupno_steceni_bodovi,ukupno_potroseni_bodovi FROM korisnik WHERE id_korisnik='$korisnik'";
                        $rezultat=$veza->selectDB($sql);
                        $vraceniKorisnik=$rezultat->fetch_array();
                        $steceniBodovi=$vraceniKorisnik[0];
                        $potroseniBodovi=$vraceniKorisnik[1];
                        
                        echo "<p id=skupljeniBodoviKorisnik>Skupljeni bodovi: $steceniBodovi</p>";
                        echo "<p id=potroseniBodoviKorisnik>Potrošeni bodovi: $potroseniBodovi</p>";
                    }
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
    </body>
</html>
