function zamiana(){
    document.getElementById('dodawanieForm').style.visibility = 'visible';
    document.getElementById('butn').style.visibility = 'hidden';
    document.getElementById('butn2').style.visibility = 'hidden';
    document.getElementById('butn').style.display = 'none';
}

function szukaj(){
    var szukana = document.getElementById('wyszukiwarka').value;
    window.location.href = "pracownik_tabela.php?szukaj="+szukana;
}