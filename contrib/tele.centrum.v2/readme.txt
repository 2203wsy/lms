W konfiguracji utworzy� nowy interfejs u�ytkownika 'callcenter'.

Uzupe�ni� o opcj�:
- callcenterip - poda� adres IP z kt�rego ��czy� si� b�d� agenci callcenter,
- networks - adresacja sieci, kt�ra mo�e wy�wietla� formularz callcenter np. 10.10.10.0/24 (mo�na poda� kilka sieci oddzielonych przecinkiem), 
- queues - id kolejek w LMS, odzielone przecinkami. 

Kolejno Zg�oszenie awarii, informacja handlowa oraz sprawy finansowe,
- categories - id kategorii zg�osze� w LMS, odzielone przecinkami. 

Kolejno Internet, telewizja, telefon oraz og�lna, 
(UWAGA! ZACHOWANIE KOLEJNO�CI JEST WYMAGANE DO POPRAWNEGO DZIA�ANIA.)
- queueuser - id u�ytkownika do kt�rego ma by� przypisane zg�oszenie (mo�e by� 0),
- warning - tre�� wiadomo�ci specjalnej wy�wietlanej na g�rze strony,
- information - mo�liwo�� dodanie dodatkowych informacji do wysuwaj�cego si� panelu (np. tabela z godzinami pracy).


Skrypt kt�ry piszuje nagrania do poprawnych zg�osze� znajduj� si� w folderze bin. 
Skrypt wymaga rozszerzenia imap dla PHP.


Nale�y ustawi� go cyklicznie w cron. 

Wymagane do pobierania nagra� z rozm�w:
- hostname - nazwa hosta poczty,
- user - nazwa u�ytkownika poczty,
- pass - has�o u�ytkownika,
- mailfrom - nazwa maila callcenter.



Dodatkowo, nale�y upewni� si� czy w sekcji 'rt' utworzona jest opcja 'mail_dir' z lokalizacj� folderu. 
B�dzie ona wykorzystywana do zapisywanie nagra� z rozm�w.

Numer telefonu pobierany jest na podstawie URL. System callcenter automatycznie dodaje do URL takie dane jak: ID konsultanta, nr tel. dzwoni�cego, nr sprawy, kt�re potem wykorzystywane s� w skrypcie.
