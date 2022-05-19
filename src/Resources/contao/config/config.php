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

// ESTATEMANAGER
$GLOBALS['TL_ESTATEMANAGER_ADDONS'][] = ['ContaoEstateManager\Project', 'AddonManager'];

use ContaoEstateManager\Project\AddonManager;

if (AddonManager::valid())
{
    // Expose module
    $GLOBALS['FE_EXPOSE_MOD']['project'] = [
        'projectDetails' => 'ContaoEstateManager\Project\ExposeModuleProjectDetails',
        'projectChildrenList' => 'ContaoEstateManager\Project\ExposeModuleProjectChildrenList',
        'projectMarketingStatus' => 'ContaoEstateManager\Project\ExposeModuleProjectMarketingStatus',
        'projectCompletionStatus' => 'ContaoEstateManager\Project\ExposeModuleProjectCompletionStatus',
    ];

    // Front end modules
    $GLOBALS['FE_MOD']['estatemanager']['realEstateProjectList'] = 'ContaoEstateManager\Project\ModuleRealEstateProjectList';

    // Hooks
    $GLOBALS['CEM_HOOKS']['getTypeParameter'][] = ['ContaoEstateManager\Project\Project', 'setFilterParameter'];
    $GLOBALS['CEM_HOOKS']['getParameterByGroups'][] = ['ContaoEstateManager\Project\Project', 'setFilterParameter'];
    $GLOBALS['CEM_HOOKS']['getParameterByTypes'][] = ['ContaoEstateManager\Project\Project', 'setFilterParameter'];
    $GLOBALS['CEM_HOOKS']['getTypeParameterByGroups'][] = ['ContaoEstateManager\Project\Project', 'setFilterParameter'];

    $GLOBALS['CEM_HOOKS']['getStatusTokens'][] = ['ContaoEstateManager\Project\Project', 'addStatusToken'];

    $GLOBALS['CEM_HOOKS']['compileRealEstateGoogleMap'][] = ['ContaoEstateManager\Project\Project', 'compileGoogleMapConfig'];
    $GLOBALS['CEM_HOOKS']['readEstatesControllerParameter'][] = ['ContaoEstateManager\Project\Project', 'setEstatesControllerParameter'];
}
