<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Tests\Dca;

use Contao\DataContainer;
use Contao\DC_Table;
use PHPUnit\Framework\TestCase;

/**
 * Prüft die DCA-Definitionen auf Contao-5-Tauglichkeit.
 *
 * Die Tests laden die DCA-Dateien wie Contao selbst (erst Sprachdateien, dann
 * DCA) und prüfen anschließend die Struktur. Damit werden Regressionen wie
 * GIF-Symbole, fehlende Toggle-Markierungen oder fehlende Sprachschlüssel
 * erkannt, bevor sie im Backend auffallen.
 */
class DcaConfigurationTest extends TestCase
{
	/**
	 * @var array<string, mixed>
	 */
	private static $arrDca = array();

	public static function setUpBeforeClass(): void
	{
		$strBase = \dirname(__DIR__, 2).'/src/Resources/contao';

		// Grundgerüst der Kern-DCA, die vom Bundle erweitert werden
		$GLOBALS['TL_DCA']['tl_content'] = array('palettes' => array('__selector__' => array()), 'fields' => array());
		$GLOBALS['TL_DCA']['tl_settings'] = array('palettes' => array('default' => '{global_legend},dateFormat'), 'fields' => array());

		foreach (glob($strBase.'/languages/de/*.php') as $strFile)
		{
			include $strFile;
		}

		foreach (array('tl_championslists', 'tl_championslists_categories', 'tl_championslists_items', 'tl_content', 'tl_settings') as $strTable)
		{
			include_once $strBase.'/dca/'.$strTable.'.php';
		}

		self::$arrDca = $GLOBALS['TL_DCA'];
	}

	/**
	 * @return array<int, array<int, string>>
	 */
	public function eigeneTabellen(): array
	{
		return array(
			array('tl_championslists'),
			array('tl_championslists_categories'),
			array('tl_championslists_items'),
		);
	}

	/**
	 * In Contao 5 muss der Datencontainer als voll qualifizierter Klassenname
	 * angegeben werden; die Kurzform "Table" funktioniert dort nicht mehr.
	 *
	 * @dataProvider eigeneTabellen
	 */
	public function testDatencontainerAlsKlassennamen(string $strTable): void
	{
		$this->assertSame(DC_Table::class, self::$arrDca[$strTable]['config']['dataContainer']);
	}

	/**
	 * Contao 5 liefert keine GIF-Symbole mehr aus.
	 *
	 * @dataProvider eigeneTabellen
	 */
	public function testOperationenVerwendenSvgSymbole(string $strTable): void
	{
		foreach (self::$arrDca[$strTable]['list']['operations'] as $strName => $arrOperation)
		{
			$this->assertArrayHasKey('icon', $arrOperation, $strTable.'.'.$strName);
			$this->assertStringEndsNotWith('.gif', $arrOperation['icon'], $strTable.'.'.$strName);
		}
	}

	/**
	 * Der Toggle wird vom Contao-Kern erledigt (act=toggle). Dafür muss das Feld
	 * ausdrücklich freigegeben sein, sonst verweigert der DC_Table den Zugriff.
	 *
	 * @dataProvider eigeneTabellen
	 */
	public function testToggleOperationIstKorrektVerdrahtet(string $strTable): void
	{
		$arrToggle = self::$arrDca[$strTable]['list']['operations']['toggle'];

		$this->assertSame('act=toggle&amp;field=published', $arrToggle['href'], $strTable);
		$this->assertSame('visible.svg', $arrToggle['icon'], $strTable);
		$this->assertTrue(self::$arrDca[$strTable]['fields']['published']['toggle'], $strTable);

		// Die Haste-Erweiterung wird nicht mehr benötigt
		$this->assertArrayNotHasKey('haste_ajax_operation', $arrToggle, $strTable);
	}

	/**
	 * Jede Beschriftung wird per Referenz aus den Sprachdateien gezogen. Fehlt
	 * ein Schlüssel, entsteht im Backend eine "Undefined array key"-Warnung.
	 *
	 * @dataProvider eigeneTabellen
	 */
	public function testAlleBeschriftungenSindUebersetzt(string $strTable): void
	{
		foreach (self::$arrDca[$strTable]['list']['operations'] as $strName => $arrOperation)
		{
			$this->assertNotEmpty($arrOperation['label'], $strTable.'.operations.'.$strName);
		}

		foreach (self::$arrDca[$strTable]['fields'] as $strField => $arrField)
		{
			if (!isset($arrField['inputType']))
			{
				continue;
			}

			$this->assertNotEmpty($arrField['label'], $strTable.'.fields.'.$strField);
		}
	}

	/**
	 * Alle Felder der Palette müssen auch definiert sein.
	 *
	 * @dataProvider eigeneTabellen
	 */
	public function testPalettenFelderSindDefiniert(string $strTable): void
	{
		foreach (self::$arrDca[$strTable]['palettes'] as $strName => $strPalette)
		{
			if ('__selector__' === $strName)
			{
				continue;
			}

			foreach (explode(',', preg_replace('/\{[^}]+\}/', '', $strPalette)) as $strField)
			{
				$strField = trim($strField, ' ;');

				if ('' === $strField)
				{
					continue;
				}

				$this->assertArrayHasKey($strField, self::$arrDca[$strTable]['fields'], $strTable.'.'.$strName);
			}
		}
	}

	public function testItemsWerdenAlsUntertabelleGefuehrt(): void
	{
		$this->assertSame(array('tl_championslists_items'), self::$arrDca['tl_championslists']['config']['ctable']);
		$this->assertSame('tl_championslists', self::$arrDca['tl_championslists_items']['config']['ptable']);
		$this->assertSame(DataContainer::MODE_PARENT, self::$arrDca['tl_championslists_items']['list']['sorting']['mode']);
	}

	public function testInhaltselementeSindInTlContentEingetragen(): void
	{
		foreach (array('champion', 'championslists_mono', 'championslists_multi') as $strType)
		{
			$this->assertArrayHasKey($strType, self::$arrDca['tl_content']['palettes'], $strType);
		}

		$this->assertContains('championslist_filter', self::$arrDca['tl_content']['palettes']['__selector__']);

		// Ohne das nur bis Contao 4.13 vorhandene Feld "guests" darf es nicht in
		// der Palette auftauchen
		$this->assertStringNotContainsString('guests', self::$arrDca['tl_content']['palettes']['championslists_mono']);
		$this->assertSame('championsfrom,championsto', self::$arrDca['tl_content']['subpalettes']['championslist_filter']);

		foreach (array('championslist', 'championslist_filter', 'championsfrom', 'championsto') as $strField)
		{
			$this->assertArrayHasKey('sql', self::$arrDca['tl_content']['fields'][$strField], $strField);
		}
	}

	public function testSystemeinstellungenWerdenErgaenzt(): void
	{
		$this->assertStringContainsString('championslists_legend', self::$arrDca['tl_settings']['palettes']['default']);

		foreach (array('championslists_defaultImageMen', 'championslists_defaultImageWomen', 'championslists_defaultImageTeamsMen', 'championslists_defaultImageTeamsWomen', 'championslists_imageSizePlayer', 'championslists_imageSizeTeam') as $strField)
		{
			$this->assertArrayHasKey($strField, self::$arrDca['tl_settings']['fields'], $strField);
			$this->assertStringContainsString($strField, self::$arrDca['tl_settings']['palettes']['default'], $strField);
		}
	}

	/**
	 * Die Platzierungen des MultiColumnWizards liefern die im Frontend und im
	 * Backend ausgewerteten Spalten.
	 */
	public function testPlatzierungenSpaltenSindVollstaendig(): void
	{
		$arrSpalten = self::$arrDca['tl_championslists_items']['fields']['platzierungen']['eval']['columnFields'];

		foreach (array('platz', 'name', 'verein', 'alter', 'rating', 'image', 'spielerregister', 'aufstellung') as $strSpalte)
		{
			$this->assertArrayHasKey($strSpalte, $arrSpalten, $strSpalte);
		}
	}

	/**
	 * Das Spielerregister-Bundle ist optional. Die Optionen dürfen deshalb nur
	 * über den eigenen Callback geladen werden, der die Installation prüft.
	 */
	public function testSpielerregisterWirdUeberEigenenCallbackGeladen(): void
	{
		$arrErwartet = array('tl_championslists_items', 'getSpielerregister');

		$this->assertSame($arrErwartet, self::$arrDca['tl_championslists_items']['fields']['spielerregister_id']['options_callback']);
		$this->assertSame($arrErwartet, self::$arrDca['tl_championslists_items']['fields']['platzierungen']['eval']['columnFields']['spielerregister']['options_callback']);
	}

}
