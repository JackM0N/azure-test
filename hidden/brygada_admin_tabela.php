<?php
    session_name("PSIN");
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styl_admin.css" type="text/css">
    <title>Firma budowlana</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie, firma, budowlana, remontowo-budowlana">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
    <script src="skrypt.js"></script>
</head>
<body>
    <header>
        <div class="logo">
            FRB
        </div>
        <div class="contact">
            <h3>Jesteś zalogowany jako:</h3> 
            <h4>
                <?php
                    if (isset($_SESSION["zalogowany"]) && ($_SESSION["zalogowany"]) == false || (!isset($_SESSION["zalogowany"])) || (!isset($_SESSION["uzytkownik"]))) {
                        print("ARGH!");
                    } else if (($_SESSION["zalogowany"]) == true && (isset($_SESSION["uzytkownik"]))) {
                        print($_SESSION["Imie"]." ".$_SESSION["Nazwisko"]." (".$_SESSION["uzytkownik"].")");
                    }
                ?>
            </h4>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="zlecenie_admin_tabela.php">ZLECENIA</a></li>
            <li><a href="pracownik_admin_tabela.php">PRACOWNICY</a></li>
            <li><a href="brygada_admin_tabela.php">BRYGADY</a></li>
            <li><a href="logowanie_admin_koniec.php">WYLOGUJ</a></li>
        </ul>
    </nav>
    <div class="main">
<?php
if (isset($_SESSION["zalogowany"]) && ($_SESSION["zalogowany"]) == false || (!isset($_SESSION["zalogowany"])) || (!isset($_SESSION["uzytkownik"]))) {
    print("<h2>Odmowa dostępu</h2>");

    $_SESSION["zalogowany"] = false;

    if (isset($_SESSION["uzytkownik"])) {
        unset($_SESSION["uzytkownik"]);
    }
    if (isset($_SESSION["Imie"])) {
        unset($_SESSION["Imie"]);
    }
    if (isset($_SESSION["Nazwisko"])) {
        unset($_SESSION["Nazwisko"]);
    }

    session_destroy();
    print("<p class='msg error'>Ta funkcja jest dostępna tylko dla zalogowoanych użytkowników</p>");
    print("<p><a href='logowanie_admin_formularz.php' class='bck'>Powrót do fromularza logowania</a></p>");
} else if (($_SESSION["zalogowany"]) == true && (isset($_SESSION["uzytkownik"]))) {
    $server = "DESKTOP-BK5MMO6\SQL1A";

    $dane_polaczenia = array("Database" => "P15_P1", "CharacterSet" => "UTF-8");

    //Łączenie z serwerem
    $polaczenie = sqlsrv_connect($server, $dane_polaczenia);

    if($polaczenie == false){
        print("<p class='msg error'> Połączenie z serwerem baz danych $server nie powiodło się.</p>");
        die(print_r(sqlsrv_errors(), true));
    }else{
        print("<h2>Brygady</h2>");
        print("<table>
                    <thead>
                        <tr>
                            <td><a href='brygada_admin_tabela.php?sort=IdBrygada&ifl=Y' class='head'>Identyfikator</a></td>
                            <td><a href='brygada_admin_tabela.php?sort=Brygadzista&ifl=N' class='head'>Brygadzista</a></td>
                            <td><a href='brygada_admin_tabela.php?sort=LiczbaPracownikow&ifl=Y' class='head'>LiczbaPracownikow</a></td>
                            <td><a href='brygada_admin_tabela.php?sort=Specjalizacja&ifl=N' class='head'>Specjalizacja</a></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>");
        
        if(!isset($_GET["sort"])){
            $sort = 'IdBrygada';
            $ifl = 'Y';
        }else{
            $sort = $_GET["sort"];
            $ifl = $_GET["ifl"];
        }

        $komenda_sql = "EXECUTE dbo.Brygada_Wyswietl_Sort '$sort', '$ifl';";

        $zbior_wierszy = sqlsrv_query($polaczenie, $komenda_sql);        
        
        if(sqlsrv_has_rows($zbior_wierszy) == false){
            print("<tr><td colspan = '4'>Brak danych brygad w bazie</td></tr>");
        }else{
            while($wiersz = sqlsrv_fetch_array($zbior_wierszy, SQLSRV_FETCH_ASSOC)){
                $IdBrygada = $wiersz["IdBrygada"];
                $Brygadzista = $wiersz["Brygadzista"]; 
                $LiczbaPracownikow = $wiersz["LiczbaPracownikow"]; 
                $Specjalizacja = $wiersz["Specjalizacja"]; 

                print("<tr>
                            <td>$IdBrygada</td>
                            <td>$Brygadzista</td>
                            <td>$LiczbaPracownikow</td>
                            <td>$Specjalizacja</td>
                            <td><a href='brygada_admin_edytuj.php?IdBrygada=$IdBrygada' class='btn upd'>Edytuj</a></td>
                            <td><a href='brygada_admin_usun.php?IdBrygada=$IdBrygada' class='btn del'>Usuń</a></td>        
                </tr>");
            }

            if($zbior_wierszy != null){
                sqlsrv_free_stmt($zbior_wierszy);
            }
        }

        print("</tbody>
                </table>");

        print("<button type='button' onclick='zamiana();' id='butn'>+ Nowa brygada</button>");
        
        print("<div id='dodawanieForm'>");
        print("<h2>Nowa brygada</h2>
        <form id='brygadaDodaj' method='get' action='brygada_admin_dodaj.php'>
        <fieldset>
            <legend>Parametry tabeli</legend>
            <p class='lbl'>
                <label for='IdBrygada'>IdBrygada: </label>
                <input id='IdBrygada' type='text' maxlength='10' name='IdBrygada' required pattern='[0-9]*'>
            </p>
            <p class='lbl'>
                <label for='Brygadzista'>Brygadzista: </label>
                <select id='Brygadzista' name='Brygadzista' size='1'>
                    <option value='0'>Wybierz brygadzistę...</option>");

                    $komenda_sql_bryg = "EXECUTE dbo.Pracownik_Wyswietl_Brygadzista;";

                    $zbior_wierszy_bryg = sqlsrv_query($polaczenie, $komenda_sql_bryg);        
                            
                    while($wiersz = sqlsrv_fetch_array($zbior_wierszy_bryg, SQLSRV_FETCH_ASSOC)){
                        $IdPracownik = $wiersz["IdPracownik"];
                        $NazwiskoImie = $wiersz["NazwiskoImie"]; 
                        
                        print("<option value='$IdPracownik'>$NazwiskoImie</option>");
                    }       

        print("
                </select>
                </p>");

                    if($zbior_wierszy_bryg != null)
                        sqlsrv_free_stmt($zbior_wierszy_bryg);
                    
        print("
            <p class='lbl'>
                <label for='LiczbaPracownikow'>Liczba pracowników: </label>
                <input id='LiczbaPracownikow' type='text' maxlength='3' name='LiczbaPracownikow' required pattern='[0-9]*'>
            </p>
            <p class='lbl'>
                <label for='Specjalizacja'>Specjalizacja: </label>
                <input id='Specjalizacja' type='text' maxlength='50' name='Specjalizacja' required pattern='[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ ]+'>
            </p>
            <p>Uwagi (opcjonalne):</p>
            <textarea name='Uwagi' id='Uwagi' maxlength='200'></textarea>
        </fieldset>
        <p><input type='submit' value='Zapisz'><input type='reset' value='Wyczyść pola'></p>
    </form>");
    print("</div>");

    sqlsrv_close($polaczenie);
    }
}
?>
</div>
</body>

</html>