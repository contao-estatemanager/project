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
    public function setFilterParameter(&$arrColumns, &$arrValues, &$arrOptions, $mode, $addFragments, $context)
    {
        $t = static::$strTable;

        // Excluding projects in default lists
        if($addFragments)
        {
            $arrColumns[] = "$t.master=''";
        }
    }

    /**
     * Returns project marketing status
     *
     * @param  $masterId
     *
     * @return integer: marketing status in percent
     */
    public static function getProjectMarketingStatus($masterId)
    {
        $t = static::$strTable;

        $arrColumns = array(
            "$t.published='1'",
            "$t.master=''",
            "$t.gruppenKennung=?"
        );

        $arrValues = array($masterId);

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
    public static function getProjectSpecificDetails($realEstate)
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
     * Add status token for project objects
     *
     * @param $objTemplate
     * @param $realEstate
     * @param $context
     */
    public function addStatusToken(&$objTemplate, $realEstate, $context)
    {
        $tokens = \StringUtil::deserialize($context->statusTokens);

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