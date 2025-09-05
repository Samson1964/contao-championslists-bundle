# Meisterliste Changelog

## Version 3.5.1 (2025-09-05)

* Fix: Standardtemplate ce_champion_default kann im Inhaltselement nicht geändert werden -> Template ce_champion_default umbenannt in ce_champion
* Add: Inhaltselement "Aktueller Meister" -> Bildgrößen in tl_content hinzugefügt, damit ein eigenes Bildformat genutzt werden kann

## Version 3.5.0 (2025-09-04)

* Fix: Warning: Undefined array key "typen" in contao/dca/tl_championslists.php (line 156) -> Zugriff auf Sprachvariable mit Und-Zeichen davor versehen
* Fix: Warning: Undefined array key "id" in contao/templates/ce_champion_default.html5 (line 6) -> id wurde nicht übergeben
* Add: Inhaltselement ce_champion (Aktueller Meister) + Inserttag
 
## Version 3.4.3 (2024-04-19)

* Change: Hinweis "Bild vorhanden" im Backend mit Prüfung ergänzt, ob die Datei wirklich vorhanden ist
* Fix: Zuweisung $GLOBALS['championslist-typ'] in checkPalette von tl_championslists_items.php erfolgte an falscher Stelle

## Version 3.4.2 (2024-04-19)

* Fix: Beim Schreiben mit log_message bei print_r true vergessen

## Version 3.4.1 (2024-04-18)

* Fix: Image "" could not be processed: Image type "2020" was not allowed to be processed -> addImageToTemplate bekommt ein leeres Objekt

## Version 3.4.0 (2023-06-18)

* Add: PHP 8 in composer.json als erlaubt eingetragen

## Version 3.3.2 (2023-03-17)

* Add: tl_championslists_items.numberParticipants -> Anzahl der Teilnehmer (plus Anzeige im Backend)

## Version 3.3.1 (2023-03-01)

* Add: Übersetzungen Backend-Module

## Version 3.3.0 (2023-02-28)

* Add: Abhängigkeit codefog/contao-haste
* Add: Kompatibilität mit PHP 8
* Change: tl_championslists -> Toggle-Funktion ausgetauscht gegen Haste-Toggler
* Change: tl_championslists_categories -> Toggle-Funktion ausgetauscht gegen Haste-Toggler
* Change: tl_championslists_items -> Toggle-Funktion ausgetauscht gegen Haste-Toggler
* Change: tl_championslists_items.published -> Standard auf true gesetzt
* Add: ja/nein-Icons für Anzeige der Vollständigkeit der Erfassung
* Add: tl_championslists_items.recording -> Select mit Optionen für Vollständigkeit der Erfassung + Anzeige in Übersicht

## Version 3.2.2 (2022-01-18)

* Fix: Fotoauswahl zeigte alle Dateien statt nur Bilder an
* Fix: Palette Mannschaftsturnier wurde nicht angezeigt

## Version 3.2.1 (2021-07-15)

* Fix: Hinter jeder Liste steht unformatiert der Listentyp -> besser formatieren -> showColumns = true
* Add: tl_championslists Toogle-Icon eingefügt

## Version 3.2.0 (2021-06-22)

* Change: tl_championslists_items.year geändert von varchar(5) auf varchar(10), um Angaben wie "1999-2000" zu ermöglichen
* Delete: tl_championslists.alias inkl. Funktion generateAlias
* Add: tl_championslists_items.failed - Option zur Kennzeichnung von Absagen im Backend (rote Markierung) und Frontend (CSS-Klasse failed)
* Fix: Sortierung der Meister zuerst nach Jahr, danach zusätzlich nach Nummer

## Version 3.1.1 (2021-03-10)

* Fix: print_r in tl_championslists_item entfernt
* Fix: Standardtemplate im Backend-Header entfernt

## Version 3.1.0 (2021-03-10)

* Delete: Standardtemplate in Meisterlisten Backend - tl_championslists.templatefile
* Fix: Platzierungen werden in Mannschaftsturnieren nicht im Template ausgegeben
* Fix: Korrekturen in default.css - Farbe Jahr/Ort, figure/figcaption-Positionierung
* Delete: tl_championslists_items überflüssige Felder entfernt
* Add: Meisterschaft verlinkt ja/nein in Backend anzeigen
* Fix: Bei leeren Platzierungen nicht "()" im Backend anzeigen

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
