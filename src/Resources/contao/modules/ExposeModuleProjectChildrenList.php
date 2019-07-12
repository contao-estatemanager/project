<?php
/**
 * This file is part of Contao EstateManager.
 *
 * @link      https://www.contao-estatemanager.com/
 * @source    https://github.com/contao-estatemanager/project
 * @copyright Copyright (c) 2019  Oveleon GbR (https://www.oveleon.de)
 * @license   https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

namespace ContaoEstateManager\Project;

use ContaoEstateManager\ExposeModule;
use ContaoEstateManager\FilterSession;
use ContaoEstateManager\RealEstateModel;

/**
 * Expose module "project children list".
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class ExposeModuleProjectChildrenList extends ExposeModule
{
    /**
     * Table name
     * @var string
     */
    protected $strTable = 'tl_real_estate';

    /**
     * Filter session object
     * @var FilterSession
     */
    protected $objFilterSession;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'expose_mod_project_children_list';

    /**
     * Do not display the module if there are no real estate children
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['virtual_tour'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=expose_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->objFilterSession = FilterSession::getInstance();

        $strBuffer = parent::generate();

        return ($this->isEmpty) ? '' : $strBuffer;
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->isEmpty = true;

        // skip non project real estates
        if(!$this->realEstate->master)
        {
            return;
        }

        // ToDo: Ohne gruppen kann derzeit nicht auf die Filterung geprüft werden
        list($arrColumns, $arrValues, $arrOptions) = $this->objFilterSession->getParameter(null, null, !!$this->childrenObserveFiltering);

        $arrColumns[] = "$this->strTable.gruppenKennung=?";
        $arrColumns[] = "$this->strTable.master=''";
        $arrValues[]  = $this->realEstate->master;

        $objChildren = RealEstateModel::findBy($arrColumns, $arrValues, $arrOptions);

        if($objChildren !== null)
        {
            $this->isEmpty = false;
            $realEstates = array();

            while($objChildren->next())
            {
                $realEstates[] = $this->parseRealEstate($objChildren->current());
            }

            $this->Template->realEstates = $realEstates;
        }
    }
}