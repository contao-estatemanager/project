<?php
/**
 * This file is part of Contao EstateManager.
 *
 * @link      https://www.contao-estatemanager.com/
 * @source    https://github.com/contao-estatemanager/project
 * @copyright Copyright (c) 2019  Oveleon GbR (https://www.oveleon.de)
 * @license   https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

// ESTATEMANAGER
$GLOBALS['TL_ESTATEMANAGER_ADDONS'][] = array('ContaoEstateManager\\Project', 'AddonManager');

if(ContaoEstateManager\Project\AddonManager::valid()) {
    // Expose module
    array_insert($GLOBALS['FE_EXPOSE_MOD'], -1, array
    (
        'project' => array(
            'projectDetails'          => '\\ContaoEstateManager\\Project\\ExposeModuleProjectDetails',
            'projectChildrenList'     => '\\ContaoEstateManager\\Project\\ExposeModuleProjectChildrenList',
            'projectMarketingStatus'  => '\\ContaoEstateManager\\Project\\ExposeModuleProjectMarketingStatus',
            'projectCompletionStatus' => '\\ContaoEstateManager\\Project\\ExposeModuleProjectCompletionStatus'
        )
    ));

    // Front end modules
    $GLOBALS['FE_MOD']['estatemanager']['realEstateProjectList'] = '\\ContaoEstateManager\\Project\\ModuleRealEstateProjectList';

    // Hooks
    $GLOBALS['TL_HOOKS']['getTypeParameter'][]          = array('ContaoEstateManager\\Project\\Project', 'setFilterParameter');
    $GLOBALS['TL_HOOKS']['getParameterByGroups'][]      = array('ContaoEstateManager\\Project\\Project', 'setFilterParameter');
    $GLOBALS['TL_HOOKS']['getTypeParameterByGroups'][]  = array('ContaoEstateManager\\Project\\Project', 'setFilterParameter');

    $GLOBALS['TL_HOOKS']['getStatusTokens'][]           = array('ContaoEstateManager\\Project\\Project', 'addStatusToken');

    $GLOBALS['TL_HOOKS']['compileRealEstateGoogleMap'][]     = array('ContaoEstateManager\\Project\\Project', 'compileGoogleMapConfig');
    $GLOBALS['TL_HOOKS']['readEstatesControllerParameter'][] = array('ContaoEstateManager\\Project\\Project', 'setEstatesControllerParameter');
}
