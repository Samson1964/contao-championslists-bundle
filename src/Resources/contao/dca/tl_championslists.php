<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Schachbulle\ContaoChampionslistsBundle\Classes\Permissions;

/**
 * Tabelle tl_championslists.
 */
$GLOBALS['TL_DCA']['tl_championslists'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ctable'                      => array('tl_championslists_items'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_championslists', 'checkPermission'),
		),
		'oncreate_callback' => array
		(
			array('tl_championslists', 'adjustPermissionsOnCreate'),
		),
		'oncopy_callback' => array
		(
			array('tl_championslists', 'adjustPermissionsOnCopy'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'    => 'primary',
				'title' => 'index',
			),
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => DataContainer::MODE_SORTED,
			'fields'                  => array('title'),
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'panelLayout'             => 'filter;search,limit',
		),
		'label' => array
		(
			'fields'                  => array('title', 'typ'),
			'showColumns'             => true,
		),
		'global_operations' => array
		(
			'kategorien' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['kategorien'],
				'href'                => 'table=tl_championslists_categories',
				'icon'                => 'bundles/contaochampionslists/images/kategorien.png',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="Backend.getScrollOffset()"',
			),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['edit'],
				'href'                => 'table=tl_championslists_items',
				'icon'                => 'edit.svg',
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '').'\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['toggle'],
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
				'showInHeader'        => true,
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg',
			),
		),
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title;{options_legend},typ;{publish_legend},published',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment",
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'long'),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'typ' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 'E',
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_championslists']['typen'],
			'eval'                    => array
			(
				'doNotCopy'           => false,
				'tl_class'            => 'long',
			),
			'sql'                     => "char(1) NOT NULL default ''",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'default'                 => '1',
			'toggle'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default ''",
		),
	),
);

/**
 * Stellt die Callbacks der Tabelle tl_championslists bereit.
 *
 * Die Rechteprüfung folgt dem Muster der Nachrichtenarchive
 * (tl_news_archive::checkPermission() und ::adjustPermissions() in Contao 4.13).
 */
class tl_championslists extends Backend
{
	/**
	 * Beschränkt die Meisterlisten auf die für den Benutzer erlaubten und prüft die laufende Aktion.
	 *
	 * Wirkt in drei Stufen: Die Übersicht zeigt über "list.sorting.root" nur
	 * noch erlaubte Listen. Fehlen die Rechte zum Anlegen oder Löschen, werden
	 * die zugehörigen Schaltflächen entfernt und der Datencontainer entsprechend
	 * gesperrt. Schließlich wird die Aktion aus der Adresse geprüft, damit sich
	 * die Sperre nicht durch einen von Hand eingegebenen Link umgehen lässt.
	 *
	 * Administratoren sind von allem ausgenommen.
	 *
	 * @param DataContainer|null $dc Der Datencontainer; wird hier nicht benötigt,
	 *                               Contao übergibt ihn aber an jeden onload_callback
	 *
	 * @throws AccessDeniedException wenn die Aktion für den Benutzer nicht erlaubt ist
	 */
	public function checkPermission(?DataContainer $dc = null): void
	{
		if (Permissions::isAdmin())
		{
			return;
		}

		$arrRoot = Permissions::getAllowedLists();
		$blnCanCreate = Permissions::canCreate();
		$blnCanDelete = Permissions::canDelete();

		$GLOBALS['TL_DCA']['tl_championslists']['list']['sorting']['root'] = $arrRoot;

		// Anlegen und Kopieren gehören zusammen: Eine Kopie ist eine neue Liste
		if (!$blnCanCreate)
		{
			$GLOBALS['TL_DCA']['tl_championslists']['config']['closed'] = true;
			$GLOBALS['TL_DCA']['tl_championslists']['config']['notCreatable'] = true;
			$GLOBALS['TL_DCA']['tl_championslists']['config']['notCopyable'] = true;
			unset($GLOBALS['TL_DCA']['tl_championslists']['list']['operations']['copy']);
		}

		if (!$blnCanDelete)
		{
			$GLOBALS['TL_DCA']['tl_championslists']['config']['notDeletable'] = true;
			unset($GLOBALS['TL_DCA']['tl_championslists']['list']['operations']['delete']);
		}

		$strAct = (string) Input::get('act');
		$intId = (int) Input::get('id');

		// Einzelne Listen werden über Permissions::isListAllowed() geprüft und
		// nicht gegen $arrRoot: Ohne erlaubte Liste ist $arrRoot der Sperrwert
		// array(0), und eine ID 0 dürfte sonst als "erlaubt" durchgehen.
		switch ($strAct)
		{
			case '':
			case 'select':
				// Die Übersicht ist bereits über "root" eingeschränkt
				break;

			case 'create':
				if (!$blnCanCreate)
				{
					throw new AccessDeniedException('Not enough permissions to create championslists.');
				}
				break;

			case 'copy':
				if (!$blnCanCreate || !Permissions::isListAllowed($intId))
				{
					throw new AccessDeniedException('Not enough permissions to copy championslist ID '.$intId.'.');
				}
				break;

			case 'delete':
				if (!$blnCanDelete || !Permissions::isListAllowed($intId))
				{
					throw new AccessDeniedException('Not enough permissions to delete championslist ID '.$intId.'.');
				}
				break;

			case 'edit':
			case 'show':
			case 'toggle':
				if (!Permissions::isListAllowed($intId))
				{
					throw new AccessDeniedException('Not enough permissions to '.$strAct.' championslist ID '.$intId.'.');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'copyAll':
				// Die in der Mehrfachauswahl angehakten IDs liegen in der Sitzung.
				// Nicht erlaubte werden herausgenommen; fehlt das Recht für die
				// Aktion ganz, bleibt keine übrig.
				$objSession = System::getContainer()->get('request_stack')->getSession();
				$arrSession = $objSession->all();

				if (('deleteAll' === $strAct && !$blnCanDelete) || ('copyAll' === $strAct && !$blnCanCreate))
				{
					$arrSession['CURRENT']['IDS'] = array();
				}
				else
				{
					$arrSession['CURRENT']['IDS'] = array_values(array_intersect(array_map('\intval', (array) ($arrSession['CURRENT']['IDS'] ?? array())), $arrRoot));
				}

				$objSession->replace($arrSession);
				break;

			default:
				throw new AccessDeniedException('Not enough permissions to '.$strAct.' championslists.');
		}
	}

	/**
	 * Schaltet eine neu angelegte Meisterliste für ihren Ersteller frei (oncreate_callback).
	 *
	 * @param string             $strTable    Tabellenname, hier immer tl_championslists
	 * @param int|string         $varInsertId ID der neuen Liste
	 * @param array<mixed>       $arrSet      Die beim Anlegen gesetzten Werte (ungenutzt)
	 * @param DataContainer|null $dc          Der Datencontainer (ungenutzt)
	 */
	public function adjustPermissionsOnCreate($strTable, $varInsertId, $arrSet = array(), ?DataContainer $dc = null): void
	{
		$this->grantNewList((int) $varInsertId);
	}

	/**
	 * Schaltet eine kopierte Meisterliste für den Kopierenden frei (oncopy_callback).
	 *
	 * @param int|string         $varInsertId ID der Kopie
	 * @param DataContainer|null $dc          Der Datencontainer (ungenutzt)
	 */
	public function adjustPermissionsOnCopy($varInsertId, ?DataContainer $dc = null): void
	{
		$this->grantNewList((int) $varInsertId);
	}

	/**
	 * Trägt eine neue Meisterliste bei den erlaubten Listen des Benutzers ein.
	 *
	 * Ohne diesen Schritt stünde der Ersteller direkt nach dem Anlegen vor einer
	 * Zugriffsverweigerung, weil die neue ID noch in keiner Rechteliste steht.
	 * Eingetragen wird dort, wo auch das Recht "create" herkommt: bei den
	 * Gruppen des Benutzers, sofern er von Gruppen erbt, und beim Benutzer
	 * selbst, sofern er eigene Rechte hat. Damit behalten auch die übrigen
	 * Mitglieder einer Gruppe Zugriff auf Listen, die ein Kollege anlegt.
	 *
	 * Es wird nur eingetragen, was Contao in dieser Sitzung selbst als neuen
	 * Datensatz vermerkt hat – eine von Hand übergebene ID lässt sich so nicht
	 * freischalten.
	 *
	 * @param int $intInsertId ID der neuen Meisterliste
	 */
	private function grantNewList(int $intInsertId): void
	{
		if ($intInsertId < 1 || Permissions::isAdmin())
		{
			return;
		}

		$arrRoot = Permissions::getAllowedLists();

		if (\in_array($intInsertId, $arrRoot, true))
		{
			return;
		}

		$arrNew = System::getContainer()->get('request_stack')->getSession()->getBag('contao_backend')->get('new_records');

		if (!\is_array($arrNew['tl_championslists'] ?? null) || !\in_array($intInsertId, array_map('\intval', $arrNew['tl_championslists']), true))
		{
			return;
		}

		$objUser = BackendUser::getInstance();
		$objDatabase = Database::getInstance();

		// Rechte auf Gruppenebene ergänzen
		if ('custom' !== $objUser->inherit)
		{
			$arrGroups = array_filter(array_map('\intval', (array) $objUser->groups));

			if ($arrGroups)
			{
				$objGroup = $objDatabase->execute('SELECT id, championslists, championslistsp FROM tl_user_group WHERE id IN('.implode(',', $arrGroups).')');

				while ($objGroup->next())
				{
					if (Permissions::hasRight($objGroup->championslistsp, 'create'))
					{
						$arrLists = StringUtil::deserialize($objGroup->championslists, true);
						$arrLists[] = $intInsertId;

						$objDatabase->prepare('UPDATE tl_user_group SET championslists=? WHERE id=?')
							->execute(serialize($arrLists), $objGroup->id);
					}
				}
			}
		}

		// Rechte auf Benutzerebene ergänzen
		if ('group' !== $objUser->inherit)
		{
			$objRow = $objDatabase->prepare('SELECT championslists, championslistsp FROM tl_user WHERE id=?')
				->limit(1)
				->execute($objUser->id);

			if ($objRow->numRows && Permissions::hasRight($objRow->championslistsp, 'create'))
			{
				$arrLists = StringUtil::deserialize($objRow->championslists, true);
				$arrLists[] = $intInsertId;

				$objDatabase->prepare('UPDATE tl_user SET championslists=? WHERE id=?')
					->execute(serialize($arrLists), $objUser->id);
			}
		}

		// Die neue Liste auch im laufenden Aufruf bekannt machen
		$arrRoot[] = $intInsertId;
		$objUser->championslists = array_values(array_filter($arrRoot));
	}
}
