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
use Contao\StringUtil;
use Contao\Validator;
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
		$GLOBALS['TL_DCA']['tl_user'] = array('palettes' => array('extend' => '{name_legend},name;{amg_legend},alexf', 'custom' => '{name_legend},name;{amg_legend},alexf', 'group' => '{name_legend},name'), 'fields' => array());
		$GLOBALS['TL_DCA']['tl_user_group'] = array('palettes' => array('default' => '{title_legend},name;{amg_legend},alexf'), 'fields' => array());

		foreach (glob($strBase.'/languages/de/*.php') as $strFile)
		{
			include $strFile;
		}

		foreach (array('tl_championslists', 'tl_championslists_categories', 'tl_championslists_items', 'tl_content', 'tl_settings', 'tl_user', 'tl_user_group') as $strTable)
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
	 * Die vier Standardbilder legen ihre Datei-Kennung in der lesbaren
	 * Schreibweise ab.
	 *
	 * Der Dateibaum liefert die Kennung als 16 Byte langen Binärwert. Die
	 * Einstellungen landen aber in einer PHP-Datei mit einfach gequoteten
	 * Zeichenketten, in der Nullbytes und Backslashes verloren gehen — der Wert
	 * käme beschädigt zurück und die Datei wäre nicht mehr auffindbar. Der
	 * save_callback muss deshalb umwandeln, ohne dabei einen bereits lesbaren
	 * oder einen leeren Wert anzutasten.
	 */
	public function testStandardbilderWerdenLesbarGespeichert(): void
	{
		// Enthält bewusst 0x00 und 0x5c, also genau die kritischen Bytes
		$strUuid = '5c00335c-8eb1-11f1-af96-005c97f36200';
		$binUuid = StringUtil::uuidToBin($strUuid);

		foreach (array('championslists_defaultImageMen', 'championslists_defaultImageWomen', 'championslists_defaultImageTeamsMen', 'championslists_defaultImageTeamsWomen') as $strField)
		{
			$arrCallbacks = self::$arrDca['tl_settings']['fields'][$strField]['save_callback'] ?? array();

			$this->assertNotEmpty($arrCallbacks, $strField);

			// Die Rückrufe nacheinander anwenden, wie DC_File es tut
			$fnAnwenden = static function ($varValue) use ($arrCallbacks) {
				foreach ($arrCallbacks as $callback)
				{
					$varValue = $callback($varValue);
				}

				return $varValue;
			};

			$varGespeichert = $fnAnwenden($binUuid);

			$this->assertSame($strUuid, $varGespeichert, $strField);
			$this->assertTrue(Validator::isStringUuid($varGespeichert), $strField);
			$this->assertSame($binUuid, StringUtil::uuidToBin($varGespeichert), $strField);

			// Ein zweiter Durchlauf darf nichts mehr verändern
			$this->assertSame($strUuid, $fnAnwenden($strUuid), $strField);

			// Kein Bild ausgewählt
			$this->assertSame('', $fnAnwenden(''), $strField);
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
	 * Die Rechte je Meisterliste hängen an zwei Feldern, die in tl_user und
	 * tl_user_group gleich aufgebaut sein müssen – Contao führt Benutzer- und
	 * Gruppenwerte nur zusammen, wenn beide Tabellen dieselben Feldnamen haben.
	 *
	 * @dataProvider benutzerTabellen
	 */
	public function testRechtefelderSindAngelegt(string $strTable): void
	{
		$arrFelder = self::$arrDca[$strTable]['fields'];

		$this->assertSame('checkbox', $arrFelder['championslists']['inputType']);
		$this->assertSame('tl_championslists.title', $arrFelder['championslists']['foreignKey']);
		$this->assertTrue($arrFelder['championslists']['eval']['multiple']);
		$this->assertSame('blob NULL', $arrFelder['championslists']['sql']);

		$this->assertSame(array('create', 'delete'), $arrFelder['championslistsp']['options']);
		$this->assertTrue($arrFelder['championslistsp']['eval']['multiple']);
		$this->assertSame('blob NULL', $arrFelder['championslistsp']['sql']);

		foreach (array('championslists', 'championslistsp') as $strFeld)
		{
			$this->assertNotEmpty($arrFelder[$strFeld]['label'], $strTable.'.'.$strFeld);
			$this->assertTrue($arrFelder[$strFeld]['exclude'], $strTable.'.'.$strFeld);
		}
	}

	/**
	 * @return array<int, array<int, string>>
	 */
	public function benutzerTabellen(): array
	{
		return array(array('tl_user'), array('tl_user_group'));
	}

	/**
	 * Die Rechtefelder stehen vor den erlaubten Feldern (amg_legend) in einer
	 * eigenen Legende. Beim Benutzer nur dort, wo er eigene Rechte haben kann –
	 * nicht in der Palette "group", in der alles von den Gruppen kommt.
	 */
	public function testRechtefelderStehenInDenPaletten(): void
	{
		$strErwartet = '{championslists_legend},championslists,championslistsp;{amg_legend}';

		$this->assertStringContainsString($strErwartet, self::$arrDca['tl_user']['palettes']['extend']);
		$this->assertStringContainsString($strErwartet, self::$arrDca['tl_user']['palettes']['custom']);
		$this->assertStringNotContainsString('championslists', self::$arrDca['tl_user']['palettes']['group']);
		$this->assertStringContainsString($strErwartet, self::$arrDca['tl_user_group']['palettes']['default']);

		$this->assertNotEmpty($GLOBALS['TL_LANG']['tl_user']['championslists_legend']);
		$this->assertNotEmpty($GLOBALS['TL_LANG']['tl_user_group']['championslists_legend']);
	}

	/**
	 * Die Prüfung muss beim Laden des Datencontainers laufen, und zwar vor
	 * allem anderen. Neue und kopierte Listen werden dem Ersteller freigeschaltet.
	 */
	public function testRechtepruefungIstVerdrahtet(): void
	{
		$arrListen = self::$arrDca['tl_championslists']['config'];

		$this->assertSame(array('tl_championslists', 'checkPermission'), $arrListen['onload_callback'][0]);
		$this->assertSame(array('tl_championslists', 'adjustPermissionsOnCreate'), $arrListen['oncreate_callback'][0]);
		$this->assertSame(array('tl_championslists', 'adjustPermissionsOnCopy'), $arrListen['oncopy_callback'][0]);

		$this->assertSame(
			array('tl_championslists_items', 'checkPermission'),
			self::$arrDca['tl_championslists_items']['config']['onload_callback'][0]
		);

		foreach (array(array('tl_championslists', 'checkPermission'), array('tl_championslists', 'adjustPermissionsOnCreate'), array('tl_championslists', 'adjustPermissionsOnCopy'), array('tl_championslists_items', 'checkPermission')) as $arrCallback)
		{
			$this->assertTrue(method_exists($arrCallback[0], $arrCallback[1]), implode('::', $arrCallback));
		}
	}

	/**
	 * Die Aufstellung soll echte HTML-Auszeichnung erlauben (z. B. <strong>,
	 * Links). Das Standard-Template wandelt reine Zeilenumbrüche zusätzlich
	 * per nl2br() um; ohne "allowHtml" würde Contao Zeichen wie "<" beim
	 * Speichern in HTML-Entitäten umwandeln.
	 */
	public function testAufstellungErlaubtHtml(): void
	{
		$this->assertTrue(self::$arrDca['tl_championslists_items']['fields']['nomination']['eval']['allowHtml'] ?? false);

		$arrSpalten = self::$arrDca['tl_championslists_items']['fields']['platzierungen']['eval']['columnFields'];
		$this->assertTrue($arrSpalten['aufstellung']['eval']['allowHtml'] ?? false);
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
