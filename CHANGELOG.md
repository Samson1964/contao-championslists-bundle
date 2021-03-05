# Meisterliste Changelog

## Version 3.0.0 (2021-03-05)

* Add: Kategorien-Tabelle für Platzierungsnamen (Platzierung "meister" ist reserviert und wird automatisch verwaltet)
* Add: Abhängigkeit menatwork/contao-multicolumnwizard-bundle
* Add: Normale Formularfelder mit MCW für Eingabe weiterer Platzierungen
* Change: Spalte clubrating aufgeteilt in verein und rating. Runonce schreibt Wert in verein.
* Add: runonce.php (und runonce_org.php) für einmalige Tabellenaktualisierung
* Change: tl_championslists_items - PagePicker durch DcaPicker ersetzt
* Change: tl_content umgestellt auf customTpl und Einzel- und Mannschaftswettbewerbe getrennt
* Change: Templates umbenannt/überarbeitet - ce_championslists_mono (vorher mod_championslists_single), ce_championslists_mono_mini (vorher mod_championslists_mini), ce_championslists_multi (vorher mod_championslists_team)
* Delete: ContentElements/Championslist.php (ersetzt durch ChampionslistsMono und ChampionslistsMulti)
* Add: ContentElements/ChampionslistsMono und ContentElements/ChampionslistsMulti
* Add: Standard-CSS, was im Template eingebunden wird
* Add: Helper-Klasse

## Version 2.0.1 (2021-01-13)

* Add: Template mod_championslist_mini

## Version 2.0.0 (2020-12-03)

* Fix: tl_championslist_items Funktion checkPalette berücksichtigte keine weiblichen Mannschaftsturniere bei der Palettenmanipulation
* Change: Bildgrößen-Einstellungen in System auf Contao 4 geändert
* Change: Championslist.php Bildverarbeitung angepaßt
* Add: Inhaltselement für Anzeige des aktuellen Meisters
* Fix: tl_championslists_item.published hat gefehlt
* Delete: Alte Templates
* Change: Template mod_championslists_team im neuen Format

## Version 1.1.2 (2020-10-28)

* Fix: Syntaxfehler im Template mod_championslists_single
* Fix Spielerregister-Abfrage: Erfolgt jetzt über Helper-Funktion des Spielerregister-Bundles

## Version 1.1.1 (2020-10-09)

* Change: DIV-Container für Spielerfoto mit Randabstand im Template mod_championslists_single
* Add: Typ der Meisterliste als Filter in der Datensatzauflistung
* Add: Template der Meisterliste als Filter in der Datensatzauflistung

## Version 1.1.0 (2020-10-09)

* Change: Reihenfolge der Einstellungen
* Add: Listentyp in Header Datensatzauflistung hinzugefügt
* Fix: Standardbilder werden nicht geladen, Code hat gefehlt

## Version 1.0.0 (2020-10-07)

* Hauptverzeichnis aufgeräumt
* Add: In den Einstellungen können Standardbilder gesetzt werden (Einzel/Mannschaft m/w)
* Add: Listentypen für weiblich Einzel+Mannschaft
* Add: Template mod_championslists_single für die neuen Einzellisten
* Fix: Inhaltselement Meisterliste - Alternatives Template und Filter funktionierte nicht
* Fix: Attempted to load class "FrontendTemplate" from namespace "Schachbulle\ContaoChampionslistsBundle\ContentElements"

## Version 0.0.4 (2020-04-08)

* Fix: Spielerregister-Funktion durch Direktaufruf der externen Klasse ersetzt
* Add: Anzeige Bild vorhanden/Spielerregister-Verknüpfung vorhanden in BE-Liste
* Fix: Ausgabe der Standardbilder im Template korrigiert
* Fix: BE-Vorschau korrigiert

## Version 0.0.3 (2020-04-07)

* Anpassung des BE-Formulars für Einzelturniere
* Templateausgabe Einzelturniere angepaßt
* Templateausgabe Mannschaftsturniere angepaßt

## Version 0.0.2 (2020-03-11)

* Umwandlung in ein C4-Bundle

## Version 0.0.1 (2020-03-11)

* Übernahme der Version aus Contao 3
