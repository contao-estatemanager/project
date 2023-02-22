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

use Contao\Backend;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\System;
use ContaoEstateManager\Project\AddonManager;

// Load translations
System::loadLanguageFile('tl_real_estate_misc');

if (AddonManager::valid())
{
    // Add onsubmit callback
    array_insert($GLOBALS['TL_DCA']['tl_real_estate']['config']['onsubmit_callback'], 0, [
        ['tl_real_estate_project', 'setMasterObjectInformation'],
    ]);

    $GLOBALS['TL_DCA']['tl_real_estate']['list']['label']['post_label_callbacks'][] = ['tl_real_estate_project', 'addProjectInformation'];

    // Add field
    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['project_price_from'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['project_price_from'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 20, 'tl_class' => 'w50 clr'],
        'sql' => 'decimal(10,2) NULL default NULL',
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['project_price_to'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['project_price_to'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 20, 'tl_class' => 'w50'],
        'sql' => 'decimal(10,2) NULL default NULL',
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['project_area_from'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['project_area_from'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 20, 'tl_class' => 'w50'],
        'sql' => 'decimal(10,2) NULL default NULL',
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['project_area_to'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['project_area_to'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 20, 'tl_class' => 'w50'],
        'sql' => 'decimal(10,2) NULL default NULL',
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['project_room_from'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['project_room_from'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 20, 'tl_class' => 'w50'],
        'sql' => 'decimal(10,2) NULL default NULL',
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['project_room_to'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['project_room_to'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 20, 'tl_class' => 'w50'],
        'sql' => 'decimal(10,2) NULL default NULL',
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['gruppenKennung'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['gruppenKennung'],
        'exclude' => true,
        'inputType' => 'text',
        'filter' => true,
        'eval' => ['maxlength' => 32, 'tl_class' => 'w50'],
        'sql' => "varchar(32) NOT NULL default ''",
        'realEstate' => [
            'group' => 'neubau',
        ],
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['master'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['master'],
        'exclude' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 32, 'tl_class' => 'w50'],
        'sql' => "varchar(32) NOT NULL default ''",
        'realEstate' => [
            'group' => 'neubau',
            'filter' => true,
        ],
    ];

    $GLOBALS['TL_DCA']['tl_real_estate']['fields']['completionStatus'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_real_estate']['completionStatus'],
        'exclude' => true,
        'inputType' => 'select',
        'eval' => ['maxlength' => 32, 'tl_class' => 'w50', 'includeBlankOption' => true],
        'options' => [10, 20, 30, 40, 50],
        'reference' => &$GLOBALS['TL_LANG']['tl_real_estate_project_misc'],
        'sql' => "varchar(32) NOT NULL default ''",
        'realEstate' => [
            'group' => 'neubau',
        ],
    ];

    // Extend the default palettes
    PaletteManipulator::create()
        ->addLegend('real_estate_project_legend', 'real_estate_media_legend', PaletteManipulator::POSITION_AFTER)
        ->addField(['master', 'gruppenKennung', 'completionStatus', 'project_price_from', 'project_price_to', 'project_area_from', 'project_area_to', 'project_room_from', 'project_room_to'], 'real_estate_project_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette('default', 'tl_real_estate')
    ;
}

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class tl_real_estate_project extends Backend
{
    /**
     * Import the back end user object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Contao\BackendUser', 'User');
    }

    /**
     * Set information to the master object.
     */
    public function setMasterObjectInformation(DataContainer $dc): void
    {
        if ($dc->activeRecord->master = '' && '' !== $dc->activeRecord->gruppenKennung)
        {
            // ToDo: Ermitteln der primären Felder mit anschließender Prüfung ob diese in das Master-Objekt geschrieben werden muss (nur published bei unpublished alle kinder durchlaufen und neuen Wert setzen)
        }
    }

    /**
     * Add reference flag.
     *
     * @param array  $row
     * @param string $label
     * @param array  $args
     */
    public function addProjectInformation($row, $label, DataContainer $dc, $args): array
    {
        if (!$row['gruppenKennung'] && !$row['master'])
        {
            return $args;
        }

        // add project information
        if ($row['gruppenKennung'] && $row['master'])
        {
            $args[0] .= '<span class="token" style="background-color:#1578ea; color:#fff;" title="Neubauprojekt">N</span>';
        }
        else
        {
            $args[0] .= '<span class="token" style="background-color:#4c98ef; color:#fff;" title="Wohneinheit">W</span>';
        }

        return $args;
    }
}
