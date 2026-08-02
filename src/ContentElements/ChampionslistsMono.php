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
 * Inhaltselement "Meisterliste Einzelwettbewerb".
 *
 * Gibt die Meister einer Einzelwettbewerbs-Liste (Listentyp "E" bzw. "F") samt
 * Alter, Verein und Wertungszahl aus.
 */
class ChampionslistsMono extends AbstractChampionslists
{
	/**
	 * Template.
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_championslists_mono';

	/**
	 * {@inheritdoc}
	 */
	protected function getImageSizeSetting(): string
	{
		return 'championslists_imageSizePlayer';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultImage(string $strTyp)
	{
		// Einzelturnier (weiblich)
		if ('F' === $strTyp)
		{
			return Config::get('championslists_defaultImageWomen') ?: Config::get('championslists_defaultImageMen');
		}

		return Config::get('championslists_defaultImageMen');
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
			'alter'       => $objItem->age,
			'verein'      => $objItem->verein,
			'rating'      => $objItem->rating,
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
			'alter'       => $arrPlatz['alter'] ?? '',
			'verein'      => $arrPlatz['verein'] ?? '',
			'rating'      => $arrPlatz['rating'] ?? '',
		);
	}
}
