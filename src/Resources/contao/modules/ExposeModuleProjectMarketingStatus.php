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
use ContaoEstateManager\Translator;

/**
 * Expose module "project maketing status".
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class ExposeModuleProjectMarketingStatus extends ExposeModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'expose_mod_project_marketing_status';

    /**
     * Do not display the module if there are no real estates
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

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $intPercent = Project::getProjectMarketingStatus($this->realEstate->master);
        $this->Template->marketingStatus = sprintf(Translator::translateExpose('project_marketing_status'), $intPercent);
    }
}