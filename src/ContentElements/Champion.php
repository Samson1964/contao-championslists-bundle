<?php
namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

class Champion extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_champion_default';

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
				// Alternativ-Template zuweisen
				if($this->championslist_alttemplate) $this->Template = new \FrontendTemplate($this->championstemplate);

				// Restliche Variablen zuweisen
				$this->Template->id = $this->championslist;
				$this->Template->title = $objListe->title;

				// Listeneinträge laden
				$objItems = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid = ? AND published = ? AND name <> ? ORDER BY year DESC")
				                           ->limit(1)
				                           ->execute($this->championslist, 1, '');

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

					// Bild extrahieren
					if($objItems->singleSRC)
					{
						// Foto aus der Datenbank
						$objFile = \FilesModel::findByPk($objItems->singleSRC);
						if(!$objFile)
						{
							// Model findet keine gültige Datei
							log_message('Kein gültiges Bild gefunden auf Seite '.$objPage->alias.': '.print_r($objItems->singleSRC, true), 'championslists.log');
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

					// Datensatz zuweisen
					$item = array
					(
						'number'        => $objItems->number,
						'year'          => $objItems->year,
						'place'         => $objItems->place,
						'url'           => $objItems->url,
						'target'        => $objItems->target,
						'name'          => $objItems->name,
						'nomination'    => $objItems->nomination,
						'age'           => $objItems->age,
						'clubrating'    => $objItems->clubrating,
						'image'         => $objBild->singleSRC,
						'thumbnail'     => $objBild->src,
						'imageSize'     => $objBild->imgSize,
						'imageTitle'    => $objBild->imageTitle,
						'imageAlt'      => $objBild->alt,
						'imageCaption'  => $objBild->caption,
						'info'          => $objItems->info,
					);

					$this->Template->item = $item;
				}
			}
		}
		return;

	}
}
