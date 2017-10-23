<?php
include ("baza.class.php");

if (isset($_SESSION["aktivniKorisnik"])){
    if (intval($_SESSION["tipKorisnik"])==2 || intval($_SESSION["tipKorisnik"])==1){}
    else{
        header("Location: pocetna.php");
    }
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
        header("Location: pocetna.php");
}
?>
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
        
        <h1 id="glavniNaslovPocetna">POČETNA STRANICA</h1>
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
                <li>
                    <a href="o_autoru.html">O AUTORU</a>
                </li>
                <li>
                    <a href="dokumentacija.html">DOKUMENTACIJA</a>
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
                $veza = new Baza();
                $veza->spojiDB();

                $sql="SELECT naziv,id_podrucje FROM podrucje_interesa";
                $rezultat=$veza->selectDB($sql);
             
                print "<table id=tablicaKorisnici><tr><td>PODRUČJE INTERESA</td><td>PREGLED DISKUSIJA</td></tr>\n";

                while (list($naziv,$id_podrucje) = $rezultat->fetch_array()) {
                    print "<tr><td>$naziv</td><td><a href='najKomentarDiskusije.php?pregledaj=$id_podrucje'>Pregledaj</a></td></tr>\n";
                }
                print "</table>\n";

                $veza->zatvoriDB();
            ?>
        </section>
        
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/hr_HR/sdk.js#xfbml=1&version=v2.9";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        
        <br><br><div class="fb-share-button" data-href="http://barka.foi.hr/WebDiP/2016_projekti/WebDiP2016x036/pocetna.php" 
             data-layout="button" data-size="large" data-mobile-iframe="true">
            <a class="fb-xfbml-parse-ignore" target="_blank" 
               href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fbarka.foi.hr%2FWebDiP%2F2016_projekti%2FWebDiP2016x036%2Fpocetna.php&amp;src=sdkpreparse">
                Podijeli</a></div>
        
        <a href="https://twitter.com/share" class="twitter-share-button" data-show-count="false">Tweet</a>
        <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
        
        
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

