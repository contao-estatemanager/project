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
use Contao\Config;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Pagination;
use Contao\System;
use ContaoEstateManager\FilterSession;
use ContaoEstateManager\ModuleRealEstate;
use ContaoEstateManager\RealEstateModel;
use ContaoEstateManager\RealEstateModulePreparation;
use ContaoEstateManager\Translator;
use Patchwork\Utf8;

/**
 * Front end module "real estate project list".
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class ModuleRealEstateProjectList extends ModuleRealEstate
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $strTable = 'tl_real_estate';

    /**
     * Filter session object.
     *
     * @var FilterSession
     */
    protected $objFilterSession;

    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_realEstateProjectList';

    /**
     * Template.
     *
     * @var string
     */
    protected $strProjectTemplate = 'real_estate_project_default';

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
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['realEstateProjectList'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        $this->objFilterSession = FilterSession::getInstance();

        if ('' !== $this->customTpl)
        {
            $this->strTemplate = $this->customTpl;
        }

        // HOOK: real estate result project list generate
        if (isset($GLOBALS['TL_HOOKS']['generateRealEstateProjectList']) && \is_array($GLOBALS['TL_HOOKS']['generateRealEstateProjectList']))
        {
            foreach ($GLOBALS['TL_HOOKS']['generateRealEstateProjectList'] as $callback)
            {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($this);
            }
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        $this->addSorting();

        [$arrColumns, $arrValues, $arrOptions] = $this->getProjectParameters();

        $cntProjects = RealEstateModel::countPublishedBy($arrColumns, $arrValues, $arrOptions);

        [$limit, $offset] = $this->addPagination($cntProjects);

        $arrOptions['limit'] = $limit;
        $arrOptions['offset'] = $offset;

        $objProjects = RealEstateModel::findPublishedBy($arrColumns, $arrValues, $arrOptions);
        $projectIds = [];
        $arrProjects = [];
        $arrRealEstates = [];

        if (null === $objProjects)
        {
            return;
        }

        // collect project ids
        while ($objProjects->next())
        {
            $projectIds[] = $objProjects->master;
            $arrProjects[$objProjects->master]['children'] = [];
        }

        if (\count($projectIds))
        {
            [$arrColumns, $arrValues, $arrOptions] = $this->objFilterSession->getParameter($this->realEstateGroups, $this->filterMode, (bool) $this->childrenObserveFiltering);

            $arrColumns[] = "$this->strTable.gruppenKennung IN(".implode(',', $projectIds).')';
            $arrColumns[] = "$this->strTable.master=''";

            $objChildren = RealEstateModel::findPublishedBy($arrColumns, $arrValues, $arrOptions);

            // get real number of all children if needed (without filter parameters)
            if ((bool) $this->childrenObserveFiltering)
            {
                $arrNumberOfChildren = [];
                $objNumberOfChildren = $this->Database->execute("SELECT COUNT(id) as cnt, gruppenKennung FROM $this->strTable WHERE gruppenKennung IN(".implode(',', $projectIds).") AND master='' GROUP BY gruppenKennung");

                while ($objNumberOfChildren->next())
                {
                    $arrNumberOfChildren[$objNumberOfChildren->gruppenKennung] = $objNumberOfChildren->cnt;
                }
            }

            // assign parsed children to projects
            if (null !== $objChildren)
            {
                while ($objChildren->next())
                {
                    $arrProjects[$objChildren->gruppenKennung]['children'][] = $this->parseRealEstate($objChildren->current());
                }
            }

            $objProjects->reset();

            if (isset($GLOBALS['CEM_HOOKS']['compileRealEstateProject']) && \is_array($GLOBALS['CEM_HOOKS']['compileRealEstateProject']))
            {
                foreach ($GLOBALS['CEM_HOOKS']['compileRealEstateProject'] as $callback)
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($objProjects, $arrProjects, $this);
                }
            }

            foreach($objProjects as $objProject)
            {
                $realEstate  = new RealEstateModulePreparation($objProject, $this,null);
                $objTemplate = new FrontendTemplate($this->strProjectTemplate);

                $objTemplate->realEstate   = $realEstate;
                $objTemplate->children     = $arrProjects[ $objProject->master ]['children'] ?: array();
                $objTemplate->jumpTo       = $this->jumpToProject;
                $objTemplate->imgSize      = $this->projectImgSize;
                $objTemplate->details      = Project::getProjectSpecificDetails($realEstate);

                $objTemplate->buttonLabel = Translator::translateExpose('button_project');
                $objTemplate->labelChildren = Translator::translateLabel('project_children_label');
                $objTemplate->labelNumberOfChildren = Translator::translateLabel('anzahl_wohneinheiten');

                if ($realEstate->anzahlWohneinheiten)
                {
                    $objTemplate->numberOfChildren = $realEstate->formatter->formatValue('anzahlWohneinheiten');
                }
                elseif ((bool) $this->childrenObserveFiltering)
                {
                    $objTemplate->numberOfChildren = $arrNumberOfChildren[ $objProject->master ];
                }
                else
                {
                    $objTemplate->numberOfChildren = count($arrProjects[ $objProject->master ]['children']);
                }

                // add provider
                $objTemplate->addProvider = (bool) $this->addProvider;

                if ($this->addProvider)
                {
                    $objTemplate->provider = $this->parseProvider($realEstate);
                }

                // add contact person
                $objTemplate->addContactPerson = (bool) $this->addContactPerson;

                if ($this->addContactPerson)
                {
                    $objTemplate->contactPerson = $this->parseContactPerson($realEstate);
                }

                if (isset($GLOBALS['TL_HOOKS']['parseRealEstateProject']) && \is_array($GLOBALS['TL_HOOKS']['parseRealEstateProject']))
                {
                    foreach ($GLOBALS['TL_HOOKS']['parseRealEstateProject'] as $callback)
                    {
                        $this->import($callback[0]);
                        $this->{$callback[0]}->{$callback[1]}($objTemplate, $realEstate, $this);
                    }
                }

                $arrRealEstates[] = $objTemplate->parse();
            }
        }

        System::loadLanguageFile('tl_real_estate_misc');

        $this->Template->empty = $GLOBALS['TL_LANG']['tl_real_estate_misc']['noProjectResults'] ?? '';
        $this->Template->realEstates = $arrRealEstates;
    }

    /**
     * Return project filter parameters.
     *
     * @param $total
     */
    protected function addPagination($total): array
    {
        $limit = null;
        $offset = 0;

        // Maximum number of items
        if ($this->numberOfItems > 0)
        {
            $limit = $this->numberOfItems;
        }

        if (0 === $total)
        {
            $this->Template->addSorting = $this->addSorting = false;
        }

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
        {
            // Adjust the overall limit
            if (isset($limit))
            {
                $total = min($limit, $total);
            }

            // Get the current page
            $id = 'page_n'.$this->id;
            $page = Input::get($id) ?? 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1))
            {
                throw new PageNotFoundException('Page not found: '.Environment::get('uri'));
            }

            // Set limit and offset
            $limit = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;
            $skip = 0;

            // Overall limit
            if ($offset + $limit > $total + $skip)
            {
                $limit = $total + $skip - $offset;
            }

            // Add the pagination menu
            $objPagination = new Pagination($total, $this->perPage, Config::get('maxPaginationLinks'), $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        return [$limit, $offset];
    }

    /**
     * Return project filter parameters.
     */
    protected function getProjectParameters(): array
    {
        $t = $this->strTable;

        [$arrColumns, $arrValues, $arrOptions] = $this->objFilterSession->getParameter(null, $this->filterMode, false);

        $arrColumns[] = "$t.master!=''";
        $arrColumns[] = "$t.gruppenKennung!=''";

        if ($_SESSION['FILTER_DATA']['price_from'] ?? null)
        {
            $arrColumn[] = "$t.project_price_from>=?";
            $arrValues[] = $_SESSION['FILTER_DATA']['price_from'];
        }

        if ($_SESSION['FILTER_DATA']['price_to'] ?? null)
        {
            $arrColumn[] = "$t.project_price_to<=?";
            $arrValues[] = $_SESSION['FILTER_DATA']['price_to'];
        }

        if ($_SESSION['FILTER_DATA']['area_from'] ?? null)
        {
            $arrColumn[] = "$t.project_area_from>=?";
            $arrValues[] = $_SESSION['FILTER_DATA']['area_from'];
        }

        if ($_SESSION['FILTER_DATA']['area_to'] ?? null)
        {
            $arrColumn[] = "$t.project_area_to<=?";
            $arrValues[] = $_SESSION['FILTER_DATA']['area_to'];
        }

        if ($_SESSION['FILTER_DATA']['room_from'] ?? null)
        {
            $arrColumn[] = "$t.project_room_from>=?";
            $arrValues[] = $_SESSION['FILTER_DATA']['room_from'];
        }

        if ($_SESSION['FILTER_DATA']['room_to'] ?? null)
        {
            $arrColumn[] = "$t.project_room_to<=?";
            $arrValues[] = $_SESSION['FILTER_DATA']['room_to'];
        }

        return [$arrColumns, $arrValues, $arrOptions];
    }
}
