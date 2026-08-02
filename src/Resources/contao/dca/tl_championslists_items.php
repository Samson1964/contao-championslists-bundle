<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\FilesModel;
use Contao\StringUtil;
use Schachbulle\ContaoChampionslistsBundle\Classes\Helper;

/**
 * Tabelle tl_championslists_items.
 */
$GLOBALS['TL_DCA']['tl_championslists_items'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ptable'                      => 'tl_championslists',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_championslists_items', 'checkPalette'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'  => 'primary',
				'pid' => 'index',
			),
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => DataContainer::MODE_PARENT,
			'disableGrouping'         => true,
			'fields'                  => array('year DESC', 'number ASC'),
			'headerFields'            => array('title', 'typ'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_championslists_items', 'listPersons'),
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="Backend.getScrollOffset()" accesskey="e"',
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg',
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '').'\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['toggle'],
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
				'showInHeader'        => true,
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg',
			),
		),
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{recording_legend},recording;{place_legend},year,failed,number,place,numberParticipants,url,target;{info_legend},info;{person1_legend},name,age,verein,rating,cowinner,singleSRC,spielerregister_id;{platzierungen_legend:hide},platzierungen;{publish_legend},published',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment",
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_championslists.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager'),
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		// Erfassungsstand/Vollständigkeit der Turnierseite
		'recording' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['recording'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkboxWizard',
			'options'                 => &$GLOBALS['TL_LANG']['tl_championslists_items']['recording_options'],
			'eval'                    => array('tl_class'=>'w50', 'multiple'=>true),
			'sql'                     => "blob NULL",
		),
		'year' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['year'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => DataContainer::SORT_DESC,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'alnum', 'tl_class'=>'w50', 'maxlength'=>10),
			'sql'                     => "varchar(10) NOT NULL default ''",
		),
		'failed' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['failed'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12', 'isBoolean'=>true),
			'sql'                     => "char(1) NOT NULL default ''",
		),
		'number' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['number'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'clr w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''",
		),
		'place' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['place'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'numberParticipants' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['numberParticipants'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => DataContainer::SORT_DESC,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'rgxp'=>'alnum', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''",
		),
		'url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['url'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'rgxp'                => 'url',
				'decodeEntities'      => true,
				'maxlength'           => 255,
				'dcaPicker'           => true,
				'addWizardClass'      => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'target' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['target'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12', 'isBoolean'=>true),
			'sql'                     => "char(1) NOT NULL default ''",
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'age' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''",
		),
		'verein' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['verein'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>40, 'tl_class'=>'w50'),
			'sql'                     => "varchar(40) NOT NULL default ''",
		),
		'rating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['rating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>40, 'tl_class'=>'w50'),
			'sql'                     => "varchar(40) NOT NULL default ''",
		),
		'cowinner' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['cowinner'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12', 'isBoolean'=>true),
			'sql'                     => "char(1) NOT NULL default ''",
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array
			(
				'filesOnly'           => true,
				'fieldType'           => 'radio',
				'tl_class'            => 'clr',
				'extensions'          => 'jpg,jpeg,png,gif,webp',
			),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('tl_championslists_items', 'getSpielerregister'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'includeBlankOption'  => true,
				'tl_class'            => 'long',
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'platzierungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'tl_class'            => 'long',
				'buttonPos'           => 'top',
				'columnFields'        => array
				(
					'platz' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_platz'],
						'exclude'                 => true,
						'inputType'               => 'select',
						'foreignKey'              => 'tl_championslists_categories.title',
						'eval'                    => array
						(
							'includeBlankOption'  => true,
							'columnPos'           => 'spalte1',
							'valign'              => 'top',
							'style'               => 'width:180px',
						),
					),
					'name' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_name'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte2',
							'valign'              => 'top',
							'style'               => 'width:350px',
						),
					),
					'verein' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_verein'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte3',
							'valign'              => 'top',
							'style'               => 'width:250px',
						),
					),
					'alter' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_alter'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte3',
							'valign'              => 'top',
							'style'               => 'width:100px',
						),
					),
					'rating' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_rating'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte3',
							'valign'              => 'top',
							'style'               => 'width:100px',
						),
					),
					'image' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_image'],
						'exclude'                 => true,
						'inputType'               => 'fileTree',
						'eval'                    => array
						(
							'filesOnly'           => true,
							'fieldType'           => 'radio',
							'extensions'          => 'jpg,jpeg,png,gif,webp',
							'columnPos'           => 'spalte4',
							'valign'              => 'top',
							'style'               => 'width:250px',
						),
					),
					'spielerregister' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_spielerregister'],
						'exclude'                 => true,
						'options_callback'        => array('tl_championslists_items', 'getSpielerregister'),
						'inputType'               => 'select',
						'eval'                    => array
						(
							'mandatory'           => false,
							'multiple'            => false,
							'chosen'              => true,
							'submitOnChange'      => false,
							'includeBlankOption'  => true,
							'columnPos'           => 'spalte2',
							'valign'              => 'top',
							'style'               => 'width:350px',
						),
					),
					'aufstellung' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_aufstellung'],
						'exclude'                 => true,
						'search'                  => true,
						'inputType'               => 'textarea',
						'eval'                    => array
						(
							'class'               => 'noresize',
							'columnPos'           => 'spalte2',
							'style'               => 'width:350px',
						),
						'explanation'             => 'insertTags',
					),
				),
			),
			'sql'                     => "blob NULL",
		),
		'nomination' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['nomination'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'class'               => 'clr noresize',
				'helpwizard'          => true,
			),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL",
		),
		'info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['info'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('rte'=>'tinyMCE', 'helpwizard'=>true),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '1',
			'toggle'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50', 'isBoolean'=>true),
			'sql'                     => "char(1) NOT NULL default '1'",
		),
	),
);

/**
 * Stellt die Callbacks der Tabelle tl_championslists_items bereit.
 */
class tl_championslists_items extends Backend
{
	/**
	 * Typ der aktuell bearbeiteten Meisterliste (E, F, M oder W).
	 *
	 * @var string
	 */
	private static $strListType = '';

	/**
	 * Passt die Palette an den Typ der übergeordneten Meisterliste an.
	 */
	public function checkPalette(DataContainer $dc): void
	{
		self::$strListType = $this->getListType($dc);

		if ('M' !== self::$strListType && 'W' !== self::$strListType)
		{
			return;
		}

		// Mannschaftsturnier männlich/weiblich: Angaben zur Person entfernen und
		// stattdessen die Aufstellung anbieten
		PaletteManipulator::create()
			->removeField('age', 'person1_legend')
			->removeField('verein', 'person1_legend')
			->removeField('rating', 'person1_legend')
			->removeField('cowinner', 'person1_legend')
			->removeField('spielerregister_id', 'person1_legend')
			->addField('nomination', 'singleSRC', PaletteManipulator::POSITION_AFTER)
			->applyToPalette('default', 'tl_championslists_items');

		// Sprachvariablen an Mannschaften anpassen. Der DcaLoader lädt keine
		// Sprachdateien, deshalb wird der Schlüssel vorher geprüft.
		if (isset($GLOBALS['TL_LANG']['tl_championslists_items']['team_name']))
		{
			$GLOBALS['TL_LANG']['tl_championslists_items']['name'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name'];
		}
	}

	/**
	 * Erzeugt die Zeile eines Datensatzes in der Listenansicht.
	 *
	 * @param array<string, mixed> $arrRow
	 */
	public function listPersons(array $arrRow): string
	{
		$blnTeam = 'M' === self::$strListType || 'W' === self::$strListType;
		$strIcon = $blnTeam ? 'team-icon' : 'user-icon';

		return '<div class="tl_content_right">'.$this->renderRecordingState($arrRow).'</div>'
			.'<div class="tl_content_left"'.$this->renderFailedAttributes($arrRow).'>'
			.$this->renderHeadline($arrRow)
			.$this->renderPlacements($arrRow, $strIcon, !$blnTeam)
			.'</div>';
	}

	/**
	 * Liefert die Einträge des Spielerregisters, sofern das Bundle
	 * schachbulle/contao-spielerregister-bundle installiert ist.
	 *
	 * @return array<int, string>
	 */
	public function getSpielerregister(): array
	{
		$strHelper = 'Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper';

		if (!class_exists($strHelper))
		{
			return array();
		}

		return (array) $strHelper::getRegister();
	}

	/**
	 * Ermittelt den Typ der übergeordneten Meisterliste.
	 *
	 * In der Listenansicht liefert $dc->currentPid die ID der Meisterliste. Ist
	 * sie nicht gesetzt, wird sie über den bearbeiteten Datensatz ermittelt.
	 */
	private function getListType(DataContainer $dc): string
	{
		$objDatabase = Database::getInstance();
		$intPid = (int) $dc->currentPid;

		if ($intPid < 1 && $dc->id)
		{
			$objItem = $objDatabase
				->prepare('SELECT pid FROM tl_championslists_items WHERE id=?')
				->limit(1)
				->execute((int) $dc->id);

			$intPid = $objItem->numRows ? (int) $objItem->pid : 0;
		}

		if ($intPid < 1)
		{
			return '';
		}

		$objListe = $objDatabase
			->prepare('SELECT typ FROM tl_championslists WHERE id=?')
			->limit(1)
			->execute($intPid);

		return $objListe->numRows ? (string) $objListe->typ : '';
	}

	/**
	 * Zeigt Teilnehmerzahl und Erfassungsstand als Balkengrafik an.
	 *
	 * @param array<string, mixed> $arrRow
	 */
	private function renderRecordingState(array $arrRow): string
	{
		$strTeilnehmer = $arrRow['numberParticipants'] ? '<b>'.StringUtil::specialchars((string) $arrRow['numberParticipants']).'</b>' : '?';
		$strReturn = '<span title="Anzahl Teilnehmer">('.$strTeilnehmer.')</span>&nbsp;';

		$arrErfassung = StringUtil::deserialize($arrRow['recording'] ?? null, true);
		$arrOptionen = $GLOBALS['TL_LANG']['tl_championslists_items']['recording_options'] ?? array();

		$arrVorhanden = array();
		$strGruen = '';
		$strRot = '';

		foreach ($arrOptionen as $strKey => $strLabel)
		{
			if (\in_array((string) $strKey, array_map('strval', $arrErfassung), true))
			{
				$arrVorhanden[] = $strLabel;
				$strGruen .= '<img src="bundles/contaochampionslists/images/bar_green.png" alt="">';
			}
			else
			{
				$strRot .= '<img src="bundles/contaochampionslists/images/bar_grow.png" alt="">';
			}
		}

		$strTitle = $arrVorhanden ? 'Vorhanden: '.implode(', ', $arrVorhanden) : 'Keine Erfassungen';

		return $strReturn.'<span title="'.StringUtil::specialchars($strTitle).'">'.$strGruen.$strRot.'</span>&nbsp;';
	}

	/**
	 * Liefert die HTML-Attribute für ausgefallene Veranstaltungen.
	 *
	 * @param array<string, mixed> $arrRow
	 */
	private function renderFailedAttributes(array $arrRow): string
	{
		if (!$arrRow['failed'])
		{
			return '';
		}

		return ' style="background-color:#FFD2D2;" title="Veranstaltung ist ausgefallen"';
	}

	/**
	 * Erzeugt Jahr, Nummer, Ort und Namen des Siegers.
	 *
	 * @param array<string, mixed> $arrRow
	 */
	private function renderHeadline(array $arrRow): string
	{
		$strReturn = '<b>'.StringUtil::specialchars((string) $arrRow['year']).'</b> ';

		$strReturn .= $arrRow['url']
			? '<img src="bundles/contaochampionslists/images/link-add-icon.png" alt="" title="Link zur Detailseite vorhanden"> '
			: '<img src="bundles/contaochampionslists/images/link-delete-icon.png" alt="" title="Link zur Detailseite nicht vorhanden"> ';

		if ($arrRow['number'])
		{
			$strReturn .= '['.StringUtil::specialchars((string) $arrRow['number']).'] ';
		}

		if ($arrRow['place'])
		{
			$strReturn .= StringUtil::specialchars((string) $arrRow['place']).' - ';
		}

		if ($arrRow['name'])
		{
			$strReturn .= '1. <b style="color:#007500">'.StringUtil::specialchars((string) $arrRow['name']).'</b> ';
		}

		return $strReturn;
	}

	/**
	 * Erzeugt die Symbole des Siegers sowie die weiteren Platzierungen.
	 *
	 * @param array<string, mixed> $arrRow
	 * @param string               $strIcon          Basisname des Symbols (user-icon oder team-icon)
	 * @param bool                 $blnShowRegister  Verknüpfung zum Spielerregister anzeigen
	 */
	private function renderPlacements(array $arrRow, string $strIcon, bool $blnShowRegister): string
	{
		$strReturn = $this->renderImageIcon($arrRow['singleSRC'] ?? null, $strIcon);

		if ($blnShowRegister && ($arrRow['spielerregister_id'] ?? null))
		{
			$strReturn .= '<img src="bundles/contaochampionslists/images/register-icon.png" alt="" title="mit Spielerregister verknüpft">';
		}

		$arrKategorien = Helper::getTitles();

		foreach (StringUtil::deserialize($arrRow['platzierungen'] ?? null, true) as $arrPlatz)
		{
			if (!\is_array($arrPlatz))
			{
				continue;
			}

			$intKategorie = (int) ($arrPlatz['platz'] ?? 0);

			if (!isset($arrKategorien[$intKategorie]))
			{
				continue;
			}

			$strReturn .= ' | <b>'.StringUtil::specialchars((string) ($arrPlatz['name'] ?? '')).'</b>'
				.' (<i>'.StringUtil::specialchars($arrKategorien[$intKategorie]).'</i>) '
				.$this->renderImageIcon($arrPlatz['image'] ?? null, $strIcon);

			if ($blnShowRegister && ($arrPlatz['spielerregister'] ?? null))
			{
				$strReturn .= '<img src="bundles/contaochampionslists/images/register-icon.png" alt="" title="mit Spielerregister verknüpft">';
			}
		}

		return $strReturn;
	}

	/**
	 * Zeigt an, ob ein hinterlegtes Bild noch existiert.
	 *
	 * @param mixed  $varUuid
	 * @param string $strIcon Basisname des Symbols (user-icon oder team-icon)
	 */
	private function renderImageIcon($varUuid, string $strIcon): string
	{
		if (!$varUuid)
		{
			return '';
		}

		if (null === FilesModel::findByPk($varUuid))
		{
			return '<img src="bundles/contaochampionslists/images/'.$strIcon.'_rot.png" alt="" title="Bild angegeben, aber nicht mehr vorhanden">';
		}

		return '<img src="bundles/contaochampionslists/images/'.$strIcon.'.png" alt="" title="Bild vorhanden">';
	}
}
