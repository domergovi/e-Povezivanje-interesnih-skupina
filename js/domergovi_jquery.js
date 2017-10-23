//datatables
$(document).ready(function(tablica)
{ 
    $("table[id^='tablica']").dataTable(
    {
        "lengthMenu": [ 5, 10, 25, 50, 75, 100 ],
        "aaSorting": [[0, "asc"], [1, "asc"], [2, "asc"]],
        "bPaginate": true,
        "bLengthChange": true,
        "bFilter": true,
        "bSort": true,
        "bInfo": true,
        "bAutoWidth": true
    });
});

//provjera jel već postoji korisničko ime u bazi
$(document).ready(function ()
{
    $("#korime").focusout(function()
    {
        var ime = $("#ime").val();
        var prezime = $("#prez").val();
        var korisIme=$("#korime").val();
        
        
        $.ajax({
            url: './dohvatiPodatke.php?ime=' + ime + '&prezime=' + prezime,
            type: 'GET',
            dataType: 'xml',

            success: function (xml) {
                console.log(xml);
                $(xml).find('korisnicko_ime').each(function ()
                {
                    if($(this).text()===korisIme){
                        event.preventDefault();                       
                        alert("Upozorenje: Korisničko ime se već koristi!");
                        document.getElementById("korime").classList.add("greska");
                    }
                    else
                    {
                        document.getElementById("korime").classList.remove("greska");
                    }
                        
                });
            }
        });
    });
});

//blokirana tipka za slanje dok se ne odradi RECAPTCHA
function recaptchaCallback() {
    $('#tipka_registracija').removeAttr('disabled');
};

//blokiraj unos kor. imena dok se ne unese ime i prezime
$(document).ready(function(){
    $("#registracija").keypress(function()
    {
        var $ime=$("#ime").val();
        var $prezime=$("#prez").val();

        if (($ime==="" && $prezime==="")|| $ime==="" || $prezime==="")
            $("#korime").attr('disabled', 'disabled');
        else
            $("#korime").removeAttr('disabled');
    });
});

//blokiraj unos POTVRDE LOZINKE dok nije unesena LOZINKA
$(document).ready(function(){
    $("#registracija").keypress(function()
    {
        var $lozinka=$("#lozinka1").val();

        if ($lozinka==="")
            $("#lozinka2").attr('disabled', 'disabled');
        else
            $("#lozinka2").removeAttr('disabled');
    });
});

//Provjera počinju li IME i PREZIME velikim početnim slovom
$(document).ready(function ()
{
    $("#registracija").submit(function()
    {
        var izraz = new RegExp(/^[A-Z]/);
        var provjeraIme = izraz.test(document.getElementById("ime").value);
        var provjeraPrezime=izraz.test(document.getElementById("prez").value);
        
        if (provjeraIme === false || provjeraPrezime===false || (provjeraIme===false && provjeraPrezime===false)){
            event.preventDefault();
            alert("Upozorenje: Ime i prezime moraju početi velikim početnim slovom!");
            document.getElementById("ime").classList.add("greska");
            document.getElementById("prez").classList.add("greska");
        }
        else
        {
            document.getElementById("ime").classList.remove("greska");
            document.getElementById("prez").classList.remove("greska");
        }
    });
});

//provjera JEDNAKOSTI lozinke i potvrde lozinke
$(document).ready(function(){
    $("#registracija").submit(function()
    {
        var lozinka=$("#lozinka1").val();
        var lozinkaPot=$("#lozinka2").val();

        if (lozinka!==lozinkaPot){
            event.preventDefault();
            alert("Upozorenje: Lozinka i potvrda lozinke nisu jednaki!");
            document.getElementById("lozinka1").classList.add("greska");
            document.getElementById("lozinka2").classList.add("greska");
        }
        else
        {
            document.getElementById("lozinka1").classList.remove("greska");
            document.getElementById("lozinka2").classList.remove("greska");
        }
    });
});

//provjera formata EMAILA nesto@nesto.nesto
$(document).ready(function ()
{
    $("#registracija").submit(function()
    {
        var izraz = new RegExp(/\w+\@\w+\.\w+/);
        var provjeraEmail = izraz.test(document.getElementById("email").value);
        
        if (provjeraEmail === false){
            event.preventDefault();
            alert("Upozorenje: Email mora biti u formatu nesto@nesto.nesto");
            document.getElementById("email").classList.add("greska");
        }
        else
        {
            document.getElementById("email").classList.remove("greska");
        }
    });
});



