<!DOCTYPE html>
<html>
    <head>
        <title>Odjava</title>
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
        
        <h1 id="glavniNaslov">ODJAVA</h1>
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
        
        <section id="sekcijaOdjava">      
            <form id="registracija" method="post" name="odjava" action="odjava.php"  novalidate="novalidate">
                <p>
                    <?php
                        include ("baza.class.php");
                        
                        if (isset($_SESSION["aktivniKorisnik"])){
                            $korisnik=$_SESSION["aktivniKorisnik"];
                            session_unset(); 
                            session_destroy();
                            echo '<p id=odjavaIspravno>Uspješna odjava iz sustava!</p>';
                            
                            $veza = new Baza();
                            $veza->spojiDB();
                            $datumVrijeme=PomakniVrijeme();
                            
                            $sql="UPDATE `log_aplikacije_sustav` SET `datum_odjave`='$datumVrijeme' WHERE `datum_odjave`='0000-00-00 00:00:00' AND id_korisnik='$korisnik'";
                            $veza->updateDB($sql);
                            
                            $veza->zatvoriDB();
                            
                            header('Location: pocetna.php');
                            }
                        else{
                            echo '<p id=odjavaPogreska>Morate biti prijavljeni u sustav kako bi se mogli odjaviti. Za prijavu kliknite na poveznicu - NOVA PRIJAVA!</p>';
                            echo '<a id="registriraj_me" href="prijava.php">NOVA PRIJAVA</a>';
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
                        
                    ?>
                </p>
            </form>
        </section>
        
        
    </body>
</html>
