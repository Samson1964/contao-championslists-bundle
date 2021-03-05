<?php
// Tabellen umstrukturieren

/**
 * Runonce Job
 */
class runonceJob extends \Backend
{

	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}

	/**
	 * Run job
	 */
	public function run()
	{
		// Kategorien anlegen und in Tabelle eintragen
		$kategorien = array
		(
			'1' => '2. Platz',
			'2' => '3. Platz',
			'3' => 'Frauen 1. Platz',
			'4' => 'Nestoren 1. Platz',
		);
		foreach($kategorien as $key => $value)
		{
			$set = array
			(
				'id'        => $key,
				'title'     => $value,
				'alias'     => strtolower(str_replace(array(' ', '.'), array('-', ''), $value)),
				'published' => 1,
				'tstamp'    => time()
			);
			$this->Database->prepare("INSERT INTO tl_championslists_categories %s")
			               ->set($set)
			               ->execute();
		}

		// Alle Datensätze laden und modifizieren
		$result = $this->Database->prepare("SELECT * FROM tl_championslists_items")
		                         ->execute();

		if($result->numRows)
		{

			while($result->next())
			{
				$platzierungen = array();

				if($result->name2)
				{
					$platzierungen[] = array
					(
						'platz'           => $result->typ2,
						'name'            => $result->name2,
						'alter'           => $result->age2,
						'verein'          => $result->clubrating2,
						'rating'          => '',
						'image'           => $result->singleSRC2,
						'spielerregister' => $result->spielerregister_id2,
						'aufstellung'     => $result->nomination2,
					);
				}
				if($result->name3)
				{
					$platzierungen[] = array
					(
						'platz'           => $result->typ3,
						'name'            => $result->name3,
						'alter'           => $result->age3,
						'verein'          => $result->clubrating3,
						'rating'          => '',
						'image'           => $result->singleSRC3,
						'spielerregister' => $result->spielerregister_id3,
						'aufstellung'     => $result->nomination3,
					);
				}
				if($result->name4)
				{
					$platzierungen[] = array
					(
						'platz'           => $result->typ4,
						'name'            => $result->name4,
						'alter'           => $result->age4,
						'verein'          => $result->clubrating4,
						'rating'          => '',
						'image'           => $result->singleSRC4,
						'spielerregister' => $result->spielerregister_id4,
						'aufstellung'     => '',
					);
				}
				if($result->name5)
				{
					$platzierungen[] = array
					(
						'platz'           => $result->typ5,
						'name'            => $result->name5,
						'alter'           => $result->age5,
						'verein'          => $result->clubrating5,
						'rating'          => '',
						'image'           => $result->singleSRC5,
						'spielerregister' => $result->spielerregister_id5,
						'aufstellung'     => '',
					);
				}
				if($result->name6)
				{
					$platzierungen[] = array
					(
						'platz'           => $result->typ6,
						'name'            => $result->name6,
						'alter'           => $result->age6,
						'verein'          => $result->clubrating6,
						'rating'          => '',
						'image'           => $result->singleSRC6,
						'spielerregister' => $result->spielerregister_id6,
						'aufstellung'     => '',
					);
				}

				// Datensatz aktualisieren
				$set = array
				(
					'verein'        => $result->clubrating,
					'rating'        => '',
					'platzierungen' => serialize($platzierungen)
				);
				$this->Database->prepare("UPDATE tl_championslists_items %s WHERE id = ?")
				               ->set($set)
				               ->execute($result->id);
			}
		}

	}

}

// Run once
$objRunonceJob = new runonceJob();
$objRunonceJob->run();
