<?php
namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

class ChampionslistsMulti extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_championslists_multi';

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Adresse aus Datenbank laden, wenn ID übergeben wurde
		if($this->championslist)
		{

			// Listentitel laden
			$objListe = $this->Database->prepare("SELECT * FROM tl_championslists WHERE id=?")
			                           ->execute($this->championslist);

			// Liste gefunden
			if($objListe)
			{

				// Restliche Variablen zuweisen
				$this->Template->id = $this->championslist;
				$this->Template->title = $objListe->title;

				// Listeneinträge laden
				if($this->championslist_filter && $this->championsfrom && $this->championsto) // Filterung nach Jahren gewünscht
				{
					// Sortierung festlegen
					($this->championsfrom < $this->championsto) ? $order = 'ASC' : $order = 'DESC';
					// Von-Bis-Werte festlegen
					if($this->championsfrom < $this->championsto)
					{
						$von = $this->championsfrom;
						$bis = $this->championsto;
					}
					else
					{
						$von = $this->championsto;
						$bis = $this->championsfrom;
					}
					// Abfrage starten
					$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid = ? AND published = ? AND year >= $von AND year <= $bis ORDER BY year $order")
					                           ->execute($this->championslist, 1);
				}
				else // Keine Filterung
					$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid = ? AND published = ? ORDER BY year DESC")
					                           ->execute($this->championslist, 1);

				$item = array();
				
				if($objItems)
				{
					// Standardbilddatei und Standardbildmaße festlegen
					switch($objListe->typ)
					{
						case 'M': // Mannschaftsturnier
							$bildgroesse = unserialize($GLOBALS['TL_CONFIG']['championslists_imageSizeTeam']);
							$bild = $GLOBALS['TL_CONFIG']['championslists_defaultImageTeamsMen'];
							break;
						case 'W': // Mannschaftsturnier (weiblich)
							$bildgroesse = unserialize($GLOBALS['TL_CONFIG']['championslists_imageSizeTeam']);
							$bild = $GLOBALS['TL_CONFIG']['championslists_defaultImageTeamsWomen'];
							break;
						default:
					}

					$i = 0;
					while($objItems->next())
					{
						(bcmod($i,2)) ? $item[$i]['class'] = 'odd' : $item[$i]['class'] = 'even';
						$item[$i]['nummer']                          = $objItems->number;
						$item[$i]['jahr']                            = $objItems->year;
						$item[$i]['ort']                             = $objItems->place;
						$item[$i]['linkurl']                         = $objItems->url;
						$item[$i]['linkziel']                        = $objItems->target;
						$item[$i]['platz']['meister']['name']        = $objItems->name;
						$item[$i]['platz']['meister']['aufstellung'] = $objItems->nomination;

						// Bild extrahieren
						if($objItems->singleSRC)
						{
							// Foto aus der Datenbank
							$objFile = \FilesModel::findByPk($objItems->singleSRC);
						}
						else
						{
							// Standardfoto
							$objFile = \FilesModel::findByUuid($bild);
						}
						$objBild = new \stdClass();
						\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => $bildgroesse), \Config::get('maxImageWidth'), null, $objFile);

						// Bild zuweisen
						$item[$i]['platz']['meister']['image']         = $objBild->singleSRC;
						$item[$i]['platz']['meister']['thumbnail']     = $objBild->src;
						$item[$i]['platz']['meister']['imageSize']     = $objBild->imgSize;
						$item[$i]['platz']['meister']['imageTitle']    = $objBild->imageTitle;
						$item[$i]['platz']['meister']['imageAlt']      = $objBild->alt;
						$item[$i]['platz']['meister']['imageCaption']  = $objBild->caption;

						// Info ergänzen
						$item[$i]['info'] = $objItems->info;

						// Weitere Mannschaften?
						if($objItems->platzierungen)
						{
							$platzierungen = unserialize($objItems->platzierungen);
							foreach($platzierungen as $platz)
							{
								// Bild extrahieren
								if($platz['image'])
								{
									// Foto aus der Datenbank
									$objFile = \FilesModel::findByPk($platz['image']);
								}
								else
								{
									// Standardfoto
									$objFile = \FilesModel::findByUuid($bild);
								}
								$objBild = new \stdClass();
								\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => $bildgroesse), \Config::get('maxImageWidth'), null, $objFile);

								// Person und Bild zuweisen
								$item[$i]['platz'][$kategorien[$platz['platz']]] = array
								(
									'name'         => $platz['name'],
									'aufstellung'  => $platz['aufstellung'],
									'image'        => $objBild->singleSRC,
									'thumbnail'    => $objBild->src,
									'imageSize'    => $objBild->imgSize,
									'imageTitle'   => $objBild->imageTitle,
									'imageAlt'     => $objBild->alt,
									'imageCaption' => $objBild->caption,
								);
							}
						}
						$i++;
					}
					$this->Template->item = $item;
				}
			}
		}
		return;

	}
}
