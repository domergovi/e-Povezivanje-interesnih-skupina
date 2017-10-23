<?php
    include ("./baza.class.php");
    session_start();
    if (!isset($_SESSION["aktivniKorisnik"])){
        header("Location: prijava.php");
    }
    else{
        if (isset($_POST["promijeni"])){
        $veza = new Baza();
        $veza->spojiDB();
        
        $ime=$_POST["ime"];
        $prezime=$_POST["prez"];
        $korime=$_POST["korime"];
        $email=$_POST["email"];
        $lozinka=$_POST["lozinka1"];
        $koraci=$_POST["koraci"];
        $id=$_SESSION["aktivniKorisnik"];
        
        $sql="SELECT id_korisnik FROM korisnik WHERE korisnicko_ime='$korime'";
        $rezultat=$veza->selectDB($sql);
        $vraceniKorisnik=$rezultat->fetch_array();
        $korisnikID=$vraceniKorisnik[0];
        
        if ($vraceniKorisnik!="" && $korisnikID!=$id){
            echo"<p id=message>Uneseno korisničko ime se već koristi! Molimo pokušajte ponovno.</p>";
        }
        else{
            echo"<p id=ispravno>Podaci su uspješno promijenjeni!</p>";
            $sql="UPDATE korisnik SET korisnicko_ime='$korime',ime='$ime',prezime='$prezime',email='$email',lozinka='$lozinka',koraci_prijave='$koraci' WHERE id_korisnik='$id'";
            $veza->updateDB($sql);
            
            $_SESSION["ime"]=$ime;
            $_SESSION["prezime"]=$prezime;
            $_SESSION["email"]=$email;
            $_SESSION["lozinka"]=$lozinka;
            $_SESSION["korime"]=$korime;
            $_SESSION["koraci"]=$koraci;
            
        $datumVrijemeOdabira=PomakniVrijeme();
        
        if (isset($_POST['podrucjeInteresa'])){
            $podrucjaInteresa = $_POST['podrucjeInteresa'];
            
            $sql="INSERT INTO `log_bodovi`(`id_korisnik`, `datum_akcije`, `id_akcije`) VALUES ('".$id."','".$datumVrijemeOdabira."',5)";
            $veza->updateDB($sql);

            $sql="UPDATE `korisnik` SET ukupno_steceni_bodovi=ukupno_steceni_bodovi+2 WHERE id_korisnik='$id'";
            $veza->updateDB($sql);
                
            $dodajBodove=intval($_SESSION["steceniBodovi"])+2;
            $_SESSION["steceniBodovi"]=$dodajBodove;
            
            foreach ($podrucjaInteresa as $odabranID){
                $sql="SELECT id_korisnik FROM odabir_podrucje_interesa WHERE id_korisnik='$id' and id_podrucje='$odabranID'";
                $rezultat=$veza->selectDB($sql);
                $vraceniRezultat=$rezultat->fetch_array();
                $dopustenje=$vraceniRezultat[0];
                
                if ($dopustenje!=""){}
                else{
                    $sql="INSERT INTO odabir_podrucje_interesa (id_korisnik,id_podrucje,datum_odabira) VALUES ('".$id."','".$odabranID."','".$datumVrijemeOdabira."')";
                    $veza->updateDB($sql);
                }
            }
            }
        }
                    
        $veza->zatvoriDB();
        }
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
<!DOCTYPE html>
<html>
    <head>
        <title>Profil korisnika</title>
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
        
        <h1 id="glavniNaslovKorisnik">PROFIL KORISNIKA</h1>
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
        
        
        <section id="sekcijaKorisnik">
            <form id="korisnikPodaci" method="post" name="korisnikPodaci" action="profilKorisnika.php" novalidate>
                
                <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    $korime=$_SESSION["korime"];
                    $ime=$_SESSION["ime"];
                    $prezime=$_SESSION["prezime"];
                    $email=$_SESSION["email"];
                    $lozinka=$_SESSION["lozinka"];
                    $koraci=$_SESSION["koraci"];
                    
                    $steceniBodovi=$_SESSION["steceniBodovi"];
                    $potroseniBodovi=$_SESSION["potroseniBodovi"];
                    
                    echo"<p>
                    <label for='ime'>Ime: </label>
                    <input type='text' id='ime' name='ime' value='$ime'><br>
                
                    <label for='prez'>Prezime: </label>
                    <input type='text' id='prez' name='prez' value='$prezime'><br>
                    
                    <label for='korime'>Korisničko ime: </label>
                    <input type='text' id='korime' name='korime' value='$korime'><br>
                    
                    <label for='email'>Email adresa: </label>
                    <input type='email' id='email' name='email' value='$email'><br>
                    
                    <label for='lozinka1'>Lozinka: </label>
                    <input type='password' id='lozinka1' name='lozinka1' value='$lozinka'>
                    
                    <label for='koraci'>Koraci prijave: </label>
                    <input type='text' id='koraci' name='koraci' value='$koraci'><br>
                    
                    <label for='podrucjeInteresa'>Dodaj područje interesa: </label>
                    <select id='podrucjeInteresa' name='podrucjeInteresa[]' size='3' multiple='multiple'>";
                        $sql='SELECT id_podrucje,naziv FROM podrucje_interesa';
                        $rezultat=$veza->selectDB($sql);
                        
                        while (list($id,$nazivP) = $rezultat->fetch_array()) {
                            echo "<option value='$id'>$nazivP</option>";
                        }
                        echo "</select><br><br>
                    
                    <input id='tipka_promijeni' type='submit' value='Promijeni podatke' name='promijeni'></p>";
                    $veza->zatvoriDB();
                ?>
            </form>
        </section>
        <?php
            echo "<p id=skupljeniBodovi>Skupljeni bodovi: $steceniBodovi</p>";
            echo "<p id=potroseniBodovi>Potrošeni bodovi: $potroseniBodovi</p>";
        ?>
    </body>
</html>
