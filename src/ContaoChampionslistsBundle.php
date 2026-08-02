<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Haupt-Bundle-Klasse der Meisterlisten.
 *
 * Bindet das Bundle in den Symfony-/Contao-Kernel ein. Die DependencyInjection-
 * Extension wird über die Symfony-Namenskonvention automatisch gefunden
 * (Bundle-Klassenname ohne "Bundle"-Suffix + "Extension").
 */
class ContaoChampionslistsBundle extends Bundle
{
}
