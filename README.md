# Meisterliste für Contao 4

## Ausgabe des aktuellen Meisters

Dazu muß als Inhaltselement "Aktueller Meister" (unter Schach-Elemente) ausgewählt werden. Das Inhaltselement gibt den aktuellen Meister einer ausgewählten Meisterliste aus. Voraussetzung ist, das das Feld "name" ausgefüllt ist.
Das Standard-Template ce_champion_default zeigt ein Foto an und den Namen. Um etwas anderes anzuzeigen, muß ein anderes Template mit dem Prefix ce_champion angelegt werden.

Im Template werden die folgenden Felder übergeben:

* id
* number
* year
* place
* url
* target
* name
* nomination
* age
* clubrating
* image
* thumbnail
* imageSize
* imageTitle
* imageAlt
* imageCaption
* info

### Ausgabe des Meisters mit Inserttag

{{meister::id}} gibt den Namen des aktuellen Meisters einer Meisterliste gemäß ID zurück.

**Frank Binding**
