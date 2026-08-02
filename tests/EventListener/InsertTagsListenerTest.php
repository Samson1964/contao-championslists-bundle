<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoChampionslistsBundle\EventListener\InsertTagsListener;

/**
 * Prüft den Inserttag {{meister::...}}.
 *
 * Getestet werden die Fälle, die ohne Datenbankzugriff auskommen. Der Zugriff
 * auf einen konkreten Meister wird im Integrationstest gegen die
 * Contao-Referenzinstallation geprüft.
 */
class InsertTagsListenerTest extends TestCase
{
	/**
	 * @dataProvider fremdeTags
	 */
	public function testFremdeTagsWerdenNichtBeansprucht(string $strTag): void
	{
		$this->assertFalse((new InsertTagsListener())->onReplaceInsertTags($strTag));
	}

	/**
	 * @return array<int, array<int, string>>
	 */
	public function fremdeTags(): array
	{
		return array(
			array('env::host'),
			array('link_url::12'),
			array('meisterschaft::1'),
			// Zuständiger Tag, aber ohne Parameter
			array('meister'),
			array('cache_meister'),
		);
	}

	/**
	 * @dataProvider ungueltigeIds
	 */
	public function testUngueltigeListenIdLiefertLeerenString(string $strTag): void
	{
		$this->assertSame('', (new InsertTagsListener())->onReplaceInsertTags($strTag));
	}

	/**
	 * @return array<int, array<int, string>>
	 */
	public function ungueltigeIds(): array
	{
		return array(
			array('meister::0'),
			array('meister::'),
			array('meister::abc'),
			array('cache_meister::-1'),
			array('meister::0|name'),
		);
	}
}
