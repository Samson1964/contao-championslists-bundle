<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Schachbulle\ContaoChampionslistsBundle\ContentElements\Champion;
use Schachbulle\ContaoChampionslistsBundle\ContentElements\ChampionslistsMono;
use Schachbulle\ContaoChampionslistsBundle\ContentElements\ChampionslistsMulti;

/*
 * -------------------------------------------------------------------------
 * BACKEND-MODULE
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_MOD']['content']['championslists'] = array
(
	'tables' => array('tl_championslists', 'tl_championslists_categories', 'tl_championslists_items'),
	'icon'   => 'bundles/contaochampionslists/images/icon.png',
);

/*
 * -------------------------------------------------------------------------
 * INHALTSELEMENTE
 * -------------------------------------------------------------------------
 *
 * Der Inserttag {{meister::...}} wird über den Service
 * Schachbulle\ContaoChampionslistsBundle\EventListener\InsertTagsListener
 * registriert (siehe src/Resources/config/services.yaml).
 */
$GLOBALS['TL_CTE']['schach']['championslists_mono'] = ChampionslistsMono::class;
$GLOBALS['TL_CTE']['schach']['championslists_multi'] = ChampionslistsMulti::class;
$GLOBALS['TL_CTE']['schach']['champion'] = Champion::class;
