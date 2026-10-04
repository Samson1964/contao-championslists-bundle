# Meisterlisten für Contao 4.13 und Contao 5

Das Bundle verwaltet Meisterlisten (Turniersieger und weitere Platzierungen) und gibt sie
über Inhaltselemente im Frontend aus.

## Systemvoraussetzungen

* PHP 7.4 oder neuer (Contao 5 setzt PHP 8.1 voraus)
* Contao 4.13 LTS oder Contao 5
* `menatwork/contao-multicolumnwizard-bundle` (wird automatisch installiert)

Optional: `schachbulle/contao-spielerregister-bundle`. Ist es installiert, lassen sich die
Meister mit dem DSB-Spielerregister verknüpfen. Ohne das Bundle bleiben die entsprechenden
Auswahllisten leer, alles andere funktioniert unverändert.

## Einstellungen

Unter *System → Einstellungen → Meisterlisten* werden die Standardbilder und die Bildgrößen
für Einzel- und Mannschaftswettbewerbe hinterlegt. Ist kein Bild für Frauen bzw.
Frauen-Mannschaften gesetzt, wird das jeweilige Männerbild verwendet.

## Backend

Das Backend-Modul *Meisterlisten* enthält drei Bereiche:

* **Meisterlisten** – eine Liste je Wettbewerb, mit Listentyp (Einzel-/Mannschaftsturnier,
  jeweils männlich/weiblich)
* **Listeneinträge** – ein Eintrag je Austragung, inklusive Turniersieger und weiterer
  Platzierungen (MultiColumnWizard)
* **Platzierungsnamen** – die Kategorien der weiteren Platzierungen. Das Alias `meister` ist
  für den Turniersieger reserviert und kann nicht vergeben werden.

## Rechte

Der Zugriff lässt sich je Meisterliste vergeben – nach demselben Muster wie bei den
Nachrichtenarchiven. In der Benutzergruppe (und beim Benutzer, sofern er eigene Rechte hat)
gibt es dafür den Abschnitt *Meisterlisten-Rechte* mit zwei Feldern:

| Feld | Wirkung |
|---|---|
| **Erlaubte Meisterlisten** | Nur diese Listen erscheinen in der Übersicht. Ihre Einträge lassen sich anlegen, bearbeiten, verschieben, kopieren und löschen. |
| **Meisterlisten-Rechte** | *Anlegen* erlaubt neue Listen und das Kopieren ganzer Listen, *Löschen* das Entfernen ganzer Listen. |

Dazu gilt:

* Voraussetzung bleibt das Backend-Modul *Meisterlisten* unter den erlaubten Modulen.
* Administratoren sehen und dürfen immer alles.
* Wer eine Liste anlegt oder kopiert, bekommt sie automatisch freigeschaltet. Eingetragen wird
  sie dort, wo das Recht *Anlegen* herkommt – bei der Gruppe ebenso wie beim Benutzer. Die
  Kollegen derselben Gruppe sehen die neue Liste damit auch.
* Geprüft wird immer die Liste, zu der ein Eintrag gehört, und beim Verschieben oder Kopieren
  zusätzlich die Ziel-Liste. Ein von Hand eingegebener Link auf eine fremde Liste endet mit
  „Zugriff verweigert“.
* Die *Platzierungsnamen* hängen an keiner einzelnen Liste, sondern gelten für alle. Sie
  bleiben für jeden bearbeitbar, der das Modul hat.
* **Contao 5.7 und neuer** kennt zusätzlich die allgemeinen *Tabellenrechte* am Benutzer
  bzw. an der Gruppe. Beide Ebenen müssen zustimmen: Wer dort für
  `tl_championslists` oder `tl_championslists_items` kein Anlegen/Ändern/Löschen hat, kann es
  auch mit den Meisterlisten-Rechten nicht.

### Aktualisierung von einer Fassung vor 4.2

Bis 4.1 durfte jeder mit dem Modul *Meisterlisten* alle Listen bearbeiten. Damit nach dem
Update niemand vor einer leeren Übersicht steht, überführt eine Migration den bisherigen
Stand: Jede Gruppe und jeder Benutzer mit eigenem Modulrecht bekommt alle vorhandenen Listen
sowie *Anlegen* und *Löschen*. Sie läuft genau einmal, zusammen mit der
Datenbank-Aktualisierung:

```bash
php vendor/bin/contao-console cache:clear
php vendor/bin/contao-console contao:migrate
```

Im Contao Manager erledigt das der Schritt *Datenbank aktualisieren*. Danach lassen sich die
Rechte in den Gruppen gezielt einschränken.

## Inhaltselemente

Alle Elemente liegen unter *Schach-Elemente*.

### Meisterliste Einzelwettbewerb (`championslists_mono`)

Gibt eine Liste vom Typ „Einzelturnier“ aus. Optional lassen sich die Einträge über einen
Jahresfilter (Von/Bis) einschränken. Templates: `ce_championslists_mono` (Standard) und
`ce_championslists_mono_mini` (Tabellenform).

### Meisterliste Mannschaftswettbewerb (`championslists_multi`)

Gibt eine Liste vom Typ „Mannschaftsturnier“ aus. Templates: `ce_championslists_multi` und
`ce_championslists_multi_mini`.

Im Template steht `$this->item` als Array aller Einträge bereit:

| Schlüssel | Inhalt |
|---|---|
| `id` | ID des Listeneintrags |
| `nummer` | Laufende Nummer der Veranstaltung |
| `jahr` | Jahr der Veranstaltung |
| `ort` | Ort der Veranstaltung |
| `class` | `odd`/`even`, bei ausgefallenen Veranstaltungen zusätzlich `failed` |
| `linkurl`, `linkziel` | Link zur Detailseite und ob er in einem neuen Fenster öffnet |
| `name` | Name des Turniersiegers bzw. der Meistermannschaft |
| `info` | Freitext zur Veranstaltung |
| `platz` | Platzierungen, indiziert nach Kategorie-Alias (`meister` = Turniersieger) |

Jede Platzierung unter `platz` enthält `name`, `aufstellung`, `image`, `thumbnail`,
`imageSize`, `imageTitle`, `imageAlt` und `imageCaption`; bei Einzelwettbewerben zusätzlich
`alter`, `verein` und `rating`.

`aufstellung` erlaubt echte HTML-Auszeichnung (das Feld heißt im Backend „Aufstellung“ bzw.
„Aufstellung der Mannschaft“). Zeilenumbrüche aus einer einfachen mehrzeiligen Eingabe bleiben
als `\n` erhalten; das mitgelieferte Standard-Template gibt sie über `nl2br()` aus. Ein eigenes
Template sollte das ebenso tun, sonst laufen mehrzeilige Aufstellungen im HTML zu einer Zeile
zusammen.

### Aktueller Meister (`champion`)

Gibt den jüngsten Eintrag einer Meisterliste aus, bei dem das Feld „Name“ gefüllt ist. Die
Bildgröße wird direkt am Inhaltselement eingestellt. Standard-Template: `ce_champion`. Für
eine abweichende Darstellung ein eigenes Template mit dem Präfix `ce_champion` anlegen und
unter *Template-Einstellungen* auswählen.

Im Template steht `$this->item` mit diesen Schlüsseln bereit:

* `id`, `number`, `year`, `place`, `url`, `target`
* `name`, `nomination`, `age`, `verein`, `rating`
* `clubrating` (Verein und Wertungszahl kombiniert, für ältere eigene Templates)
* `image`, `thumbnail`, `imageSize`, `imageTitle`, `imageAlt`, `imageCaption`
* `info`

Für `nomination` gilt dieselbe Anmerkung wie für `aufstellung` oben: HTML ist erlaubt,
Zeilenumbrüche bleiben als `\n` erhalten und müssen im Template per `nl2br()` ausgegeben
werden. Das mitgelieferte Standard-Template `ce_champion` zeigt nur den Namen an.

### Ausgabe des Meisters mit Inserttag

`{{meister::ID}}` gibt den Namen des aktuellen Meisters der Meisterliste mit der angegebenen
ID zurück.

## Tests

```bash
vendor/bin/phpunit
```

**Frank Binding**
