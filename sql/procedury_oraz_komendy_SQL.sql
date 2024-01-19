--------------------------------------------------------------------------------------------------------------------------------------
--Definicja tabeli przechowuj¹cej dane kont u¿ykowników
--------------------------------------------------------------------------------------------------------------------------------------

--Zwykli u¿ytkownicy
CREATE TABLE dbo.Uzytkownik
(
	IdUzytkownik int IDENTITY(1,1) NOT NULL PRIMARY KEY,
	IdKlient int NOT NULL,
	Konto varchar(30) NOT NULL,
	Haslo varchar(100) NOT NULL,
	DataZarejestrowania datetime NOT NULL
);
GO

ALTER TABLE dbo.Uzytkownik
ADD CONSTRAINT FK_Uzytkownik_Klient
FOREIGN KEY (IdKlient)
REFERENCES dbo.Klient(IdKlient);
GO

ALTER TABLE dbo.Uzytkownik
ADD CONSTRAINT UN_Uzytkownik_Konto
UNIQUE (Konto);
GO

SELECT * FROM dbo.Zlecenie;
SELECT * FROM dbo.Klient;
SELECT * FROM dbo.Uzytkownik;

-- Wstawienie przyk³adowych danych

INSERT dbo.Uzytkownik
(IdKlient, Konto, Haslo, DataZarejestrowania)
VALUES
(3, 'ajankowski', '$2y$10$JgdFUXwfI0J2Hbgvab3mgeYKRUVhDelMOundXw7CN/lxTQee/rJRK', GETDATE()),
(1, 'jwojcik', '$2y$10$h7YEr3PG19riaitcigBenumGTGpfKPy5aDSHHtK2rq.tFP0w7JJGm', GETDATE());
GO


--Administratorzy
CREATE TABLE dbo.Administrator
(
	IdAdministrator int IDENTITY(1,1) NOT NULL PRIMARY KEY,
	Imie varchar(30) NOT NULL,
	Nazwisko varchar(40) NOT NULL,
	Konto varchar(30) NOT NULL,
	Haslo varchar(100) NOT NULL,
	DataZarejestrowania datetime NOT NULL
);
GO

ALTER TABLE dbo.Administrator
ADD CONSTRAINT UN_Administrator_Konto
UNIQUE (Konto);
GO

INSERT dbo.Administrator
(Imie, Nazwisko, Konto, Haslo, DataZarejestrowania)
VALUES
('Adam', 'Nowak', 'admin', '$2y$10$1bZwXCZRgaWMo03m69UgFunQeJSgXqvUzZcJiym0/.ywBWd2lNxru', GETDATE());
GO

--------------------------------------------------------------------------------------------------------------------------------------
--Procedury
--------------------------------------------------------------------------------------------------------------------------------------


CREATE OR ALTER PROCEDURE dbo.Usluga_Wyswietl
AS
BEGIN
SELECT UslugaNazwa, CenaJednostkowa, JednostkaMiary, Opis
FROM dbo.Usluga;
END;
GO

EXECUTE dbo.Usluga_Wyswietl;
GO

CREATE OR ALTER PROCEDURE dbo.Uzytkownik_Wyswietl
@Konto varchar(30)
AS
BEGIN
SELECT dbo.Uzytkownik.IdKlient AS IdKlient, Imie, Nazwisko, Konto, Haslo
FROM dbo.Uzytkownik
	INNER JOIN dbo.Klient ON dbo.Klient.IdKlient = dbo.Uzytkownik.IdKlient
WHERE Konto = @Konto;
END;
GO

EXECUTE dbo.Uzytkownik_Wyswietl 'ajankowski';
GO

CREATE OR ALTER PROCEDURE dbo.Zlecenie_Dane_Uzytk
@IdKlient int
AS
BEGIN
SELECT IdZlecenie, dbo.Zlecenie.IdBrygada AS IdBrygada, dbo.Zlecenie.IdStatusZlecenia, StatusZleceniaNazwa, IdKlienta, Nazwisko+' '+Imie AS [DaneKlienta], 
		dbo.Zlecenie.IdObiekt AS IdObiekt, dbo.Obiekt.Miejscowosc+' '+dbo.Obiekt.Ulica+' '+dbo.Obiekt.NumerDomu AS [Obiekt], dbo.Zlecenie.IdBrygada AS IdBrygada, Specjalizacja,
		DataRozpoczecia, TerminRealizacji, DataZakonczenia, Cena, dbo.Zlecenie.Uwagi
FROM dbo.Zlecenie
	INNER JOIN dbo.StatusZlecenia ON dbo.StatusZlecenia.IdStatusZlecenia = dbo.Zlecenie.IdStatusZlecenia
	INNER JOIN dbo.Klient ON dbo.Klient.IdKlient = dbo.Zlecenie.IdKlienta
	INNER JOIN dbo.Obiekt ON dbo.Obiekt.IdObiekt = dbo.Zlecenie.IdObiekt
	INNER JOIN dbo.Brygada ON dbo.Brygada.IdBrygada = dbo.Zlecenie.IdBrygada
WHERE dbo.Zlecenie.IdKlienta = @IdKlient
END;
GO

EXECUTE dbo.Zlecenie_Dane_Uzytk 3;
GO

CREATE OR ALTER PROCEDURE dbo.IdZlecenie_Wybierz
AS
BEGIN
SELECT COUNT(*) as Ilosc
FROM dbo.Zlecenie;
END;

EXECUTE dbo.IdZlecenie_Wybierz;
GO

CREATE OR ALTER PROCEDURE dbo.Status_Wyswietl
AS
BEGIN
SELECT IdStatusZlecenia ,StatusZleceniaNazwa
FROM dbo.StatusZlecenia;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Obiekt_Wyswietl
AS
BEGIN
SELECT IdObiekt, Miejscowosc+' '+Ulica+' '+NumerDomu AS DaneObiekt 
FROM dbo.Obiekt;
END;

EXECUTE dbo.Obiekt_Wyswietl;
GO

CREATE OR ALTER PROCEDURE dbo.Brygada_Przydziel_Wyswietl
AS
BEGIN
SELECT IdBrygada, Specjalizacja
FROM dbo.Brygada
WHERE IdBrygada = 999;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Zlecenie_Wstaw
@Par_IdZlecenie int, 
@Par_IdBrygada int, 
@Par_IdStatusZlecenia int, 
@Par_IdKlienta int, 
@Par_IdObiekt int, 
@Par_DataRozpoczecia date, 
@Par_TerminRealizacji date, 
@Par_DataZakonczenia date, 
@Par_Cena money, 
@Par_Uwagi varchar(1000)
AS
BEGIN
INSERT dbo.Zlecenie
(IdZlecenie, IdBrygada, IdStatusZlecenia, IdKlienta, IdObiekt, DataRozpoczecia, TerminRealizacji, DataZakonczenia, Cena, Uwagi)
VALUES
(@Par_IdZlecenie, @Par_IdBrygada, @Par_IdStatusZlecenia, @Par_IdKlienta, @Par_IdObiekt, @Par_DataRozpoczecia, @Par_TerminRealizacji, @Par_DataZakonczenia, @Par_Cena, @Par_Uwagi)
END;
GO

CREATE OR ALTER PROCEDURE dbo.Zlecenie_Dane_Edycja
@IdZlecenie int
AS
BEGIN
SELECT IdZlecenie, dbo.Zlecenie.IdBrygada AS IdBrygada, dbo.Zlecenie.IdStatusZlecenia, StatusZleceniaNazwa, IdKlienta, Nazwisko+' '+Imie AS [DaneKlienta], 
		dbo.Zlecenie.IdObiekt AS IdObiekt, dbo.Obiekt.Miejscowosc+' '+dbo.Obiekt.Ulica+' '+dbo.Obiekt.NumerDomu AS [Obiekt], dbo.Zlecenie.IdBrygada AS IdBrygada, Specjalizacja,
		DataRozpoczecia, TerminRealizacji, DataZakonczenia, Cena, dbo.Zlecenie.Uwagi
FROM dbo.Zlecenie
	INNER JOIN dbo.StatusZlecenia ON dbo.StatusZlecenia.IdStatusZlecenia = dbo.Zlecenie.IdStatusZlecenia
	INNER JOIN dbo.Klient ON dbo.Klient.IdKlient = dbo.Zlecenie.IdKlienta
	INNER JOIN dbo.Obiekt ON dbo.Obiekt.IdObiekt = dbo.Zlecenie.IdObiekt
	INNER JOIN dbo.Brygada ON dbo.Brygada.IdBrygada = dbo.Zlecenie.IdBrygada
WHERE IdZlecenie = @IdZlecenie;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Status_Bez
@IdStatus int
AS
BEGIN
SELECT IdStatusZlecenia, StatusZleceniaNazwa
FROM dbo.StatusZlecenia
WHERE IdStatusZlecenia NOT LIKE @IdStatus
END;
GO

EXECUTE dbo.Status_Bez 0;
GO

CREATE OR ALTER PROCEDURE dbo.Klient_Wyswietl_Bez
@IdKlient int
AS
BEGIN 
SELECT IdKlient, Imie+' '+Nazwisko AS DaneKlienta 
FROM dbo.Klient
WHERE IdKlient NOT LIKE @IdKlient
END;
GO

CREATE OR ALTER PROCEDURE dbo.Obiekt_Wyswietl_Bez
@IdObiekt int
AS
BEGIN
SELECT IdObiekt, dbo.Obiekt.Miejscowosc+' '+dbo.Obiekt.Ulica+' '+dbo.Obiekt.NumerDomu AS [Obiekt]
FROM dbo.Obiekt
WHERE IdObiekt NOT LIKE @IdObiekt
END;
GO

CREATE OR ALTER PROCEDURE dbo.Zlecenie_Szczegoly
@IdZlecenie int
AS
BEGIN
SELECT IdZlecenie,
StatusZleceniaNazwa, 
dbo.Zlecenie.IdBrygada AS IdBrygady, Specjalizacja,
Nazwisko+' '+Imie AS [ImieNazwiskoKlienta], 
NazwaFirmy,dbo.Klient.Miejscowosc+' '+dbo.Klient.Ulica+' '+dbo.Klient.NumerDomu AS [DaneAdresoweKlienta], 
'Tel:'+dbo.Klient.NumerTelefonu+' Mail:'+dbo.Klient.AdresEmail AS [DaneKontaktoweKlienta], 
dbo.Obiekt.Miejscowosc+' '+dbo.Obiekt.Ulica+' '+dbo.Obiekt.NumerDomu AS [Obiekt],
dbo.Obiekt.NumerLokalu AS NumerLokalu, 
DataRozpoczecia,TerminRealizacji, DataZakonczenia, Cena,
dbo.Zlecenie.Uwagi AS Uwagi
FROM dbo.Zlecenie
	INNER JOIN dbo.StatusZlecenia ON dbo.StatusZlecenia.IdStatusZlecenia = dbo.Zlecenie.IdStatusZlecenia
	INNER JOIN dbo.Klient ON dbo.Klient.IdKlient = dbo.Zlecenie.IdKlienta
	INNER JOIN dbo.Obiekt ON dbo.Obiekt.IdObiekt = dbo.Zlecenie.IdObiekt
	INNER JOIN dbo.Brygada ON dbo.Brygada.IdBrygada = dbo.Zlecenie.IdBrygada
WHERE IdZlecenie = @IdZlecenie;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Usluga_Szczegoly
@Id int
AS
BEGIN
SELECT UslugaNazwa, Ilosc, Cena
FROM dbo.ZlecenieUsluga
	INNER JOIN dbo.Usluga ON dbo.Usluga.IdUsluga = dbo.ZlecenieUsluga.IdUsluga
WHERE IdZlecenie = @Id;
END;
GO


CREATE OR ALTER PROCEDURE dbo.Material_Szczegoly
@Id int
AS
BEGIN
SELECT Nazwa, CenaZaplacona, WykorzystanieMaterialu
FROM dbo.ZlecenieMaterial
	INNER JOIN dbo.Material ON dbo.Material.IdMaterial = dbo.ZlecenieMaterial.IdMaterial
WHERE IdZlecenie = @Id;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Administrator_Wyswietl
@Konto varchar(30)
AS
BEGIN
SELECT Imie,Nazwisko, Konto, Haslo
FROM dbo.Administrator
WHERE Konto = @Konto;
END;
GO

EXECUTE dbo.Administrator_Wyswietl 'admin';
GO

CREATE OR ALTER PROCEDURE dbo.Zlecenie_Dane_Wszystkie
AS
BEGIN
SELECT IdZlecenie, dbo.Zlecenie.IdBrygada AS IdBrygada, dbo.Zlecenie.IdStatusZlecenia, StatusZleceniaNazwa, IdKlienta, Nazwisko+' '+Imie AS [DaneKlienta], 
		dbo.Zlecenie.IdObiekt AS IdObiekt, dbo.Obiekt.Miejscowosc+' '+dbo.Obiekt.Ulica+' '+dbo.Obiekt.NumerDomu AS [Obiekt], dbo.Zlecenie.IdBrygada AS IdBrygada, Specjalizacja,
		DataRozpoczecia, TerminRealizacji, DataZakonczenia, Cena, dbo.Zlecenie.Uwagi
FROM dbo.Zlecenie
	INNER JOIN dbo.StatusZlecenia ON dbo.StatusZlecenia.IdStatusZlecenia = dbo.Zlecenie.IdStatusZlecenia
	INNER JOIN dbo.Klient ON dbo.Klient.IdKlient = dbo.Zlecenie.IdKlienta
	INNER JOIN dbo.Obiekt ON dbo.Obiekt.IdObiekt = dbo.Zlecenie.IdObiekt
	INNER JOIN dbo.Brygada ON dbo.Brygada.IdBrygada = dbo.Zlecenie.IdBrygada
END;
GO

CREATE OR ALTER PROCEDURE dbo.Klient_Wyswietl
AS
BEGIN
SELECT IdKlient ,Imie+' '+Nazwisko AS DaneKlienta
FROM dbo.Klient;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Brygada_Wyswietl
AS
BEGIN
SELECT IdBrygada,Specjalizacja
FROM dbo.Brygada
END;
GO

CREATE OR ALTER PROCEDURE dbo.Brygada_Wyswietl_Bez
@IdBrygada int
AS
BEGIN
SELECT IdBrygada, Specjalizacja
FROM dbo.Brygada
WHERE IdBrygada NOT LIKE @IdBrygada
END;
GO

CREATE OR ALTER PROCEDURE dbo.Zlecenie_Usun
@IdZlecenie int
AS
BEGIN
DELETE dbo.Zlecenie
WHERE IdZlecenie = @IdZlecenie;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Brygada_Wyswietl_Sort
@Sort varchar(30),
@CzyNumer varchar(1)
AS
BEGIN
IF (@CzyNumer LIKE 'Y')
	SELECT IdBrygada, Nazwisko + ' ' + Imie AS Brygadzista, LiczbaPracownikow, dbo.Brygada.Specjalizacja
	FROM dbo.Brygada INNER JOIN dbo.Pracownik ON dbo.Brygada.IdBrygadzista = dbo.Pracownik.IdPracownik
	ORDER BY 
		CASE @Sort
			WHEN 'IdBrygada' THEN IdBrygada
			WHEN 'LiczbaPracownikow' THEN LiczbaPracownikow
		END
	ASC;
ELSE
	SELECT IdBrygada, Nazwisko + ' ' + Imie AS Brygadzista, LiczbaPracownikow, dbo.Brygada.Specjalizacja
	FROM dbo.Brygada INNER JOIN dbo.Pracownik ON dbo.Brygada.IdBrygadzista = dbo.Pracownik.IdPracownik
	ORDER BY 
		CASE @Sort
			WHEN 'Brygadzista' THEN Nazwisko
			WHEN 'Specjalizacja' THEN dbo.Brygada.Specjalizacja
		END
	ASC;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Pracownik_Wyswietl_Brygadzista
AS
BEGIN
SELECT IdPracownik, Nazwisko +' '+ Imie AS NazwiskoImie
FROM dbo.Pracownik
WHERE Specjalizacja LIKE 'Brygadzista'
ORDER BY Nazwisko ASC;
END;
GO


CREATE OR ALTER PROCEDURE dbo.Brygada_Wyswietl_IdBrygada
@IdBrygada int
AS
BEGIN
SELECT IdBrygada, IdBrygadzista,Nazwisko + ' ' +Imie AS  BrygadzistaNazwiskoImie, LiczbaPracownikow, dbo.Brygada.Specjalizacja, dbo.Brygada.Uwagi
FROM dbo.Brygada INNER JOIN dbo.Pracownik ON dbo.Brygada.IdBrygadzista = dbo.Pracownik.IdPracownik
WHERE IdBrygada = @IdBrygada;
END;
GO

CREATE OR ALTER PROCEDURE dbo.Pracownik_Wyswietl_Brygadzista_BezId
@IdPracownik int
AS
BEGIN
SELECT IdPracownik, Nazwisko +' '+ Imie AS NazwiskoImie
FROM dbo.Pracownik
WHERE ((Specjalizacja LIKE 'Brygadzista') AND (IdPracownik NOT LIKE @IdPracownik))
ORDER BY Nazwisko ASC;
END;
GO