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

### Ausgabe des Meisters mit Inserttag

`{{meister::ID}}` gibt den Namen des aktuellen Meisters der Meisterliste mit der angegebenen
ID zurück.

## Tests

```bash
vendor/bin/phpunit
```

**Frank Binding**
