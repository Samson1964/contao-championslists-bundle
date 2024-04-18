<?php
namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

class ChampionslistsMono extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_championslists_mono';

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;

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
					$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid = ? AND published = ? AND year >= $von AND year <= $bis ORDER BY year $order, number $order")
					                           ->execute($this->championslist, 1);
				}
				else // Keine Filterung
					$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid = ? AND published = ? ORDER BY year DESC, number DESC")
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
						default:
					}

					// Kategorien laden
					$kategorien = \Schachbulle\ContaoChampionslistsBundle\Classes\Helper::getAliase();

					$i = 0;
					while($objItems->next())
					{
						(bcmod($i,2)) ? $class = 'odd' : $class = 'even';
						$class .= $objItems->failed ? ' failed' : '';
						$item[$i]['nummer']                          = $objItems->number;
						$item[$i]['jahr']                            = $objItems->year;
						$item[$i]['class']                           = $class;
						$item[$i]['ort']                             = $objItems->place;
						$item[$i]['linkurl']                         = $objItems->url;
						$item[$i]['linkziel']                        = $objItems->target;
						$item[$i]['platz']['meister']['name']        = $objItems->name;
						$item[$i]['platz']['meister']['aufstellung'] = $objItems->nomination;
						$item[$i]['platz']['meister']['alter']       = $objItems->age;
						$item[$i]['platz']['meister']['verein']      = $objItems->verein;
						$item[$i]['platz']['meister']['rating']      = $objItems->rating;

						// Bild extrahieren
						if($objItems->singleSRC)
						{
							// Foto aus der Datenbank
							$objFile = \FilesModel::findByPk($objItems->singleSRC);
							if(!$objFile)
							{
								// Model findet keine gültige Datei
								log_message('Kein gültiges Bild gefunden auf Seite '.$objPage->alias.': '.print_r($objItems->singleSRC), 'championslists.log');
								// Deshalb Standardfoto verwenden
								$objFile = \FilesModel::findByUuid($bild);
							}
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

						// Weitere Spieler?
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
									if(!$objFile)
									{
										// Model findet keine gültige Datei
										log_message('Kein gültiges Bild gefunden auf Seite '.$objPage->alias.': '.print_r($platz['image']), 'championslists.log');
										// Deshalb Standardfoto verwenden
										$objFile = \FilesModel::findByUuid($bild);
									}
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
									'alter'        => $platz['alter'],
									'verein'       => $platz['verein'],
									'rating'       => $platz['rating'],
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
