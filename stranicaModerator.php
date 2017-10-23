<?php
    include ("./baza.class.php");
    $veza = new Baza();
    $veza->spojiDB();
    
    session_start();
    
    if (!isset($_SESSION["aktivniKorisnik"])){
        header("Location: prijava.php");
    }
    
    $tipKorisnika=intval($_SESSION["tipKorisnik"]);
        if ($tipKorisnika==3){
            header("Location: prijava.php");
        } 
    
    else{
        if (isset($_POST["dodajDiskusiju"])){

        $naziv=$_POST["naziv"];
        $pravila=$_POST["pravila"];
        $aktivacija=PomakniVrijeme();
        $deaktivacija=$_POST["deaktivacija"];

        $id=$_SESSION["aktivniKorisnik"];
        
        if (isset($_POST['podrucjeInteresa'])){
            $podrucjaInteresa = $_POST['podrucjeInteresa'];
            echo"<p id=ispravno>Nova diskusija je uspješno dodana!</p>";
            foreach ($podrucjaInteresa as $podrucjeID){
                    $sql="INSERT INTO diskusija (id_podrucje,naziv,pravila_diskusije,datum_aktivacije,datum_deaktivacije) "
                            . "VALUES ('".$podrucjeID."','".$naziv."','".$pravila."','".$aktivacija."','".$deaktivacija."')";
                    $veza->updateDB($sql);
                }
            
            $sql="SELECT max(id_diskusija) FROM diskusija";
            $rezultat=$veza->selectDB($sql);
            $vracenaDiskusija=$rezultat->fetch_array();
            $diskusijaID=intval($vracenaDiskusija[0]);
            
            $sql="SELECT id_korisnik FROM korisnik "
                    . "WHERE id_korisnik IN "
                    . "(SELECT id_korisnik FROM odabir_podrucje_interesa WHERE id_podrucje IN "
                    . "(SELECT id_podrucje FROM diskusija WHERE id_diskusija='$diskusijaID'))";
            $rezultat=$veza->selectDB($sql);
            
            while (list($korisnik) = $rezultat->fetch_array()) {
                $sql="INSERT INTO `pretplaceni_korisnici`(`id_korisnik`, `id_diskusija`, `datum_pretplate`, zabranjen_pristup) VALUES "
                                        . "(".$korisnik.",".$diskusijaID.",'".$aktivacija."',0)";
                $veza->updateDB($sql);
            }
            
            }
        else{
            echo '<p id=message>Odaberite područje interesa za vašu diskusiju</p>';
        }
        }
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
        <title>Moderator</title>
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
        
        <h1 id="glavniNaslovKosarica">MODERATOR</h1>
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
            <form id="stranicaModerator" method="post" name="stranicaModerator" action="stranicaModerator.php" novalidate>
                <h2 id="podnaslovModerator">NOVA DISKUSIJA</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $id=$_SESSION["aktivniKorisnik"];
                    
                    echo"<p>
                    <label for='naziv'>Naziv diskusije: </label>
                    <input type='text' id='naziv' name='naziv'><br>
                
                    <label for='pravila'>Pravila diskusije: </label>
                    <input type='text' id='pravila' name='pravila'><br>
                    
                    <label for='deaktivacija'>Datum deaktivacije: </label>
                    <input type='text' id='deaktivacija' name='deaktivacija' placeholder='yyyy-mm-dd hh:mm:ss'><br>
                    
                    <label for='podrucjeInteresa'>Područje interesa: </label>
                    <select id='podrucjeInteresa' name='podrucjeInteresa[]' size='1'>";
                        if (intval($_SESSION['tipKorisnik'])==1){
                            $sql="SELECT id_podrucje,naziv FROM podrucje_interesa";
                            $rezultat=$veza->selectDB($sql);
                        }
                        else{
                            $sql="SELECT id_podrucje,naziv FROM podrucje_interesa WHERE moderator='$id'";
                            $rezultat=$veza->selectDB($sql);
                        }
                        
                        
                        while (list($id,$nazivP) = $rezultat->fetch_array()) {
                            echo "<option value='$id'>$nazivP</option>";
                        }
                        echo "</select><br><br>";
                    
                    echo "<input id='tipka_promijeni' type='submit' value='Dodaj diskusiju' name='dodajDiskusiju'></p><br>";
            
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        
        <section id="sekcijaObavijest">
            <form id="stranicaModerator" method="post" name="stranicaModerator" action="stranicaModerator.php" novalidate>
                <h2 id="podnaslovModerator">SLANJE OBAVIJESTI</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $moderator=$_SESSION["aktivniKorisnik"];
                    
                    
                    echo"<div><label for='obavijestKorisnik'>Pošalji obavijest: </label>
                            <select id='obavijestKorisnik' name='obavijestKorisnik[]' size='3' multiple='multiple'>";
                                
                                if (intval($_SESSION['tipKorisnik'])==1){
                                    $sql="SELECT korisnik.id_korisnik,korisnik.korisnicko_ime FROM korisnik";
                                    $rezultat=$veza->selectDB($sql);
                                }
                                else{
                                    $sql="SELECT korisnik.id_korisnik,korisnik.korisnicko_ime 
                                        FROM korisnik
                                        WHERE korisnik.id_korisnik 
                                        IN (SELECT odabir_podrucje_interesa.id_korisnik 
                                        FROM odabir_podrucje_interesa, podrucje_interesa 
                                        WHERE odabir_podrucje_interesa.id_podrucje=podrucje_interesa.id_podrucje 
                                        AND podrucje_interesa.moderator='$moderator') GROUP BY korisnik.korisnicko_ime";
                                    $rezultat=$veza->selectDB($sql);
                                }

                                while (list($id,$korime) = $rezultat->fetch_array()) {
                                    echo "<option value='$id'>$korime</option>";
                                }
                    echo "</select><br>";
                    
                    echo "<label for='tekst'>Tekst obavijesti: </label>"
                    . "<input type='text' id='tekst' name='tekst'><br>"
                            . "<input id='tipka_promijeni' type='submit' value='Pošalji' name='posaljiObavijestKorisnik'></div><br>";
                    
                    
                    if (isset($_POST["posaljiObavijestKorisnik"])){
                        if (isset($_POST["obavijestKorisnik"])){
                        $obavijestKorisnik=$_POST["obavijestKorisnik"];
                        $tekstObavijesti = $_POST['tekst'];
                        $datumVrijeme=  PomakniVrijeme();
                        
                        foreach ($obavijestKorisnik as $odabranID){
                                $sql="SELECT email FROM korisnik WHERE id_korisnik='$odabranID'";
                                $rezultat=$veza->selectDB($sql);
                                $vraceniRezultat=$rezultat->fetch_array();
                                $email=$vraceniRezultat[0];

                                    $mail_to = $email;
                                    $mail_from = "E-povezivanje interesnih skupina";
                                    $mail_subject = "Obavijest moderatora";
                                    $mail_body = "Tekst: ".$tekstObavijesti;
                                    
                                    if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
                                    else {
                                        echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
                                    }
                                    
                                    if (intval($_SESSION['tipKorisnik'])==1){
                                        $sql="SELECT diskusija.id_diskusija FROM diskusija,pretplaceni_korisnici,podrucje_interesa
                                            WHERE diskusija.id_diskusija=pretplaceni_korisnici.id_diskusija 
                                            AND pretplaceni_korisnici.id_korisnik='$odabranID'
                                            AND podrucje_interesa.id_podrucje=diskusija.id_podrucje";
                                        $rezultat=$veza->selectDB($sql);
                                    }
                                    else{
                                        $sql="SELECT diskusija.id_diskusija FROM diskusija,pretplaceni_korisnici,podrucje_interesa
                                            WHERE diskusija.id_diskusija=pretplaceni_korisnici.id_diskusija 
                                            AND pretplaceni_korisnici.id_korisnik='$odabranID'
                                            AND podrucje_interesa.id_podrucje=diskusija.id_podrucje
                                            AND podrucje_interesa.moderator='$moderator'";
                                        $rezultat=$veza->selectDB($sql);
                                    }    
                                while (list($id) = $rezultat->fetch_array()) {
                                    $sql="INSERT INTO obavijest (moderator,id_diskusija,datum_slanja,tekst) "
                                            . "VALUES ('$moderator','$id','$datumVrijeme','$tekstObavijesti')";
                                    $veza->updateDB($sql);
                                }
                            }
                        }
                    }
                    
                    
                            echo"<div><label for='obavijestDiskusija'>Pošalji obavijest: </label>
                            <select id='obavijestKorisnik' name='obavijestDiskusija[]' size='3' multiple='multiple'>";
                                
                                if (intval($_SESSION['tipKorisnik'])==1){
                                        $sql="SELECT id_diskusija,naziv FROM diskusija";
                                        $rezultat=$veza->selectDB($sql);
                                    }
                                else{
                                    $sql="SELECT id_diskusija,naziv FROM diskusija WHERE id_podrucje IN "
                                            . "(SELECT id_podrucje FROM podrucje_interesa WHERE moderator='$moderator' )";
                                    $rezultat=$veza->selectDB($sql);
                                }    
                                while (list($diskusija,$naziv) = $rezultat->fetch_array()) {
                                    echo "<option value='$diskusija'>$naziv</option>";
                                }
                    echo "</select><br>";
                    
                    echo "<label for='tekstDiskusija'>Tekst obavijesti: </label>"
                    . "<input type='text' id='tekstDiskusija' name='tekstDiskusija'><br>"
                            . "<input id='tipka_promijeni' type='submit' value='Pošalji' name='posaljiObavijestDiskusija'></div>";
                    
                    
                    if (isset($_POST["posaljiObavijestDiskusija"])){
                        if (isset($_POST["obavijestDiskusija"])){
                        $obavijestDiskusija=$_POST["obavijestDiskusija"];
                        $tekstObavijestiDiskusije = $_POST['tekstDiskusija'];
                        $datumVrijeme=PomakniVrijeme();
                        
                        foreach ($obavijestDiskusija as $diskusijaID){
                                $sql="SELECT korisnik.email FROM korisnik, podrucje_interesa,diskusija,odabir_podrucje_interesa
                                WHERE korisnik.id_korisnik=odabir_podrucje_interesa.id_korisnik
                                AND odabir_podrucje_interesa.id_podrucje=diskusija.id_podrucje
                                AND diskusija.id_diskusija='$diskusijaID' GROUP BY korisnik.email";
                                $rezultat=$veza->selectDB($sql);
                                
                                while (list($email) = $rezultat->fetch_array()) {
                                    $mail_to = $email;
                                    $mail_from = "E-povezivanje interesnih skupina";
                                    $mail_subject = "Obavijest moderatora";
                                    $mail_body = "Obavijest vezana za diskusiju: ".$tekstObavijestiDiskusije;
                                    
                                    if (mail($mail_to, $mail_subject, $mail_body, $mail_from)) {} 
                                    else {
                                        echo("<p id=message>Problem kod poruke za: '$mail_to'!</p>");
                                    }
                                }
                                
                                      $sql="INSERT INTO `obavijest`(`moderator`, `id_diskusija`, `datum_slanja`, `tekst`) "
                                            . "VALUES ('$moderator','$diskusijaID','$datumVrijeme','$tekstObavijestiDiskusije')";
                                      $veza->updateDB($sql);
                                }
                            }
                        }
                    
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        
        <section id="sekcijaModeriraj">
            <form id="stranicaModerator" method="post" name="stranicaModerator" action="stranicaModerator.php" novalidate>
                <h2 id="podnaslovModerator">DEFINIRANJE KUPONA</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $idModerator=$_SESSION["aktivniKorisnik"];
                    
                    echo"<p>
                    
                    <label for='slobodniKupon'>Slobodni kuponi: </label>
                    <select id='slobodniKupon' name='slobodniKupon' size='3'>";
                        $sql="SELECT id_kupon,naziv FROM kupon_clanstva WHERE datum_aktivacije='0000-00-00 00:00:00'";
                        $rezultat=$veza->selectDB($sql);
                        
                        while (list($idKup,$nazivKup) = $rezultat->fetch_array()) {
                            echo "<option value='$idKup'>$nazivKup</option>";
                        }
                        echo "</select><br><br>
                    
                    <label for='aktivacijaKupon'>Datum aktivacije: </label>
                    <input type='text' id='aktivacijaKupon' name='aktivacijaKupon' placeholder='yyyy-mm-dd hh:mm:ss'><br>
                    
                    <label for='deaktivacijaKupon'>Datum deaktivacije: </label>
                    <input type='text' id='deaktivacijaKupon' name='deaktivacijaKupon' placeholder='yyyy-mm-dd hh:mm:ss'><br>
                    
                    <label for='bodoviKupon'>Iznos bodova: </label>
                    <input type='text' id='bodoviKupon' name='bodoviKupon'><br>
                    
                    <label for='podrucjeInteresa'>Područje interesa: </label>
                    <select id='podrucjeInteresa' name='podrucjeInteresa' size='1'>";
                        if (intval($_SESSION['tipKorisnik'])==1){
                            $sql="SELECT id_podrucje,naziv FROM podrucje_interesa";
                            $rezultat=$veza->selectDB($sql);
                        }
                        else{
                            $sql="SELECT id_podrucje,naziv FROM podrucje_interesa WHERE moderator='$idModerator'";
                            $rezultat=$veza->selectDB($sql);
                        }
                        
                        while (list($id,$nazivP) = $rezultat->fetch_array()) {
                            echo "<option value='$id'>$nazivP</option>";
                        }
                        echo "</select><br><br>
                    
                    <input id='tipka_promijeni' type='submit' value='Definiraj kupon' name='definirajKupon'></p><br>";
                 
                        
                        
                 if (isset($_POST["definirajKupon"])){
                     $slobodniKuponID=$_POST["slobodniKupon"];
                     $datumAktivacije=$_POST["aktivacijaKupon"];
                     $datumDeaktivacije=$_POST["deaktivacijaKupon"];
                     $bodoviKupon=$_POST["bodoviKupon"];
                     $podrucje=$_POST["podrucjeInteresa"];
                        
                     $sql="UPDATE `kupon_clanstva` SET `moderator`='$idModerator', `id_podrucje`='$podrucje',"
                             . "`datum_aktivacije`='$datumAktivacije', `datum_deaktivacije`='$datumDeaktivacije', "
                             . "`potrebni_bodovi`='$bodoviKupon' WHERE id_kupon='$slobodniKuponID'";
                     $veza->updateDB($sql);
                     
                 }
                        
                        
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        <section id="sekcijaModeriraj">
            <form id="stranicaModerator" method="post" name="stranicaModerator" action="stranicaModerator.php" novalidate>
                <h2 id="podnaslovModerator">PROVJERA KODA KUPONA</h2>
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $id=$_SESSION["aktivniKorisnik"];
                    
                    echo"<p>
                    <label for='kod'>Kod kupona: </label>
                    <input type='text' id='kod' name='kod'><br>
                    
                    <input id='tipka_promijeni' type='submit' value='Provjeri kod' name='provjeriKod'></p><br>";
                    
                    if (isset($_POST["provjeriKod"])){
                        $uneseniKod=$_POST["kod"];
                        
                        $sql="SELECT datum_kupovine FROM kupovina WHERE generirani_kod='$uneseniKod'";
                        $rezultat=$veza->selectDB($sql);
                        $vraceniKod=$rezultat->fetch_array();
                        
                        if ($vraceniKod!=null){
                            echo '<p id=ispravno>Uneseni kod kupona postoji u sustavu!</p>';
                        }
                        else{
                            echo '<p id=message>Uneseni kod kupona ne postoji u sustavu!</p>';
                        }
                    }
            
                $veza->zatvoriDB();
                ?>
            </form>
        </section>
        
        
        <?php
            $veza = new Baza();
            $veza->spojiDB();
                        
            $moderator=$_SESSION["aktivniKorisnik"];
            
            
            if (intval($_SESSION['tipKorisnik'])==1){
                $sql="SELECT korisnik.id_korisnik,korisnik.korisnicko_ime 
                    FROM korisnik
                    WHERE korisnik.id_korisnik 
                    IN (SELECT odabir_podrucje_interesa.id_korisnik 
                    FROM odabir_podrucje_interesa, podrucje_interesa 
                    WHERE odabir_podrucje_interesa.id_podrucje=podrucje_interesa.id_podrucje) 
                    GROUP BY korisnik.korisnicko_ime";
                $rezultat=$veza->selectDB($sql);
            }
            
            else{
                $sql="SELECT korisnik.id_korisnik,korisnik.korisnicko_ime 
                    FROM korisnik
                    WHERE korisnik.id_korisnik 
                    IN (SELECT odabir_podrucje_interesa.id_korisnik 
                    FROM odabir_podrucje_interesa, podrucje_interesa 
                    WHERE odabir_podrucje_interesa.id_podrucje=podrucje_interesa.id_podrucje 
                    AND podrucje_interesa.moderator='$moderator') GROUP BY korisnik.korisnicko_ime";
                $rezultat=$veza->selectDB($sql);
            }

                    
            print "<table id=tablicaModerator><tr><td>KORISNIK</td><td>BLOKIRAJ</td><td>ODBLOKIRAJ</td></tr>\n";

            while (list($idKorisnik,$korime) = $rezultat->fetch_array()) {
                print "<tr><td>$korime</td><td><a href='stranicaModerator.php?blokiraj=$idKorisnik'>Blokiraj korisnika</a></td><td><a href='stranicaModerator.php?odblokiraj=$idKorisnik'>Odblokiraj korisnika</a></td></tr>\n";
            }
            print "</table>\n";
            
            if (isset($_GET["blokiraj"])){
                $korisnik=$_GET["blokiraj"];
                $datumVrijeme=  PomakniVrijeme();
                
                $sql="UPDATE pretplaceni_korisnici SET zabranjen_pristup=1 WHERE id_korisnik=$korisnik";
                $veza->updateDB($sql);
                
            }
            
            if (isset($_GET["odblokiraj"])){
                $korisnik=$_GET["odblokiraj"];
                $datumVrijeme=  PomakniVrijeme();
                
                $sql="UPDATE pretplaceni_korisnici SET zabranjen_pristup=0 WHERE id_korisnik=$korisnik";
                $veza->updateDB($sql);
                
            }        
            $veza->zatvoriDB();
        ?>
        
    </body>
</html>

