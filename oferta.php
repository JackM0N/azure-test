<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="firma, budowlana, remontowo-budowlana">
    <link rel="stylesheet" href="style/globalna.css" type="text/css">
    <link rel="stylesheet" href="style/oferta.css" type="text/css">
    <title>Firma budowlana</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
</head>
<body>
<header>
        <div class="logo">
            FRB
        </div>
        <div class="contact">
            <h3>Telefon kontaktowy</h3> 
            <h4>+48 268 425 138</h4>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="stronaglowna.html">STRONA GŁÓWNA</a></li>
            <li><a href="onas.html">O NAS</a></li>
            <li><a href="oferta.php">OFERTA</a></li>
            <li><a href="kontakt.html">KONTAKT</a></li>
            <li id="login">
                <a href="oferta.php" id="log">LOGOWANIE</a>
                <div id="dropdown-content">
                    <a href="hidden/logowanie_uzytk_formularz.php">KLIENT</a>
                    <a href="hidden/logowanie_admin_formularz.php">ADMIN</a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="pic">
        <img src="img/oferta-przyklad.jpg" alt="">
    </div>
    <div id="about">
        <div id="abouttext">
            <h1>Nasza oferta</h1>
            <p>
                FRB to renomowana firma budowlano-remontowa, która od lat zapewnia wysokiej jakości usługi na rynku. 
                Zespół FRB składa się z doświadczonych i wykwalifikowanych fachowców, którzy są ekspertami w dziedzinie 
                budownictwa i remontów. Firma specjalizuje się w różnorodnych projektach, obejmujących zarówno budowę nowych 
                domów, jak i modernizację istniejących budynków, dlatego jeśli poszukujesz usługi z zakresu wykonawstwa,
                wykończeń lub remontów na pewno będziemy w stanie Ci pomóc! Jeśli jednak masz wątpliwości możesz przejrzeć
                naszą pełną listę oferowanych usług w tabeli poniżej.
            </p>
            <p><strong>UWAGA: Cena jednostkowa nie jest zawsze ceną ostateczną! Opisane niżej kwoty mogą 
                ulec zmianie w zależności od projektu i potencjalnych rabatów!</strong></p>
        </div>    
    </div>
    <div id="offer">
        <div id="offercontent">
        <h1>Oferowane usługi</h1>
    <?php
        require_once("polaczenie_bd.php");

        if (isset($polaczenie) && $polaczenie != null) {
            try{
                print("<table>
                        <thead>
                            <tr>
                                <td>Nazwa usługi</td>
                                <td>Cena jednostkowa</td>
                                <td>Jednostka miary</td>
                                <td>Opis</td>
                            </tr>
                        </thead>
                        <tbody>");

                $sql = "EXECUTE dbo.Usluga_Wyswietl";
                $komenda_sql = $polaczenie->prepare($sql);
                $komenda_sql->execute();
                $zbior_wierszy = $komenda_sql->fetchAll(PDO::FETCH_ASSOC);
                if($komenda_sql->rowCount() == 0){
                    print("<tr><td colspan='4'>UPS! Coś poszło nie tak! Nie można wczytać danych usług...</td></tr>");
                }else{
                    foreach($zbior_wierszy as $wiersz){
                        $NazwaUslugi = $wiersz["UslugaNazwa"];
                        $CenaJednostkowa = number_format($wiersz["CenaJednostkowa"],2);
                        $JednostkaMiary = $wiersz["JednostkaMiary"];
                        $Opis = $wiersz["Opis"];
                    
                        print("<tr>
                                <td>$NazwaUslugi</td>
                                <td>$CenaJednostkowa</td>
                                <td>$JednostkaMiary</td>
                                <td>$Opis</td>
                              </tr>");
                    }
                }
                print("</tbody>
                    </table>");     
            }catch(Exception $e){
                print("<p class='msg error'> Podczas przetwarzania danych wystąpił błąd<br> Szczegóły:".$e->getMessage()."</p>");
            }finally{
                $polaczenie = null;
            }
        }
    ?>
    </div>
    </div>
    <footer>&copy; Copyright by Przemysław Gotowała 31645</footer>
</body>
</html>