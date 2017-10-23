<!DOCTYPE html>
<html>
    <head>
        <title>Dnevnik</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Početna stranica">
        <meta name="author" content="Domagoj Ergović">
        <meta name="keywords" content="skupine,komentar,webdip">
        <link rel="stylesheet" type="text/css" href="css/domergovi.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css"/>
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
        
        
        <h1 id="glavniNaslovKosarica">DNEVNIK</h1>
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
            <table id='tablicaKorisnici' style="background: rgba(0, 0, 0, 0.9);"><thead>
                    <tr>
                        <th>ID KORISNIKA</th>
                        <th>DATUM PRISTUPA</th>
                        <th>SKRIPTA</th>
                        <th>PREGLEDNIK</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                include ("./baza.class.php");
                $veza = new Baza();
                $veza->spojiDB();
                
                    $sql="SELECT id_korisnik,datum_pristupa,skripta,preglednik FROM log_aplikacije"; 
                    $rezultat=$veza->selectDB($sql);

                    while (list($id,$datum,$skripta, $preglednik) = $rezultat->fetch_array()) {
                        print "<tr><td>$id</td><td>$datum</td><td>$skripta</td><td>$preglednik</td></tr>\n";
                    }
                
                $veza->zatvoriDB();
                ?>
                </tbody>
            </table>
        </section><br><br><br>
        
        <section>
            <table id='tablicaKorisnici' style="background: rgba(0, 0, 0, 0.9);"><thead>
                    <tr>
                        <th>ID KORISNIKA</th>
                        <th>DATUM PRISTUPA</th>
                        <th>UPIT</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $veza = new Baza();
                $veza->spojiDB();
                
                    $sql="SELECT id_korisnik,datum_pristupa,upit FROM log_aplikacije_baza"; 
                    $rezultat=$veza->selectDB($sql);

                    while (list($id,$datum,$upit) = $rezultat->fetch_array()) {
                        print "<tr><td>$id</td><td>$datum</td><td>$upit</td></tr>\n";
                    }
                
                $veza->zatvoriDB();
                ?>
                </tbody>
            </table>
        </section>
        
        <section>
            <table id='tablicaKorisnici' style="background: rgba(0, 0, 0, 0.9);"><thead>
                    <tr>
                        <th>ID KORISNIKA</th>
                        <th>DATUM PRISTUPA</th>
                        <th>OPIS RADNJE</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $veza = new Baza();
                $veza->spojiDB();
                
                    $sql="SELECT id_korisnik,datum_pristupa,opis_radnje FROM log_aplikacije_ostalo"; 
                    $rezultat=$veza->selectDB($sql);

                    while (list($id,$datum,$radnja) = $rezultat->fetch_array()) {
                        print "<tr><td>$id</td><td>$datum</td><td>$radnja</td></tr>\n";
                    }
                
                $veza->zatvoriDB();
                ?>
                </tbody>
            </table>
        </section><br><br><br>
        
        <section>
            <table id='tablicaKorisnici' style="background: rgba(0, 0, 0, 0.9);"><thead>
                    <tr>
                        <th>ID KORISNIKA</th>
                        <th>DATUM PRISTUPA</th>
                        <th>DATUM ODJAVE</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $veza = new Baza();
                $veza->spojiDB();
                
                    $sql="SELECT id_korisnik,datum_pristupa,datum_odjave FROM log_aplikacije_sustav"; 
                    $rezultat=$veza->selectDB($sql);


                    while (list($id,$datum,$datum2) = $rezultat->fetch_array()) {
                        print "<tr><td>$id</td><td>$datum</td><td>$datum2</td></tr>\n";
                    }
                
                $veza->zatvoriDB();
                ?>
                </tbody>
            </table>
        </section><br><br><br>
        
        <section>
            <table id='tablicaKorisnici' style="background: rgba(0, 0, 0, 0.9);"><thead>
                    <tr>
                        <th>ID KORISNIKA</th>
                        <th>DATUM AKCIJE</th>
                        <th>NAZIV AKCIJE</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $veza = new Baza();
                $veza->spojiDB();
                
                    $sql="SELECT id_korisnik,datum_akcije,id_akcije FROM log_bodovi"; 
                    $rezultat=$veza->selectDB($sql);


                    while (list($id,$datum,$akcija) = $rezultat->fetch_array()) {
                        
                        print "<tr><td>$id</td><td>$datum</td><td>$akcija</td></tr>\n";
                    }
                
                $veza->zatvoriDB();
                
                ?>
                </tbody>
            </table>
        </section><br><br><br>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/domergovi_jquery.js"></script>
    </body>
</html>



