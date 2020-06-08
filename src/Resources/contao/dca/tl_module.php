<?php
/**
 * This file is part of Contao EstateManager.
 *
 * @link      https://www.contao-estatemanager.com/
 * @source    https://github.com/contao-estatemanager/project
 * @copyright Copyright (c) 2019  Oveleon GbR (https://www.oveleon.de)
 * @license   https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

if(ContaoEstateManager\Project\AddonManager::valid()) {
    // Extend estate manager statusTokens field options
    $GLOBALS['TL_DCA']['tl_module']['fields']['statusTokens']['options'][] = 'project';

    // Add module palette for projects
    $GLOBALS['TL_DCA']['tl_module']['palettes']['realEstateProjectList']  = '{title_legend},name,headline,type;{config_legend},numberOfItems,perPage,filterMode,childrenObserveFiltering,addSorting;{redirect_legend},jumpTo,jumpToProject;{item_extension_legend:hide},addProvider,addContactPerson;{template_legend:hide},statusTokens,customTpl,realEstateProjectTemplate,realEstateTemplate,realEstateProviderTemplate,realEstateContactPersonTemplate;{image_legend:hide},imgSize,projectImgSize,providerImgSize,contactPersonImgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

    // Extend the googlemaps palettes
    Contao\CoreBundle\DataContainer\PaletteManipulator::create()
        ->addField(array('showProjects'), 'config_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
        ->applyToPalette('realEstateGoogleMap', 'tl_module')
    ;

    // Add field
    $GLOBALS['TL_DCA']['tl_module']['fields']['showProjects'] = array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showProjects'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'eval'                    => array('tl_class'=>'w50 m12'),
        'sql'                     => "char(1) NOT NULL default ''"
    );

    $GLOBALS['TL_DCA']['tl_module']['fields']['jumpToProject'] = array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['jumpToProject'],
        'exclude'                 => true,
        'inputType'               => 'pageTree',
        'foreignKey'              => 'tl_page.title',
        'eval'                    => array('fieldType'=>'radio'),
        'sql'                     => "int(10) unsigned NOT NULL default '0'",
        'relation'                => array('type'=>'hasOne', 'load'=>'eager')
    );

    $GLOBALS['TL_DCA']['tl_module']['fields']['realEstateProjectTemplate'] = array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['realEstateProjectTemplate'],
        'default'                 => 'real_estate_item_default',
        'exclude'                 => true,
        'inputType'               => 'select',
        'options_callback' => function () {
            return Contao\Controller::getTemplateGroup('real_estate_project_');
        },
        'eval'                    => array('tl_class'=>'w50'),
        'sql'                     => "varchar(64) NOT NULL default ''"
    );

    $GLOBALS['TL_DCA']['tl_module']['fields']['childrenObserveFiltering'] = array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['childrenObserveFiltering'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'eval'                    => array('tl_class'=>'w50 m12'),
        'sql'                     => "char(1) NOT NULL default ''"
    );

    $GLOBALS['TL_DCA']['tl_module']['fields']['projectImgSize'] = array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['projectImgSize'],
        'exclude'                 => true,
        'inputType'               => 'imageSize',
        'reference'               => &$GLOBALS['TL_LANG']['MSC'],
        'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
        'options_callback' => function ()
        {
            return Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(Contao\BackendUser::getInstance());
        },
        'sql'                     => "varchar(64) NOT NULL default ''"
    );
}
