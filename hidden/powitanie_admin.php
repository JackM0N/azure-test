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
    <link rel="stylesheet" href="../style/globalna.css" type="text/css">
    <link rel="stylesheet" href="../style/glowna.css" type="text/css">
    <title>Firma budowlana</title>
    <meta name="keywords" content="serwisy, internetowe, programowanie, firma, budowlana, remontowo-budowlana">
    <meta name="description" content="Strona utworzona w ramach listy P1.">
    <meta name="author" content="Przemysław Gotowała">
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
                    print($_SESSION["Imie"]." ".$_SESSION["Nazwisko"]." (".$_SESSION["uzytkownik"].")")
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
    <div id="welcome">
        <h1>WITAJ W PANELU ADMINISTRATORA FIRMY FRB!</h1>
    </div>
</body>
</html>