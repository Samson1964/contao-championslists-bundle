<?php
namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

class Championslist extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_championslists_single';

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
				// Alternativ-Template zuweisen
				if($this->championslist_alttemplate) $this->Template = new \FrontendTemplate($this->championstemplate);

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
						case 'E': // Einzelturnier
							$bildgroesse = unserialize($GLOBALS['TL_CONFIG']['championslists_imageSizePlayer']);
							$bild = $GLOBALS['TL_CONFIG']['championslists_defaultImageMen'];
							break;
						case 'F': // Einzelturnier (weiblich)
							$bildgroesse = unserialize($GLOBALS['TL_CONFIG']['championslists_imageSizePlayer']);
							$bild = $GLOBALS['TL_CONFIG']['championslists_defaultImageWomen'];
							break;
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
						$item[$i]['number'] = $objItems->number;
						$item[$i]['year'] = $objItems->year;
						$item[$i]['place'] = $objItems->place;
						$item[$i]['url'] = $objItems->url;
						$item[$i]['target'] = $objItems->target;
						$item[$i]['name'] = $objItems->name;
						$item[$i]['nomination'] = $objItems->nomination;
						$item[$i]['age'] = $objItems->age;
						$item[$i]['clubrating'] = $objItems->clubrating;

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
						$item[$i]['image']         = $objBild->singleSRC;
						$item[$i]['thumbnail']     = $objBild->src;
						$item[$i]['imageSize']     = $objBild->imgSize;
						$item[$i]['imageTitle']    = $objBild->imageTitle;
						$item[$i]['imageAlt']      = $objBild->alt;
						$item[$i]['imageCaption']  = $objBild->caption;

						// Info ergänzen
						$item[$i]['info'] = $objItems->info;

						// Weitere Spieler?
						$item[$i]['players'] = array();
						for($nr = 2; $nr <= 6; $nr++)
						{
							if($objItems->{'person'.$nr})
							{
								// Bild extrahieren
								if($objItems->{'singleSRC'.$nr})
								{
									// Foto aus der Datenbank
									$objFile = \FilesModel::findByPk($objItems->{'singleSRC'.$nr});
								}
								else
								{
									// Standardfoto
									$objFile = \FilesModel::findByUuid($bild);
								}
								$objBild = new \stdClass();
								\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => $bildgroesse), \Config::get('maxImageWidth'), null, $objFile);

								$item[$i]['players_index'][] = array
								(
									'typ'        => $objItems->{'typ'.$nr},
									'name'       => $objItems->{'name'.$nr},
									'age'        => $objItems->{'age'.$nr},
									'clubrating' => $objItems->{'clubrating'.$nr},
									'image'      => $objBild->singleSRC,
									'thumbnail'  => $objBild->src
								);
								// Alternativer Zugriff
								$item[$i]['players_key'][$objItems->{'typ'.$nr}] = array
								(
									'name'       => $objItems->{'name'.$nr},
									'age'        => $objItems->{'age'.$nr},
									'clubrating' => $objItems->{'clubrating'.$nr},
									'image'      => $objBild->singleSRC,
									'thumbnail'  => $objBild->src
								);
							}
						}
						// Zusätzliche Felder bei Mannschaften ausgeben
						$item[$i]['name2'] = $objItems->name2;
						$item[$i]['nomination2'] = $objItems->nomination2;
						$item[$i]['name3'] = $objItems->name3;
						$item[$i]['nomination3'] = $objItems->nomination3;

						// Bild 2 extrahieren
						if($objItems->singleSRC2)
						{
							// Foto aus der Datenbank
							$objFile = \FilesModel::findByPk($objItems->singleSRC2);
						}
						else
						{
							// Der Datensatz hat kein Bild, Standardbild einbinden
							$objFile = \FilesModel::findByUuid($bild);
						}
						$objBild = new \stdClass();
						\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => $bildgroesse), \Config::get('maxImageWidth'), null, $objFile);
						$item[$i]['image2'] = $objBild->singleSRC;
						$item[$i]['thumbnail2'] = $objBild->src;

						// Bild 3 extrahieren
						if($objItems->singleSRC3)
						{
							// Foto aus der Datenbank
							$objFile = \FilesModel::findByPk($objItems->singleSRC3);
						}
						else
						{
							// Der Datensatz hat kein Bild, Standardbild einbinden
							$objFile = \FilesModel::findByUuid($bild);
						}
						$objBild = new \stdClass();
						\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => $bildgroesse), \Config::get('maxImageWidth'), null, $objFile);
						$item[$i]['image3'] = $objBild->singleSRC;
						$item[$i]['thumbnail3'] = $objBild->src;

						$i++;
					}
					$this->Template->item = $item;
				}
			}
		}
		return;

	}
}
