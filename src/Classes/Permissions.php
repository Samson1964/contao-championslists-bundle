<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Classes;

use Contao\BackendUser;
use Contao\Database;
use Contao\StringUtil;

/**
 * Rechteprüfung für einzelne Meisterlisten.
 *
 * Das Vorbild sind die Nachrichtenarchive des Contao-Kerns: Ein Benutzer (oder
 * seine Gruppen) bekommt eine Auswahl erlaubter Meisterlisten (Feld
 * "championslists") und getrennt davon das Recht, ganze Listen anzulegen oder
 * zu löschen (Feld "championslistsp" mit den Werten "create" und "delete").
 *
 * Die Klasse bündelt, was die DCA-Klassen von tl_championslists und
 * tl_championslists_items gemeinsam brauchen. Die reinen Rechenfunktionen
 * (normalizeIds, hasRight) kommen ohne Contao-Umgebung aus und sind damit
 * einzeln testbar.
 */
class Permissions
{
	/**
	 * Name des Feldes mit den erlaubten Meisterlisten in tl_user/tl_user_group.
	 */
	public const FIELD_LISTS = 'championslists';

	/**
	 * Name des Feldes mit den Listen-Rechten (create, delete).
	 */
	public const FIELD_RIGHTS = 'championslistsp';

	/**
	 * Prüft, ob der angemeldete Backend-Benutzer Administrator ist.
	 *
	 * Administratoren unterliegen keiner Einschränkung; alle Prüfungen in den
	 * DCA-Klassen enden für sie sofort.
	 *
	 * @return bool true für Administratoren
	 */
	public static function isAdmin(): bool
	{
		return (bool) BackendUser::getInstance()->isAdmin;
	}

	/**
	 * Liefert die IDs der Meisterlisten, die der angemeldete Benutzer bearbeiten darf.
	 *
	 * Contao führt die Werte des Benutzers und seiner Gruppen selbst zusammen,
	 * weil beide Felder in $GLOBALS['TL_PERMISSIONS'] angemeldet sind (siehe
	 * config.php). Hat der Benutzer keine einzige Liste, kommt array(0) zurück:
	 * Genau diesen Wert erwartet der DC_Table als "list.sorting.root", um eine
	 * leere Übersicht zu zeigen – ein leeres Array würde dagegen als "keine
	 * Einschränkung" verstanden.
	 *
	 * @return array<int> Erlaubte Listen-IDs, mindestens array(0)
	 */
	public static function getAllowedLists(): array
	{
		return self::normalizeIds(BackendUser::getInstance()->{self::FIELD_LISTS});
	}

	/**
	 * Prüft, ob der angemeldete Benutzer ganze Meisterlisten anlegen (und kopieren) darf.
	 *
	 * @return bool true mit dem Recht "create" oder als Administrator
	 */
	public static function canCreate(): bool
	{
		return self::isAdmin() || self::hasRight(BackendUser::getInstance()->{self::FIELD_RIGHTS}, 'create');
	}

	/**
	 * Prüft, ob der angemeldete Benutzer ganze Meisterlisten löschen darf.
	 *
	 * @return bool true mit dem Recht "delete" oder als Administrator
	 */
	public static function canDelete(): bool
	{
		return self::isAdmin() || self::hasRight(BackendUser::getInstance()->{self::FIELD_RIGHTS}, 'delete');
	}

	/**
	 * Prüft, ob eine bestimmte Meisterliste für den angemeldeten Benutzer erlaubt ist.
	 *
	 * @param mixed $varListId ID der Meisterliste; alles, was sich nicht als
	 *                         positive Ganzzahl lesen lässt, gilt als nicht erlaubt
	 *
	 * @return bool true für Administratoren und für freigegebene Listen
	 */
	public static function isListAllowed($varListId): bool
	{
		if (self::isAdmin())
		{
			return true;
		}

		$intListId = (int) $varListId;

		return $intListId > 0 && \in_array($intListId, self::getAllowedLists(), true);
	}

	/**
	 * Wandelt den Inhalt des Feldes "championslists" in eine saubere ID-Liste um.
	 *
	 * Das Feld kommt je nach Herkunft als Array (vom Benutzerobjekt), als
	 * serialisierte Zeichenkette (direkt aus der Datenbank) oder gar nicht
	 * (null, leere Zeichenkette). Die Einträge selbst sind Zeichenketten. Hier
	 * entsteht daraus immer ein Array eindeutiger, positiver Ganzzahlen.
	 *
	 * @param mixed $varLists Rohwert des Feldes
	 *
	 * @return array<int> Die IDs, oder array(0) wenn keine einzige erlaubt ist
	 */
	public static function normalizeIds($varLists): array
	{
		if (\is_string($varLists))
		{
			$varLists = StringUtil::deserialize($varLists, true);
		}

		if (!\is_array($varLists))
		{
			return array(0);
		}

		$arrIds = array_values(array_unique(array_filter(array_map('\intval', $varLists), static fn (int $intId): bool => $intId > 0)));

		return $arrIds ?: array(0);
	}

	/**
	 * Prüft, ob ein Recht im Feld "championslistsp" enthalten ist.
	 *
	 * @param mixed  $varRights Rohwert des Feldes (Array, serialisiert oder leer)
	 * @param string $strRight  Gesuchtes Recht, "create" oder "delete"
	 *
	 * @return bool true wenn das Recht gesetzt ist
	 */
	public static function hasRight($varRights, string $strRight): bool
	{
		if (\is_string($varRights))
		{
			$varRights = StringUtil::deserialize($varRights, true);
		}

		return \is_array($varRights) && \in_array($strRight, $varRights, true);
	}

	/**
	 * Ermittelt die Meisterliste, zu der ein Listeneintrag gehört.
	 *
	 * @param mixed $varItemId ID des Eintrags in tl_championslists_items
	 *
	 * @return int|null ID der Eltern-Liste, oder null wenn es den Eintrag nicht gibt
	 */
	public static function getListOfItem($varItemId): ?int
	{
		$objItem = Database::getInstance()
			->prepare('SELECT pid FROM tl_championslists_items WHERE id=?')
			->limit(1)
			->execute((int) $varItemId);

		return $objItem->numRows ? (int) $objItem->pid : null;
	}
}
