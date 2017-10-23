<?php
    include("baza.class.php");
    header ("Content-Type:text/xml");
    
    /* create a dom document with encoding utf8 */
    $domtree = new DOMDocument('1.0', 'UTF-8');

    /* create the root element of the xml tree */
    $xmlRoot = $domtree->createElement("xml");
    /* append it to the document created */
    $xmlRoot = $domtree->appendChild($xmlRoot);
    
    $veza = new Baza();
    $veza->spojiDB();
    
    $sql="SELECT ime,prezime,korisnicko_ime FROM korisnik";
    $rezultat=$veza->selectDB($sql);
    
    while(list($ime,$prezime,$korime) = $rezultat->fetch_array()) 
        {
            $currentTrack = $domtree->createElement("korisnik");
            $currentTrack = $xmlRoot->appendChild($currentTrack);
            $currentTrack->appendChild($domtree->createElement('ime',$ime));
            $currentTrack->appendChild($domtree->createElement('prezime',$prezime));
            $currentTrack->appendChild($domtree->createElement('korisnicko_ime',$korime));
        }

    /* get the xml printed */
    echo $domtree->saveXML();
    $veza->zatvoriDB();
?>
