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

use ContaoEstateManager\RealEstateModel;
use ContaoEstateManager\Translator;

/**
 * Class Project.
 *
 * @author  Daniele Sciannimanica <daniele@oveleon.de>
 */
class Project
{
    /**
     * Table.
     *
     * @var string
     */
    private static $strTable = 'tl_real_estate';

    /**
     * Set project filter parameters.
     *
     * @param $arrColumns
     * @param $arrValues
     * @param $arrOptions
     * @param $mode
     * @param $addFragments
     * @param $context
     */
    public function setFilterParameter(&$arrColumns, &$arrValues, &$arrOptions, $mode, $addFragments, $context): void
    {
        $t = static::$strTable;

        // Excluding projects in default lists
        if ($addFragments)
        {
            $arrColumns[] = "$t.master=''";
        }
    }

    /**
     * Set mode param for google maps showing projects.
     *
     * @param $template
     * @param $mapConfig
     * @param $context
     */
    public function compileGoogleMapConfig(&$template, &$mapConfig, $context): void
    {
        if ($context->showProjects)
        {
            $mapConfig['source']['param']['filter'] = false;
            $mapConfig['source']['param']['mode'] = 'project';
        }
    }

    /**
     * Set filter parameter for projects by mode.
     *
     * @param $arrColumns
     * @param $arrValues
     * @param $arrOptions
     * @param $currParam
     * @param $context
     */
    public function setEstatesControllerParameter(&$arrColumns, &$arrValues, &$arrOptions, $currParam, $context): void
    {
        $t = static::$strTable;

        if ('project' === $currParam['mode'])
        {
            foreach ($arrColumns as $key => $column)
            {
                if (strpos($column, "master=''"))
                {
                    unset($arrColumns[$key]);
                }
            }

            $arrColumns[] = "$t.master!=''";
        }
    }

    /**
     * Returns project marketing status.
     *
     * @param $objMaster
     *
     * @return int: marketing status in percent
     */
    public static function getProjectMarketingStatus($objMaster)
    {
        $t = static::$strTable;

        if ((bool) $objMaster->referenz)
        {
            return 100;
        }

        $arrColumns = [
            "$t.published='1'",
            "$t.master=''",
            "$t.gruppenKennung=?",
        ];

        $arrValues = [$objMaster->master];

        $objChildren = RealEstateModel::findPublishedBy($arrColumns, $arrValues, []);

        if (null === $objChildren)
        {
            return 0;
        }

        $cntMarketed = 0;
        $cntChildren = $objChildren->count();

        while ($objChildren->next())
        {
            if ((bool) $objChildren->referenz || (bool) $objChildren->vermietet || 'reserviert' === strtolower($objChildren->verkaufstatus) || 'verkauft' === strtolower($objChildren->verkaufstatus))
            {
                ++$cntMarketed;
            }
        }

        return round(100 / $cntChildren * $cntMarketed);
    }

    /**
     * Returns project main details as formatted collection.
     *
     * @param $realEstate
     */
    public static function getProjectSpecificDetails($realEstate): array
    {
        $details = [];

        if ($realEstate->project_price_from && $realEstate->project_price_to)
        {
            $details['price'] = [
                'label' => Translator::translateLabel('project_price_label'),
                'details' => $realEstate->getFields([
                    'project_price_from',
                    'project_price_to',
                ]),
            ];
        }
        elseif ($realEstate->project_price_from)
        {
            $details['price'] = [
                'label' => Translator::translateLabel('project_price_label'),
                'details' => $realEstate->getFields([
                    'project_price_from',
                ]),
            ];
        }
        elseif ($realEstate->project_price_to)
        {
            $details['price'] = [
                'label' => Translator::translateLabel('project_price_label'),
                'details' => $realEstate->getFields([
                    'project_price_to',
                ]),
            ];
        }

        if ($realEstate->project_area_from && $realEstate->project_area_to)
        {
            $details['area'] = [
                'label' => Translator::translateLabel('project_area_label'),
                'details' => $realEstate->getFields([
                    'project_area_from',
                    'project_area_to',
                ]),
            ];
        }
        elseif ($realEstate->project_area_from)
        {
            $details['area'] = [
                'label' => Translator::translateLabel('project_area_label'),
                'details' => $realEstate->getFields([
                    'project_area_from',
                ]),
            ];
        }
        elseif ($realEstate->project_area_to)
        {
            $details['area'] = [
                'label' => Translator::translateLabel('project_area_label'),
                'details' => $realEstate->getFields([
                    'project_area_to',
                ]),
            ];
        }

        if ($realEstate->project_room_from && $realEstate->project_room_to)
        {
            $details['room'] = [
                'label' => Translator::translateLabel('project_room_label'),
                'details' => $realEstate->getFields([
                    'project_room_from',
                    'project_room_to',
                ]),
            ];
        }
        elseif ($realEstate->project_room_from)
        {
            $details['room'] = [
                'label' => Translator::translateLabel('project_room_label'),
                'details' => $realEstate->getFields([
                    'project_room_from',
                ]),
            ];
        }
        elseif ($realEstate->project_room_to)
        {
            $details['room'] = [
                'label' => Translator::translateLabel('project_room_label'),
                'details' => $realEstate->getFields([
                    'project_room_to',
                ]),
            ];
        }

        return $details;
    }

    /**
     * Returns number of children from master object or child object.
     *
     * @param $realEstate
     */
    public static function getNumberOfChildren($realEstate)
    {
        // If we have received a master property and the number of units has been transferred, return it directly
        if ((bool) $realEstate->master && $realEstate->anzahlWohneinheiten)
        {
            return (int) ($realEstate->formatter->formatValue('anzahlWohneinheiten'));
        }

        $masterId = $realEstate->master ?: $realEstate->gruppenKennung;

        if ($masterId)
        {
            $t = static::$strTable;

            $arrColumns = [
                "$t.published='1'",
                "$t.master=''",
                "$t.gruppenKennung=?",
            ];

            $arrValues = [$masterId];

            return RealEstateModel::countPublishedBy($arrColumns, $arrValues, []);
        }
    }

    /**
     * Add status token for projects.
     *
     * @param $validStatusToken
     * @param $arrStatusTokens
     * @param $context
     */
    public function addStatusToken($validStatusToken, &$arrStatusTokens, $context): void
    {
        if (\in_array('project', $validStatusToken, true) && $context->objRealEstate->gruppenKennung)
        {
            $arrStatusTokens[] = [
                'value' => Translator::translateValue('project'),
                'class' => 'project',
            ];
        }
    }
}
