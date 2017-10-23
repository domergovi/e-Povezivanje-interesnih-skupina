<?php
    include ("./baza.class.php");
    
    session_start();
    if (!isset($_SESSION["odabraniKuponi"])){
        $_SESSION["odabraniKuponi"]=array();
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

function OdabraniPDF(){
    $idKupona=$_GET["pregled"];
    
    $veza = new Baza();
    $veza->spojiDB();
    
    $sql="SELECT pdf_dokument FROM kupon_clanstva WHERE id_kupon='$idKupona'";
    $rezultat=$veza->selectDB($sql);
    $pdf=$rezultat->fetch_array();
    $pdfDokument=$pdf[0];
    
    $veza->zatvoriDB();
    
    return $pdfDokument;
}

if (isset($_GET["kuponID"])&&(isset($_GET["naziv"]))){
    $kupon=$_GET["kuponID"];
    $nazivKupona=$_GET["naziv"];
    $_SESSION['odabraniKuponi'][$kupon] = $nazivKupona;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Kuponi članstva</title>
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
        
        <h1 id="glavniNaslovKorisnik">KUPONI ČLANSTVA</h1>
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
            <form id="pregledKupona" method="post" name="pregledKupona" action="pregledKupona.php" novalidate>
                <h2 id="podnaslovKuponKosarica">DOSTUPNI KUPONI</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    $korisnik=$_SESSION["aktivniKorisnik"];
                    $trenutniDatum=PomakniVrijeme();
                    
                    $sql="SELECT kupon_clanstva.id_kupon,kupon_clanstva.naziv,kupon_clanstva.potrebni_bodovi 
                          FROM kupon_clanstva,odabir_podrucje_interesa
                          WHERE kupon_clanstva.id_podrucje=odabir_podrucje_interesa.id_podrucje 
                          AND odabir_podrucje_interesa.id_korisnik='$korisnik' 
                          AND kupon_clanstva.datum_deaktivacije>='$trenutniDatum'
                          AND kupon_clanstva.id_kupon NOT IN 
                          (SELECT kupon_clanstva.id_kupon
                          FROM kupon_clanstva,kosarica,kupovina
                          WHERE kupon_clanstva.id_kupon=kupovina.id_kupon 
                          AND kosarica.id_kosarica=kupovina.id_kosarica 
                          AND kosarica.id_korisnik='$korisnik')";
                    $rezultat=$veza->selectDB($sql);
                    
                    while (list($kuponID,$naziv,$potrebniBodovi) = $rezultat->fetch_array()) {
                        if (!in_array($naziv, $_SESSION["odabraniKuponi"])){
                            echo "<div id='paragrafKupon' style='float: left;'>";
                            echo "<figure id='slikaKupona'>";
                                echo "<img id='kupon' src='slike/sale.png' alt='slikaKupona' width='200' height='200'>";
                            echo "<figcaption id='figcaptKupon'>Kupon: <br><a href='pregledKupona.php?pregled=$kuponID'>$naziv</a></figcaption><br>";
                            echo "Potrebni bodovi: $potrebniBodovi<br><br>";
                            echo "<a id='dodajUKosaricu' href='pregledKupona.php?kuponID=$kuponID&naziv=$naziv'>Dodaj u košaricu</a>";
                            echo "</figure></div>";
                    }
                    }
                    $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        <section id="sekcijaKupon">
            <form id="pregledKupona" method="post" name="pregledKupona" action="pregledKupona.php" novalidate>
                <h2 id="podnaslovKuponKosarica">ISKORIŠTENI KUPONI</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $sql="SELECT kupon_clanstva.id_kupon, kupon_clanstva.naziv, kupovina.generirani_kod
                          FROM kupon_clanstva,kosarica,kupovina
                          WHERE kupon_clanstva.id_kupon=kupovina.id_kupon 
                          AND kosarica.id_kosarica=kupovina.id_kosarica 
                          AND kosarica.id_korisnik='$korisnik'";
                    $rezultat=$veza->selectDB($sql);
                    
                    while (list($kuponID,$naziv,$kod) = $rezultat->fetch_array()) {
                        echo "<div id='paragrafKupon' style='float: left;'>";
                        echo "<figure id='slikaKupona'>";
                            echo "<img id='kupon' src='slike/sale.png' alt='slikaKupona' width='200' height='200'>";
                        echo "<figcaption id='figcaptKupon'>Kupon: <br><a href='pregledKupona.php?pregled=$kuponID'>$naziv</a></figcaption><br>";
                        echo "Kod: $kod";
                        echo "</figure></div>";
                    }
                    
                    $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        <?php
            if (isset($_GET["pregled"])){
                echo '<p id=pdfIspis><object data="data:application/pdf;base64,';
                    echo base64_encode(OdabraniPDF());
                    echo '"type="application/pdf" style="height:500px;width:80%"></object></p>';
            }
        ?>
        
    </body>
</html>

