<?php
namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

class Championslist extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_championslists_einzel';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MEISTERLISTE ###';
			$objTemplate->id = $this->id;
			$objTemplate->title = $this->title;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id; 
			
			return $objTemplate->parse();
		}

		return parent::generate();
	} 
	
	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Adresse aus Datenbank laden, wenn ID übergeben wurde
		if($this->championslist)
		{
			// Voreinstellungen laden
			$picWidthPlayer = $this->championslists_picWidthPlayer ? $this->championslists_picWidthPlayer : $GLOBALS['TL_CONFIG']['championslists_picWidthPlayer'];
			$picHeightPlayer = $this->championslists_picHeightPlayer ? $this->championslists_picHeightPlayer : $GLOBALS['TL_CONFIG']['championslists_picHeightPlayer'];

			// Listentitel laden
			$objListe = $this->Database->prepare("SELECT * FROM tl_championslists WHERE id=?")
			                           ->execute($this->championslist);
			// Liste gefunden
			if($objListe)
			{
				// Template zuweisen
				if($this->championslist_alttemplate) // Alternativ-Template wurde definiert
					$this->Template = new FrontendTemplate($this->championstemplate);
				else // Kein Alternativ-Template, dann Standard-Template nehmen
					($objListe->templatefile) ? $this->Template = new \FrontendTemplate($objListe->templatefile) : $this->Template = new FrontendTemplate($this->strTemplate);
				// Restliche Variablen zuweisen
				$this->Template->id = $this->championslist;
				$this->Template->vorlage = $objListe->templatefile;
				$this->Template->title = $objListe->title;
				// Listeneinträge laden
				if($this->championslist_filter) // Filterung nach Jahren gewünscht
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
					$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid=? AND year >= $von AND year <= $bis ORDER BY year $order")
					                           ->execute($this->championslist);
				}
				else // Keine Filterung
					$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid=? ORDER BY year DESC")
					                           ->execute($this->championslist);
				if($objItems)
				{
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
							$objFile = \FilesModel::findByPk($objItems->singleSRC);
							$item[$i]['image'] = $objFile->path;
							$item[$i]['thumbnail'] = \Image::get($objFile->path, $picWidthPlayer, $picHeightPlayer, 'crop');
						}
						else $item[$i]['image'] = '';
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
									$objFile = \FilesModel::findByPk($objItems->{'singleSRC'.$nr});
									$image = $objFile->path;
									$thumbnail = \Image::get($objFile->path, $picWidthPlayer, $picHeightPlayer, 'crop');
								}
								else 
								{
									$image = '';
									$thumbnail = '';
								}
								$item[$i]['players_index'][] = array
								(
									'typ'        => $objItems->{'typ'.$nr},
									'name'       => $objItems->{'name'.$nr},
									'age'        => $objItems->{'age'.$nr},
									'clubrating' => $objItems->{'clubrating'.$nr},
									'image'      => $image,
									'thumbnail'  => $thumbnail
								);
								// Alternativer Zugriff
								$item[$i]['players_key'][$objItems->{'typ'.$nr}] = array
								(
									'name'       => $objItems->{'name'.$nr},
									'age'        => $objItems->{'age'.$nr},
									'clubrating' => $objItems->{'clubrating'.$nr},
									'image'      => $image,
									'thumbnail'  => $thumbnail
								);
							}
						}
						// Zusätzliche Felder bei Mannschaften ausgeben
						$item[$i]['name2'] = $objItems->name2;
						$item[$i]['nomination2'] = $objItems->nomination2;
						$item[$i]['name3'] = $objItems->name3;
						$item[$i]['nomination3'] = $objItems->nomination3;
						// Bild extrahieren
						if($objItems->singleSRC2)
						{
							$objFile = \FilesModel::findByPk($objItems->singleSRC2);
							$item[$i]['image2'] = $objFile->path;
							$item[$i]['thumbnail2'] = \Image::get($objFile->path, $picWidthPlayer, $picHeightPlayer, 'crop');
						}
						else 
						{
							$item[$i]['image2'] = '';
							$item[$i]['thumbnail2'] = '';
						}
						if($objItems->singleSRC3)
						{
							$objFile = \FilesModel::findByPk($objItems->singleSRC3);
							$item[$i]['image3'] = $objFile->path;
							$item[$i]['thumbnail3'] = \Image::get($objFile->path, $picWidthPlayer, $picHeightPlayer, 'crop');
						}
						else 
						{
							$item[$i]['image3'] = '';
							$item[$i]['thumbnail3'] = '';
						}
						$i++;
					}
					//echo "<pre>";
					//print_r($item);
					//echo "</pre>";
					$this->Template->item = $item;
				}
			}
		}
		return;

	}
}
