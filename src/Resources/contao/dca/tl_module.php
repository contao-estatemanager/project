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

use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;
use ContaoEstateManager\Project\AddonManager;

if (AddonManager::valid())
{
    // Extend estate manager statusTokens field options
    $GLOBALS['TL_DCA']['tl_module']['fields']['statusTokens']['options'][] = 'project';

    // Add module palette for projects
    $GLOBALS['TL_DCA']['tl_module']['palettes']['realEstateProjectList'] = '{title_legend},name,headline,type;{config_legend},realEstateGroups,numberOfItems,perPage,filterMode,childrenObserveFiltering,addSorting;{redirect_legend},jumpTo,jumpToProject;{item_extension_legend:hide},addProvider,addContactPerson;{template_legend:hide},statusTokens,customTpl,realEstateProjectTemplate,realEstateTemplate,realEstateProviderTemplate,realEstateContactPersonTemplate;{image_legend:hide},imgSize,projectImgSize,providerImgSize,contactPersonImgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

    // Add field
    $GLOBALS['TL_DCA']['tl_module']['fields']['showProjects'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['showProjects'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_module']['fields']['jumpToProject'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['jumpToProject'],
        'exclude' => true,
        'inputType' => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval' => ['fieldType' => 'radio'],
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'relation' => ['type' => 'hasOne', 'load' => 'eager'],
    ];

    $GLOBALS['TL_DCA']['tl_module']['fields']['realEstateProjectTemplate'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['realEstateProjectTemplate'],
        'exclude' => true,
        'inputType' => 'select',
        'options_callback' => static fn () => Controller::getTemplateGroup('real_estate_project_'),
        'eval' => ['tl_class' => 'w50'],
        'sql' => "varchar(64) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_module']['fields']['childrenObserveFiltering'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['childrenObserveFiltering'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_module']['fields']['projectImgSize'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['projectImgSize'],
        'exclude' => true,
        'inputType' => 'imageSize',
        'reference' => &$GLOBALS['TL_LANG']['MSC'],
        'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
        'options_callback' => static fn () => System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance()),
        'sql' => "varchar(64) NOT NULL default ''",
    ];

    // Extend the googlemaps palettes
    $bundles = System::getContainer()->getParameter('kernel.bundles');

    if (isset($bundles['EstateManagerGoogleMaps']))
    {
        PaletteManipulator::create()
            ->addField(['showProjects'], 'config_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('realEstateGoogleMap', 'tl_module')
        ;
    }
}
