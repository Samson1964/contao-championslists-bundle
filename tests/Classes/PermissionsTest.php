<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Tests\Classes;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoChampionslistsBundle\Classes\Permissions;

/**
 * Prüft die datenbankunabhängigen Teile der Rechteprüfung.
 */
class PermissionsTest extends TestCase
{
	/**
	 * Ohne erlaubte Liste muss array(0) herauskommen: Nur diesen Wert versteht
	 * der DC_Table als "nichts anzeigen". Ein leeres Array hieße "keine
	 * Einschränkung" – der Benutzer sähe dann alle Listen.
	 *
	 * @dataProvider leereWerte
	 *
	 * @param mixed $varWert
	 */
	public function testOhneListenKommtDerSperrwert($varWert): void
	{
		$this->assertSame(array(0), Permissions::normalizeIds($varWert));
	}

	/**
	 * @return array<string, array<int, mixed>>
	 */
	public function leereWerte(): array
	{
		return array(
			'null' => array(null),
			'leere Zeichenkette' => array(''),
			'leeres Array' => array(array()),
			'serialisiertes leeres Array' => array(serialize(array())),
			'nur ungültige Einträge' => array(array('', '0', 'abc', -3)),
			'falscher Typ' => array(42),
		);
	}

	public function testIdsWerdenZuEindeutigenGanzzahlen(): void
	{
		$this->assertSame(array(3, 17, 5), Permissions::normalizeIds(array('3', '17', 3, '5', '0', '')));
	}

	/**
	 * Direkt aus der Datenbank kommt das Feld serialisiert, vom Benutzerobjekt
	 * als Array – beides muss dasselbe ergeben.
	 */
	public function testSerialisierterWertWirdEntpackt(): void
	{
		$this->assertSame(array(1, 2), Permissions::normalizeIds(serialize(array('1', '2'))));
	}

	public function testRechtWirdErkannt(): void
	{
		$this->assertTrue(Permissions::hasRight(array('create', 'delete'), 'create'));
		$this->assertTrue(Permissions::hasRight(serialize(array('delete')), 'delete'));
	}

	/**
	 * @dataProvider fehlendeRechte
	 *
	 * @param mixed $varRechte
	 */
	public function testFehlendesRechtWirdVerneint($varRechte): void
	{
		$this->assertFalse(Permissions::hasRight($varRechte, 'create'));
	}

	/**
	 * @return array<string, array<int, mixed>>
	 */
	public function fehlendeRechte(): array
	{
		return array(
			'null' => array(null),
			'leere Zeichenkette' => array(''),
			'leeres Array' => array(array()),
			'nur delete' => array(array('delete')),
			'serialisiert nur delete' => array(serialize(array('delete'))),
			// Kein loser Vergleich: true oder 0 dürfen kein Recht vortäuschen
			'true im Array' => array(array(true)),
			'0 im Array' => array(array(0)),
		);
	}
}
