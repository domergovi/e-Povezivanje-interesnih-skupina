<!DOCTYPE html>
<html>
    <head>
        <title>Korisnici</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Početna stranica">
        <meta name="author" content="Domagoj Ergović">
        <meta name="keywords" content="skupine,komentar,webdip">
        <link rel="stylesheet" type="text/css" href="../css/domergovi.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css"/>
        <title></title>
    </head>
    <body>
        <header>
            <figure id="slikaZaglavlje">
                <img src="../slike/homeButton.png" usemap="#mapaZaglavlje" alt="SlikaZaglavlja" width="200">
                <map name="mapaZaglavlje">
                    <area href="../pocetna.php" shape="circle" alt="krug" coords="99,72,67" target="_blank"/>
                </map>
                <figcaption hidden="hidden">Slika zaglavlja</figcaption>
            </figure>
        </header>
        
        <h1 id="glavniNaslovKorisnici">KORISNICI</h1>
        <h2 id="sporedniNaslov">E - POVEZIVANJE INTERESNIH SKUPINA</h2>
        
        <nav>
            <ul class="meni">
                <li>
                    <a href="../pocetna.php">POČETNA</a>
                </li>
                <li>
                    <a href="../registracija.php">REGISTRACIJA</a>
                </li>
                <li>
                    <a href="../prijava.php">PRIJAVA</a>
                </li>
                <?php
                session_start();
                    if (isset($_SESSION["aktivniKorisnik"])){
                        echo '<li><a href="../profilKorisnika.php">PROFIL</a></li>';
                        echo '<li><a href="../pregledDiskusija.php">DISKUSIJE</a></li>';
                        echo '<li><a href="../pregledKupona.php">KUPONI</a></li>';
                        echo '<li><a href="../kosarica.php">KOŠARICA</a></li>';
                    }
                ?>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        if (intval($_SESSION["tipKorisnik"])==2 || intval($_SESSION["tipKorisnik"])==1){
                            echo '<li><a style="color: green;" href="../stranicaModerator.php">MODERATOR</a></li>';
                        }
                    }
                ?>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        if (intval($_SESSION["tipKorisnik"])==1){
                            echo '<li><a style="color: blue;" href="../pomakVremena.php">POMAK VREMENA</a></li>';
                            echo '<li><a style="color: blue;" href="../stranicaAdministrator.php">ADMIN</a></li>';
                            echo '<li><a style="color: blue;" href="../privatno/korisnici.php">KORISNICI</a></li>';
                            echo '<li><a style="color: blue;" href="../dnevnik.php">DNEVNIK</a></li>';
                            echo '<li><a style="color: blue;" href="../korisnikBodovi.php">BODOVI KORISNIKA</a></li>';
                        }
                    }
                ?>
                <?php
                    if (isset($_SESSION["aktivniKorisnik"])){
                        echo '<li><a style="color: red;" href="../odjava.php">ODJAVA</a></li>';
                    }
                ?>
            </ul>
        </nav>
        
        <section>
            <table id='tablicaKorisnici' style="background: rgba(0, 0, 0, 0.9);"><thead>
                    <tr>
                        <th>KORISNIČKO IME</th>
                        <th>PREZIME</th>
                        <th>IME</th>
                        <th>EMAIL</th>
                        <th>LOZINKA</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                include ("../baza.class.php");
                $veza = new Baza();
                $veza->spojiDB();
                    
                    $sql="SELECT korisnicko_ime,prezime,ime,email,lozinka FROM korisnik"; 
                    $rezultat=$veza->selectDB($sql);

                    while (list($korime,$prezime,$ime, $email,$lozinka) = $rezultat->fetch_array()) {
                        print "<tr><td>$korime</td><td>$prezime</td><td>$ime</td><td>$email</td><td>$lozinka</td></tr>\n";
                    }
                
                $veza->zatvoriDB();
                ?>
                </tbody>
            </table>
        </section>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="../js/domergovi_jquery.js"></script> 
    </body>
</html>

