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
    array_insert($GLOBALS['TL_DCA']['tl_module']['fields']['statusTokens']['options'], -1, array('project'));

    // Add module palette for projects
    array_insert($GLOBALS['TL_DCA']['tl_module']['palettes'], 0, array
    (
        'realEstateProjectList'  => '{title_legend},name,headline,type;{config_legend},numberOfItems,perPage,filterMode,childrenObserveFiltering,addSorting;{redirect_legend},jumpTo,jumpToProject;{item_extension_legend:hide},addProvider,addContactPerson;{template_legend:hide},statusTokens,customTpl,realEstateProjectTemplate,realEstateTemplate,realEstateProviderTemplate,realEstateContactPersonTemplate;{image_legend:hide},imgSize,providerImgSize,contactPersonImgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID',
    ));

    // Add field
    array_insert($GLOBALS['TL_DCA']['tl_module']['fields'], -1, array(
        'jumpToProject' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_module']['jumpToProject'],
            'exclude'                 => true,
            'inputType'               => 'pageTree',
            'foreignKey'              => 'tl_page.title',
            'eval'                    => array('fieldType'=>'radio'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=>'hasOne', 'load'=>'eager')
        ),
        'realEstateProjectTemplate' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_module']['realEstateProjectTemplate'],
            'default'                 => 'real_estate_item_default',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => array('tl_module_estate_manager_project', 'getRealEstateProjectTemplates'),
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'childrenObserveFiltering' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_module']['childrenObserveFiltering'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 m12'),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    ));
}

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class tl_module_estate_manager_project extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Return all real estate list templates as array
     *
     * @return array
     */
    public function getRealEstateProjectTemplates()
    {
        return $this->getTemplateGroup('real_estate_project_');
    }
}