<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;

/*
 * Paletten
 *
 * Das Feld "guests" gibt es nur bis Contao 4.13. Deshalb wird es nur ergänzt,
 * wenn der Contao-Kern es bereitstellt (die Kern-DCA wird vor dieser geladen).
 */
$strExpert = isset($GLOBALS['TL_DCA']['tl_content']['fields']['guests']) ? 'guests,cssID' : 'cssID';

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'championslist_filter';
$GLOBALS['TL_DCA']['tl_content']['palettes']['champion'] = '{type_legend},type,headline;{champions_legend},championslist;{sourcesize_legend},size,fullsize;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},'.$strExpert.';{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslists_mono'] = '{type_legend},type,headline;{champions_legend},championslist,championslist_filter;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},'.$strExpert.';{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslists_multi'] = '{type_legend},type,headline;{champions_legend},championslist,championslist_filter;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},'.$strExpert.';{invisible_legend:hide},invisible,start,stop';

unset($strExpert);

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['championslist_filter'] = 'championsfrom,championsto';

/*
 * Felder
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['championslist'] = array
(
	'label'                    => &$GLOBALS['TL_LANG']['tl_content']['championslist'],
	'exclude'                  => true,
	'options_callback'         => array('tl_content_championslist', 'getChampionslists'),
	'inputType'                => 'select',
	'eval'                     => array
	(
		'mandatory'            => false,
		'multiple'             => false,
		'chosen'               => true,
		'submitOnChange'       => true,
		'tl_class'             => 'w50 wizard',
	),
	'wizard'                   => array
	(
		array('tl_content_championslist', 'editListe'),
	),
	'sql'                      => "int(10) unsigned NOT NULL default '0'",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championslist_filter'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championslist_filter'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            => 'clr',
		'isBoolean'           => true,
		'submitOnChange'      => true,
	),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championsfrom'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championsfrom'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array
	(
		'mandatory'           => true,
		'rgxp'                => 'digit',
		'tl_class'            => 'w50',
		'maxlength'           => 4,
	),
	'sql'                     => "varchar(4) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championsto'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championsto'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array
	(
		'mandatory'           => true,
		'rgxp'                => 'digit',
		'tl_class'            => 'w50',
		'maxlength'           => 4,
	),
	'sql'                     => "varchar(4) NOT NULL default ''",
);

/**
 * Stellt die Callbacks der Meisterlisten-Felder in tl_content bereit.
 */
class tl_content_championslist extends Backend
{
	/**
	 * Erzeugt den Bearbeiten-Link neben der Auswahlliste.
	 */
	public function editListe(DataContainer $dc): string
	{
		$intListe = (int) $dc->value;

		if ($intListe < 1)
		{
			return '';
		}

		$strTitle = sprintf($GLOBALS['TL_LANG']['tl_content']['editchampionslist'] ?? '%s', $intListe);

		// Ohne act-Parameter wird kein Request-Token benötigt
		$strHref = System::getContainer()->get('router')->generate('contao_backend', array
		(
			'do'    => 'championslists',
			'table' => 'tl_championslists_items',
			'id'    => $intListe,
			'popup' => '1',
			'nb'    => '1',
		));

		return ' <a href="'.StringUtil::specialchars($strHref).'" title="'.StringUtil::specialchars($strTitle).'"'
			.' onclick="Backend.openModalIframe({\'title\':\''.StringUtil::specialchars(str_replace("'", "\\'", $strTitle)).'\',\'url\':this.href});return false">'
			.Image::getHtml('alias.svg', $strTitle).'</a>';
	}

	/**
	 * Liefert die auswählbaren Meisterlisten passend zum Inhaltselement.
	 *
	 * Für Mannschaftswettbewerbe stehen nur Listen vom Typ M/W zur Verfügung,
	 * für alle anderen Elemente nur Listen vom Typ E/F.
	 *
	 * @return array<int, string>
	 */
	public function getChampionslists(DataContainer $dc): array
	{
		$arrTypen = 'championslists_multi' === $this->getElementType($dc) ? array('M', 'W') : array('E', 'F');

		$objListen = Database::getInstance()
			->prepare('SELECT id, title FROM tl_championslists WHERE typ IN (?, ?) ORDER BY title ASC')
			->execute(...$arrTypen);

		$arrListen = array();

		while ($objListen->next())
		{
			$arrListen[(int) $objListen->id] = (string) $objListen->title;
		}

		return $arrListen;
	}

	/**
	 * Ermittelt den Typ des aktuell bearbeiteten Inhaltselements.
	 */
	private function getElementType(DataContainer $dc): string
	{
		$objContent = Database::getInstance()
			->prepare('SELECT type FROM tl_content WHERE id=?')
			->limit(1)
			->execute((int) $dc->id);

		return $objContent->numRows ? (string) $objContent->type : '';
	}
}
