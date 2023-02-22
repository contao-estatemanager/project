<?php

declare(strict_types=1);

/*
 * This file is part of Contao EstateManager.
 *
 * @see        https://www.contao-estatemanager.com/
 * @source     https://github.com/contao-estatemanager/project
 * @copyright  Copyright (c) 2021 Oveleon GbR (https://www.oveleon.de)
 * @license    https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

namespace ContaoEstateManager\Project;

use Contao\BackendTemplate;
use Contao\StringUtil;
use ContaoEstateManager\ExposeModule;
use ContaoEstateManager\Translator;
use Patchwork\Utf8;

/**
 * Expose module "project details".
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class ExposeModuleProjectDetails extends ExposeModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'expose_mod_project_details';

    /**
     * Do not display the module if there are no real estates.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['project_details'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=expose_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        $arrDetails = StringUtil::deserialize($this->projectDetails);
        $arrReturn = [];

        if ($arrDetails)
        {
            $arrProjectDetails = Project::getProjectSpecificDetails($this->realEstate);

            if (\in_array('children', $arrDetails, true))
            {
                $childLabel = Translator::translateLabel('project_children');

                $arrProjectDetails = array_merge(
                    $arrProjectDetails,
                    ['children' => [
                        'label' => $childLabel,
                        'details' => [[
                            'label' => $childLabel,
                            'key' => 'children',
                            'value' => Project::getNumberOfChildren($this->realEstate),
                        ]],
                    ]]
                );
            }

            if ($arrProjectDetails)
            {
                // assign and sort
                foreach ($arrDetails as $key)
                {
                    if ($arrProjectDetails[$key])
                    {
                        $arrReturn[$key] = $arrProjectDetails[$key];
                    }
                }

                if ($arrReturn)
                {
                    $this->Template->details = $arrReturn;
                }
            }
        }
    }
}
