<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Überführt bestehende Installationen in die Rechtevergabe je Meisterliste.
 *
 * Bis Version 4.1 durfte jeder, der das Backend-Modul "Meisterlisten" hatte,
 * alle Listen bearbeiten, anlegen und löschen. Ab 4.2 zählt die Auswahl
 * "Erlaubte Meisterlisten". Ohne diese Migration stünden alle bisherigen
 * Redakteure nach dem Update vor einer leeren Übersicht.
 *
 * Die Migration legt die vier neuen Spalten selbst an und trägt bei jeder
 * Benutzergruppe und jedem Benutzer mit eigenem Modulrecht "championslists"
 * alle vorhandenen Meisterlisten sowie die Rechte "create" und "delete" ein –
 * also genau den Stand, den diese Benutzer vor dem Update hatten.
 *
 * Dass die Spalten hier und nicht erst vom Schema-Abgleich angelegt werden,
 * ist Absicht: Ihr Fehlen ist das einzige verlässliche Kennzeichen für "noch
 * nicht migriert". Ein leeres Feld taugt dafür nicht, weil ein Administrator
 * die Auswahl später bewusst leeren kann – die Migration würde sie sonst bei
 * jedem contao:migrate wieder auffüllen. Migrationen laufen vor dem
 * Schema-Abgleich; der findet die Spalten danach passend vor.
 */
class ListPermissionsMigration extends AbstractMigration
{
	/**
	 * Tabellen, deren Rechte überführt werden.
	 */
	private const TABLES = array('tl_user_group', 'tl_user');

	/**
	 * Die Datenbankverbindung.
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * @param Connection $connection Die Datenbankverbindung der Contao-Installation
	 */
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Liefert den Namen, unter dem contao:migrate die Migration anzeigt.
	 */
	public function getName(): string
	{
		return 'Meisterlisten: Rechte je Liste für bestehende Gruppen und Benutzer';
	}

	/**
	 * Prüft, ob die Migration noch aussteht.
	 *
	 * Sie steht aus, solange die Meisterlisten-Tabelle existiert (also schon
	 * eine ältere Fassung des Bundles installiert war) und mindestens einer der
	 * Benutzertabellen die Spalte "championslists" fehlt. Bei einer
	 * Neuinstallation gibt es tl_championslists noch nicht – dann legt der
	 * Schema-Abgleich die Spalten an, und es gibt nichts zu überführen.
	 *
	 * @return bool true wenn run() ausgeführt werden soll
	 */
	public function shouldRun(): bool
	{
		$schemaManager = $this->connection->createSchemaManager();

		if (!$schemaManager->tablesExist(array('tl_championslists', 'tl_user_group', 'tl_user')))
		{
			return false;
		}

		foreach (self::TABLES as $strTable)
		{
			if (!$this->hasColumn($strTable, 'championslists'))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Legt die Spalten an und trägt den bisherigen Stand ein.
	 *
	 * Berücksichtigt werden nur Zeilen, deren eigenes Feld "modules" das Modul
	 * "championslists" enthält. Administratoren brauchen nichts (sie sehen
	 * ohnehin alles), ebenso Benutzer, die ihre Rechte ausschließlich von
	 * Gruppen erben – bei ihnen wirkt der Eintrag an der Gruppe.
	 *
	 * @return MigrationResult Erfolgsmeldung mit der Zahl der angepassten Zeilen
	 */
	public function run(): MigrationResult
	{
		// Nur Tabellen überführen, denen die Auswahl-Spalte wirklich fehlte.
		// Bricht ein Lauf nach der ersten Tabelle ab, bleibt deren inzwischen
		// vielleicht schon gepflegte Auswahl beim zweiten Versuch unangetastet.
		$arrMigrate = array();

		foreach (self::TABLES as $strTable)
		{
			if (!$this->hasColumn($strTable, 'championslists'))
			{
				$arrMigrate[] = $strTable;
			}

			foreach (array('championslists', 'championslistsp') as $strColumn)
			{
				if (!$this->hasColumn($strTable, $strColumn))
				{
					$this->connection->executeStatement('ALTER TABLE '.$strTable.' ADD '.$strColumn.' BLOB DEFAULT NULL');
				}
			}
		}

		$arrLists = array_map('strval', $this->connection->fetchFirstColumn('SELECT id FROM tl_championslists ORDER BY id'));
		$strLists = serialize($arrLists);
		$strRights = serialize(array('create', 'delete'));
		$intCount = 0;

		foreach ($arrMigrate as $strTable)
		{
			$strWhere = 'tl_user' === $strTable ? " WHERE admin != '1' AND inherit != 'group'" : '';

			foreach ($this->connection->fetchAllAssociative('SELECT id, modules FROM '.$strTable.$strWhere) as $arrRow)
			{
				if (!$this->hasModule($arrRow['modules']))
				{
					continue;
				}

				$this->connection->executeStatement(
					'UPDATE '.$strTable.' SET championslists=?, championslistsp=? WHERE id=?',
					array($arrLists ? $strLists : null, $strRights, $arrRow['id'])
				);

				++$intCount;
			}
		}

		return $this->createResult(true, sprintf('Meisterlisten-Rechte für %d Gruppen/Benutzer übernommen (%d Listen).', $intCount, \count($arrLists)));
	}

	/**
	 * Prüft, ob eine Tabelle eine Spalte besitzt.
	 *
	 * Doctrine liefert die Spalten als Array mit den kleingeschriebenen
	 * Spaltennamen als Schlüssel; so prüfen es auch die Migrationen des
	 * Contao-Kerns.
	 *
	 * @param string $strTable  Tabellenname
	 * @param string $strColumn Spaltenname
	 *
	 * @return bool true wenn die Spalte existiert
	 */
	private function hasColumn(string $strTable, string $strColumn): bool
	{
		$arrColumns = $this->connection->createSchemaManager()->listTableColumns($strTable);

		return isset($arrColumns[strtolower($strColumn)]);
	}

	/**
	 * Prüft, ob das serialisierte Feld "modules" das Modul "championslists" enthält.
	 *
	 * Bewusst mit unserialize() und gesperrten Klassen statt über Contaos
	 * StringUtil: Migrationen laufen, bevor das Contao-Framework initialisiert
	 * ist.
	 *
	 * @param mixed $varModules Rohwert der Spalte "modules"
	 *
	 * @return bool true wenn das Modul freigeschaltet ist
	 */
	private function hasModule($varModules): bool
	{
		if (!\is_string($varModules) || '' === $varModules)
		{
			return false;
		}

		$arrModules = @unserialize($varModules, array('allowed_classes' => false));

		return \is_array($arrModules) && \in_array('championslists', $arrModules, true);
	}
}
