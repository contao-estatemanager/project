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

use Contao\Controller;
use ContaoEstateManager\Project\AddonManager;

if (AddonManager::valid())
{
    // Add palettes
    $GLOBALS['TL_DCA']['tl_expose_module']['palettes']['projectDetails'] = '{title_legend},name,headline,type;{settings_legend},projectDetails;{template_legend:hide},customTpl,projectDetailsTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
    $GLOBALS['TL_DCA']['tl_expose_module']['palettes']['projectChildrenList'] = '{title_legend},name,headline,type;{settings_legend},childrenObserveFiltering,jumpTo;{image_legend:hide},imgSize;{template_legend:hide},customTpl,projectChildrenListTemplate,realEstateTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
    $GLOBALS['TL_DCA']['tl_expose_module']['palettes']['projectMarketingStatus'] = '{title_legend},name,headline,type;{settings_legend},hideOnZeroPercent;{template_legend:hide},customTpl,projectMarketingStatusTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
    $GLOBALS['TL_DCA']['tl_expose_module']['palettes']['projectCompletionStatus'] = '{title_legend},name,headline,type;{settings_legend},completionStatus;{template_legend:hide},customTpl,projectCompletionStatusTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

    // Add fields
    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['projectDetailsTemplate'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['projectDetailsTemplate'],
        'default' => 'expose_mod_project_details',
        'exclude' => true,
        'inputType' => 'select',
        'options_callback' => static fn () => Controller::getTemplateGroup('expose_mod_project_details_'),
        'eval' => ['tl_class' => 'w50'],
        'sql' => "varchar(64) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['projectMarketingStatusTemplate'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['projectMarketingStatusTemplate'],
        'default' => 'expose_mod_project_marketing_status',
        'exclude' => true,
        'inputType' => 'select',
        'options_callback' => static fn () => Controller::getTemplateGroup('expose_mod_project_marketing_status_'),
        'eval' => ['tl_class' => 'w50'],
        'sql' => "varchar(64) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['projectChildrenListTemplate'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['projectChildrenListTemplate'],
        'default' => 'expose_mod_project_children_list',
        'exclude' => true,
        'inputType' => 'select',
        'options_callback' => static fn () => Controller::getTemplateGroup('expose_mod_project_children_list_'),
        'eval' => ['tl_class' => 'w50'],
        'sql' => "varchar(64) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['projectCompletionStatusTemplate'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['projectCompletionStatusTemplate'],
        'default' => 'expose_mod_project_children_list',
        'exclude' => true,
        'inputType' => 'select',
        'options_callback' => static fn () => Controller::getTemplateGroup('expose_mod_project_completion_status_'),
        'eval' => ['tl_class' => 'w50'],
        'sql' => "varchar(64) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['projectDetails'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['projectDetails'],
        'exclude' => true,
        'inputType' => 'checkboxWizard',
        'options' => ['price', 'area', 'room', 'children'],
        'reference' => &$GLOBALS['TL_LANG']['tl_real_estate_misc'],
        'eval' => ['multiple' => true],
        'sql' => 'blob NULL',
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['completionStatus'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['completionStatus'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'options' => [10, 20, 30, 40, 50],
        'reference' => &$GLOBALS['TL_LANG']['tl_real_estate_project_misc'],
        'eval' => ['multiple' => true],
        'sql' => 'blob NULL',
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['childrenObserveFiltering'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['childrenObserveFiltering'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['hideOnZeroPercent'] = [
        'label' => &$GLOBALS['TL_LANG']['tl_expose_module']['hideOnZeroPercent'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ];

    // Extend estate manager statusTokens field options
    $GLOBALS['TL_DCA']['tl_expose_module']['fields']['statusTokens']['options'][] = 'project';
}
