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

/**
 * Expose module "project completion status".
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class ExposeModuleProjectCompletionStatus extends ExposeModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'expose_mod_project_completion_status';

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
            $objTemplate->wildcard = '### '.mb_strtoupper($GLOBALS['TL_LANG']['FMD']['project_completion_status'][0], 'UTF-8').' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=expose_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        $strBuffer = parent::generate();

        return $this->isEmpty ? '' : $strBuffer;
    }

    /**
     * Generate the module.
     */
    protected function compile()
    {
        $currStatus = $this->realEstate->completionStatus;

        if (!$currStatus)
        {
            $this->isEmpty = true;

            return false;
        }

        $arrStatus = StringUtil::deserialize($this->completionStatus, true);
        $arrReturn = [];

        foreach ($arrStatus as $status)
        {
            $arrReturn[] = [
                'label' => Translator::translate($status, 'tl_real_estate_project_misc'),
                'active' => $currStatus >= $status,
                'class' => 'status-'.$status,
            ];
        }

        $this->Template->completionStatus = $arrReturn;
    }
}
