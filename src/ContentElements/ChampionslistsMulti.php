<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

use Contao\Config;
use Contao\Database\Result;

/**
 * Inhaltselement "Meisterliste Mannschaftswettbewerb".
 *
 * Gibt die Meistermannschaften einer Liste (Listentyp "M" bzw. "W") samt
 * Aufstellung aus.
 */
class ChampionslistsMulti extends AbstractChampionslists
{
	/**
	 * Template.
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_championslists_multi';

	/**
	 * {@inheritdoc}
	 */
	protected function getImageSizeSetting(): string
	{
		return 'championslists_imageSizeTeam';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultImage(string $strTyp)
	{
		// Mannschaftsturnier (weiblich)
		if ('W' === $strTyp)
		{
			return Config::get('championslists_defaultImageTeamsWomen') ?: Config::get('championslists_defaultImageTeamsMen');
		}

		return Config::get('championslists_defaultImageTeamsMen');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getWinnerFields(Result $objItem): array
	{
		return array
		(
			'name'        => $objItem->name,
			'aufstellung' => $objItem->nomination,
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getPlacementFields(array $arrPlatz): array
	{
		return array
		(
			'name'        => $arrPlatz['name'] ?? '',
			'aufstellung' => $arrPlatz['aufstellung'] ?? '',
		);
	}
}
