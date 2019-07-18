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
    // Add field
    array_insert($GLOBALS['TL_DCA']['tl_expose_module']['palettes'], -1, array
    (
        'projectDetails'          => '{title_legend},name,headline,type;{settings_legend},projectDetails;{template_legend:hide},customTpl,projectDetailsTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID',
        'projectChildrenList'     => '{title_legend},name,headline,type;{settings_legend},childrenObserveFiltering,jumpTo;{image_legend:hide},imgSize;{template_legend:hide},customTpl,projectChildrenListTemplate,realEstateTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID',
        'projectMarketingStatus'  => '{title_legend},name,headline,type;{settings_legend},hideOnZeroPercent;{template_legend:hide},customTpl,projectMarketingStatusTemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID'
    ));

    // Add fields
    array_insert($GLOBALS['TL_DCA']['tl_expose_module']['fields'], -1, array(
        'projectDetailsTemplate' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_expose_module']['projectDetailsTemplate'],
            'default'                 => 'expose_mod_project_details',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => array('tl_expose_module_project', 'getProjectDetailsTemplates'),
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'projectMarketingStatusTemplate' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_expose_module']['projectMarketingStatusTemplate'],
            'default'                 => 'expose_mod_project_marketing_status',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => array('tl_expose_module_project', 'getProjectMarketingStatusTemplates'),
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'projectChildrenListTemplate' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_expose_module']['projectChildrenListTemplate'],
            'default'                 => 'expose_mod_project_children_list',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => array('tl_expose_module_project', 'getProjectChildrenListTemplates'),
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'projectDetails' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_expose_module']['projectDetails'],
            'exclude'                 => true,
            'inputType'               => 'checkboxWizard',
            'options'                 => array('price', 'area', 'room', 'children'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_real_estate_misc'],
            'eval'                    => array('multiple'=>true),
            'sql'                     => "blob NULL"
        ),
        'childrenObserveFiltering' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_expose_module']['childrenObserveFiltering'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 m12'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'hideOnZeroPercent' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_expose_module']['hideOnZeroPercent'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('tl_class'=>'w50 m12'),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    ));

    // Extend estate manager statusTokens field options
    array_insert($GLOBALS['TL_DCA']['tl_expose_module']['fields']['statusTokens']['options'], -1, array('project'));
}


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Daniele Sciannimanica <daniele@oveleon.de>
 */
class tl_expose_module_project extends \Backend
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
     * Return all project details templates as array
     *
     * @return array
     */
    public function getProjectDetailsTemplates()
    {
        return $this->getTemplateGroup('expose_mod_project_details_');
    }

    /**
     * Return all marketing status templates as array
     *
     * @return array
     */
    public function getProjectMarketingStatusTemplates()
    {
        return $this->getTemplateGroup('expose_mod_project_marketing_status_');
    }

    /**
     * Return all children list templates as array
     *
     * @return array
     */
    public function getProjectChildrenListTemplates()
    {
        return $this->getTemplateGroup('expose_mod_project_children_list_');
    }
}