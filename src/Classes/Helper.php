<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Classes;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Psr\Log\LogLevel;

/**
 * Sammlung wiederverwendbarer Hilfsfunktionen der Meisterlisten.
 *
 * Die Klasse kapselt den Zugriff auf die Platzierungs-Kategorien sowie die
 * Bildaufbereitung, damit Inhaltselemente und DCA-Dateien denselben Code nutzen.
 */
class Helper
{
	/**
	 * Zwischenspeicher für die Kategorien, damit pro Request nur einmal
	 * abgefragt wird.
	 *
	 * @var array<string, array<int, string>>|null
	 */
	private static $arrCategories;

	/**
	 * Bereits protokollierte Meldungen, um Wiederholungen zu vermeiden.
	 *
	 * @var array<string, true>
	 */
	private static $arrLogged = array();

	/**
	 * Sentinel für "kein Bild ausgewählt" in älteren Datensätzen.
	 *
	 * Die Spalte singleSRC ist heute "binary(16) NULL", war es aber nicht
	 * immer. Solange sie NOT NULL war, füllte MySQL einen leeren BINARY-Wert
	 * automatisch mit 16 Nullbytes statt NULL zu speichern; eine spätere
	 * Umstellung der Spalte auf NULL ändert diese bereits gespeicherten Werte
	 * nicht rückwirkend. Ohne diese Erkennung versucht Helper::getImageData()
	 * für jeden dieser Altdatensätze eine nie auffindbare Datei zu laden und
	 * protokolliert das als Fehler.
	 */
	private const NULL_UUID = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

	/**
	 * Liefert die Aliase der Kategorien, indiziert nach Kategorie-ID.
	 *
	 * @return array<int, string>
	 */
	public static function getAliase(): array
	{
		return self::getCategories()['alias'];
	}

	/**
	 * Liefert die Titel der Kategorien, indiziert nach Kategorie-ID.
	 *
	 * @return array<int, string>
	 */
	public static function getTitles(): array
	{
		return self::getCategories()['title'];
	}

	/**
	 * Bereitet ein Bild für die Ausgabe im Template auf.
	 *
	 * Ist das angegebene Bild nicht (mehr) vorhanden, wird das übergebene
	 * Standardbild verwendet. Lässt sich auch dieses nicht verarbeiten, werden
	 * leere Werte geliefert, damit die Templates keine Warnungen erzeugen.
	 *
	 * @param mixed                    $varUuid         UUID/ID des gewünschten Bildes
	 * @param array<int, mixed>|string|int|null $varSize Bildgröße (Contao-Bildgrößen-Array oder -ID)
	 * @param mixed                    $varFallbackUuid UUID des Standardbildes
	 * @param string                   $strContext      Zusatzinfo für das Systemprotokoll
	 *
	 * @return array<string, mixed>
	 */
	public static function getImageData($varUuid, $varSize = null, $varFallbackUuid = null, string $strContext = ''): array
	{
		$strSuffix = $strContext ? ' ('.$strContext.')' : '';

		if (self::NULL_UUID === $varUuid)
		{
			$varUuid = null;
		}

		if ($varUuid)
		{
			$arrImage = self::buildImage($varUuid, $varSize);

			if (null !== $arrImage)
			{
				return $arrImage;
			}

			// Der Sperrschlüssel enthält bewusst nur die UUID: Verweisen hundert
			// Einträge auf dieselbe fehlende Datei, genügt eine Meldung.
			//
			// Schweregrad WARNING statt ERROR: Ein einzelner Eintrag mit einer
			// veralteten oder gelöschten Bildreferenz ist eine erwartbare
			// Alltäglichkeit bei einer über Jahre gepflegten Liste, kein
			// Anwendungsfehler - die Seite rendert korrekt mit dem Standardbild.
			$strUuid = self::formatUuid($varUuid);
			self::log('Kein gültiges Bild gefunden'.$strSuffix.': '.$strUuid, 'bild:'.$strUuid, LogLevel::WARNING);
		}

		// Standardbild als Rückfallebene verwenden
		if ($varFallbackUuid)
		{
			$arrImage = self::buildImage($varFallbackUuid, $varSize);

			if (null !== $arrImage)
			{
				return $arrImage;
			}

			$strUuid = self::formatUuid($varFallbackUuid);
			self::log('Standardbild konnte nicht verarbeitet werden'.$strSuffix.': '.$strUuid, 'standardbild:'.$strUuid);
		}

		return self::getEmptyImageData();
	}

	/**
	 * Bereitet ein einzelnes Bild auf, liefert null wenn es nicht verarbeitet
	 * werden kann (Datei fehlt, kein Bild, nicht unterstütztes Format).
	 *
	 * @param mixed $varUuid
	 * @param mixed $varSize
	 *
	 * @return array<string, mixed>|null
	 */
	private static function buildImage($varUuid, $varSize): ?array
	{
		$objFile = FilesModel::findByPk($varUuid);

		if (null === $objFile)
		{
			return null;
		}

		$objFigure = System::getContainer()
			->get('contao.image.studio')
			->createFigureBuilder()
			->fromFilesModel($objFile)
			->setSize(self::normalizeSize($varSize))
			->buildIfResourceExists();

		if (null === $objFigure)
		{
			return null;
		}

		return array_merge(self::getEmptyImageData(), $objFigure->getLegacyTemplateData());
	}

	/**
	 * Liefert die Kategorien als Aliase und Titel.
	 *
	 * @return array<string, array<int, string>>
	 */
	private static function getCategories(): array
	{
		if (null !== self::$arrCategories)
		{
			return self::$arrCategories;
		}

		$arrAlias = array();
		$arrTitle = array();

		$objKategorien = Database::getInstance()
			->prepare('SELECT id, title, alias FROM tl_championslists_categories')
			->execute();

		while ($objKategorien->next())
		{
			$arrAlias[(int) $objKategorien->id] = (string) $objKategorien->alias;
			$arrTitle[(int) $objKategorien->id] = (string) $objKategorien->title;
		}

		return self::$arrCategories = array('alias' => $arrAlias, 'title' => $arrTitle);
	}

	/**
	 * Setzt den Kategorien-Zwischenspeicher zurück (wird von den Tests genutzt).
	 */
	public static function resetCategories(): void
	{
		self::$arrCategories = null;
	}

	/**
	 * Normalisiert die Bildgrößen-Angabe aus den Systemeinstellungen.
	 *
	 * Serialisierte Werte werden entpackt; ein Array ohne jeden Inhalt (etwa
	 * array('', '', '')) wird zu null, damit die Bildkonfiguration nicht leer
	 * an den FigureBuilder übergeben wird.
	 *
	 * @param mixed $varSize
	 *
	 * @return array<int, mixed>|string|int|null
	 */
	public static function normalizeSize($varSize)
	{
		if (\is_string($varSize))
		{
			$varSize = StringUtil::deserialize($varSize);
		}

		if (\is_array($varSize))
		{
			// Ein Array ohne jeden Inhalt würde zu einer leeren Bildkonfiguration führen
			return array_filter($varSize) ? $varSize : null;
		}

		return $varSize ?: null;
	}

	/**
	 * Liefert die im Template erwarteten Bildschlüssel mit leeren Werten.
	 *
	 * @return array<string, string>
	 */
	public static function getEmptyImageData(): array
	{
		return array
		(
			'singleSRC'    => '',
			'src'          => '',
			'imgSize'      => '',
			'alt'          => '',
			'imageTitle'   => '',
			'caption'      => '',
		);
	}

	/**
	 * Wandelt eine (binäre) UUID für die Protokollausgabe in Textform um.
	 *
	 * @param mixed $varUuid
	 */
	private static function formatUuid($varUuid): string
	{
		if (\is_string($varUuid) && 16 === \strlen($varUuid))
		{
			return StringUtil::binToUuid($varUuid);
		}

		return \is_scalar($varUuid) ? (string) $varUuid : '';
	}

	/**
	 * Schreibt eine Meldung in das Contao-Systemprotokoll.
	 *
	 * Ersetzt die in Contao 5 entfallene Funktion log_message(). Gleichartige
	 * Meldungen werden pro Aufruf nur einmal geschrieben: Eine Meisterliste kann
	 * mehrere hundert Einträge enthalten, die alle auf dieselbe fehlende Datei
	 * zeigen – ohne diese Sperre liefe das Protokoll bei jedem Seitenaufruf voll.
	 *
	 * Die Kontext-Aktion wird bewusst nicht gesetzt: Contaos eigener
	 * ContaoTableProcessor leitet sie selbst aus dem Schweregrad ab (ab "error"
	 * aufwärts "ERROR", sonst "GENERAL") - genau wie der Contao-Kern es an
	 * eigenen Stellen macht.
	 *
	 * @param string      $strMessage Die Meldung für das Protokoll
	 * @param string|null $strKey     Sperrschlüssel; ohne Angabe gilt die Meldung
	 *                                selbst als Schlüssel
	 * @param string      $strLevel   PSR-3-Schweregrad (Konstante aus Psr\Log\LogLevel)
	 */
	public static function log(string $strMessage, ?string $strKey = null, string $strLevel = LogLevel::ERROR): void
	{
		$strKey = $strKey ?? $strMessage;

		if (isset(self::$arrLogged[$strKey]))
		{
			return;
		}

		self::$arrLogged[$strKey] = true;

		$container = System::getContainer();

		if (null === $container || !$container->has('monolog.logger.contao'))
		{
			return;
		}

		$container->get('monolog.logger.contao')->log(
			$strLevel,
			$strMessage,
			array('contao' => new ContaoContext(__METHOD__))
		);
	}
}
