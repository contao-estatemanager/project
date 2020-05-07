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

use Contao\StringUtil;
use ContaoEstateManager\Translator;
use ContaoEstateManager\RealEstateModel;

/**
 * Class Project
 * @package ContaoEstateManager\Project
 * @author  Daniele Sciannimanica <daniele@oveleon.de>
 */
class Project
{
    /**
     * Table
     * @var string
     */
    private static $strTable = 'tl_real_estate';

    /**
     * Set project filter parameters
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
        if($addFragments)
        {
            $arrColumns[] = "$t.master=''";
        }
    }

    /**
     * Set mode param for google maps showing projects
     *
     * @param $template
     * @param $mapConfig
     * @param $context
     */
    public function compileGoogleMapConfig(&$template, &$mapConfig, $context): void
    {
        if($context->showProjects)
        {
            $mapConfig['source']['param']['filter'] = false;
            $mapConfig['source']['param']['mode'] = 'project';
        }
    }

    /**
     * Set filter parameter for projects by mode
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

        if($currParam['mode'] === 'project')
        {
            foreach ($arrColumns as $key => $column)
            {
                if(strpos($column, "master=''"))
                {
                    unset($arrColumns[$key]);
                }
            }

            $arrColumns[] = "$t.master!=''";
        }
    }

    /**
     * Returns project marketing status
     *
     * @param  $objMaster
     *
     * @return int: marketing status in percent
     */
    public static function getProjectMarketingStatus($objMaster): int
    {
        $t = static::$strTable;

        if(!!$objMaster->referenz)
        {
            return 100;
        }

        $arrColumns = array(
            "$t.published='1'",
            "$t.master=''",
            "$t.gruppenKennung=?"
        );

        $arrValues = array($objMaster->master);

        $objChildren = RealEstateModel::findBy($arrColumns, $arrValues, array());

        if($objChildren === null)
        {
            return 0;
        }

        $cntMarketed = 0;
        $cntChildren = $objChildren->count();

        while($objChildren->next())
        {
            if(!!$objChildren->referenz || !!$objChildren->vermietet || strtolower($objChildren->verkaufstatus) === 'reserviert' || strtolower($objChildren->verkaufstatus) === 'verkauft')
            {
                $cntMarketed++;
            }
        }

        return round((100 / $cntChildren) * $cntMarketed);
    }

    /**
     * Returns project main details as formatted collection
     *
     * @param $realEstate
     *
     * @return array
     */
    public static function getProjectSpecificDetails($realEstate): array
    {
        $details = array();

        if($realEstate->project_price_from && $realEstate->project_price_to)
        {
            $details['price'] = array(
                'label'   => Translator::translateLabel('project_price_label'),
                'details' => $realEstate->getFields(array(
                    'project_price_from',
                    'project_price_to'
                ))
            );
        }
        elseif($realEstate->project_price_from)
        {
            $details['price'] = array(
                'label'   => Translator::translateLabel('project_price_label'),
                'details' => $realEstate->getFields(array(
                    'project_price_from'
                ))
            );
        }
        elseif($realEstate->project_price_to)
        {
            $details['price'] = array(
                'label'   => Translator::translateLabel('project_price_label'),
                'details' => $realEstate->getFields(array(
                    'project_price_to'
                ))
            );
        }

        if($realEstate->project_area_from && $realEstate->project_area_to)
        {
            $details['area'] = array(
                'label'   => Translator::translateLabel('project_area_label'),
                'details' => $realEstate->getFields(array(
                    'project_area_from',
                    'project_area_to'
                ))
            );
        }
        elseif($realEstate->project_area_from)
        {
            $details['area'] = array(
                'label'   => Translator::translateLabel('project_area_label'),
                'details' => $realEstate->getFields(array(
                    'project_area_from'
                ))
            );
        }
        elseif($realEstate->project_area_to)
        {
            $details['area'] = array(
                'label'   => Translator::translateLabel('project_area_label'),
                'details' => $realEstate->getFields(array(
                    'project_area_to'
                ))
            );
        }

        if($realEstate->project_room_from && $realEstate->project_room_to)
        {
            $details['room'] = array(
                'label'   => Translator::translateLabel('project_room_label'),
                'details' => $realEstate->getFields(array(
                    'project_room_from',
                    'project_room_to'
                ))
            );
        }
        elseif($realEstate->project_room_from)
        {
            $details['room'] = array(
                'label'   => Translator::translateLabel('project_room_label'),
                'details' => $realEstate->getFields(array(
                    'project_room_from'
                ))
            );
        }
        elseif($realEstate->project_room_to)
        {
            $details['room'] = array(
                'label'   => Translator::translateLabel('project_room_label'),
                'details' => $realEstate->getFields(array(
                    'project_room_to'
                ))
            );
        }

        return $details;
    }

    /**
     * Returns number of children from master object or child object
     *
     * @param $realEstate
     *
     * @return int
     */
    public static function getNumberOfChildren($realEstate): int
    {
        // If we have received a master property and the number of units has been transferred, return it directly
        if(!!$realEstate->master && $realEstate->anzahlWohneinheiten)
        {
            return intval($realEstate->formatter->formatValue('anzahlWohneinheiten'));
        }

        $masterId = $realEstate->master ?: $realEstate->gruppenKennung;

        if($masterId)
        {
            $t = static::$strTable;

            $arrColumns = array(
                "$t.published='1'",
                "$t.master=''",
                "$t.gruppenKennung=?"
            );

            $arrValues = array($masterId);

            return RealEstateModel::countBy($arrColumns, $arrValues, array());
        }
    }

    /**
     * Add status token for project objects
     *
     * @param $objTemplate
     * @param $realEstate
     * @param $context
     */
    public function addStatusToken(&$objTemplate, $realEstate, $context): void
    {
        $tokens = StringUtil::deserialize($context->statusTokens);

        if(!$tokens)
        {
            return;
        }

        // add reference status token
        if (in_array('project', $tokens) && $realEstate->objRealEstate->gruppenKennung)
        {
            $objTemplate->arrStatusTokens = array_merge($objTemplate->arrStatusTokens, array(
                array(
                    'value' => Translator::translateValue('project'),
                    'class' => 'project'
                )
            ));
        }
    }
}
