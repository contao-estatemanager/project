<?php
/**
 * This file is part of Contao EstateManager.
 *
 * @link      https://www.contao-estatemanager.com/
 * @source    https://github.com/contao-estatemanager/project
 * @copyright Copyright (c) 2019  Oveleon GbR (https://www.oveleon.de)
 * @license   https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

// Load translations
\System::loadLanguageFile('tl_real_estate_misc');

if(ContaoEstateManager\Project\AddonManager::valid()) {

    // Add onsubmit callback
    array_insert($GLOBALS['TL_DCA']['tl_real_estate']['config']['onsubmit_callback'], 0, array(
        array('tl_real_estate_project', 'setMasterObjectInformation')
    ));

    $GLOBALS['TL_DCA']['tl_real_estate']['list']['label']['post_label_callbacks'][] = array('tl_real_estate_project', 'addProjectInformation');

    // Add field
    array_insert($GLOBALS['TL_DCA']['tl_real_estate']['fields'], -1, array(
        'project_price_from' => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['project_price_from'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>20, 'tl_class'=>'w50'),
            'sql'                       => "decimal(10,2) NULL default NULL"
        ),
        'project_price_to' => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['project_price_to'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>20, 'tl_class'=>'w50'),
            'sql'                       => "decimal(10,2) NULL default NULL"
        ),
        'project_area_from' => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['project_area_from'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>20, 'tl_class'=>'w50'),
            'sql'                       => "decimal(10,2) NULL default NULL"
        ),
        'project_area_to' => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['project_area_to'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>20, 'tl_class'=>'w50'),
            'sql'                       => "decimal(10,2) NULL default NULL"
        ),
        'project_room_from' => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['project_room_from'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>20, 'tl_class'=>'w50'),
            'sql'                       => "decimal(10,2) NULL default NULL"
        ),
        'project_room_to' => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['project_room_to'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>20, 'tl_class'=>'w50'),
            'sql'                       => "decimal(10,2) NULL default NULL"
        ),
        'gruppenKennung'  => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['gruppenKennung'],
            'inputType'                 => 'text',
            'filter'                    => true,
            'eval'                      => array('maxlength'=>32, 'tl_class'=>'w50'),
            'sql'                       => "varchar(32) NOT NULL default ''",
            'realEstate'                => array(
                'group'    => 'neubau'
            )
        ),
        'master'  => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['master'],
            'inputType'                 => 'text',
            'eval'                      => array('maxlength'=>32, 'tl_class'=>'w50'),
            'sql'                       => "varchar(32) NOT NULL default ''",
            'realEstate'                => array(
                'group'    => 'neubau',
                'filter'   => true,
            )
        ),
        'completionStatus'  => array
        (
            'label'                     => &$GLOBALS['TL_LANG']['tl_real_estate']['completionStatus'],
            'inputType'                 => 'select',
            'eval'                      => array('maxlength'=>32, 'tl_class'=>'w50', 'includeBlankOption'=>true),
            'options'                   => array(10, 20, 30, 40, 50),
            'reference'                 => &$GLOBALS['TL_LANG']['tl_real_estate_project_misc'],
            'sql'                       => "varchar(32) NOT NULL default ''",
            'realEstate'                => array(
                'group'    => 'neubau'
            )
        )
    ));

    // Extend the default palettes
    Contao\CoreBundle\DataContainer\PaletteManipulator::create()
        ->addLegend('real_estate_project_legend', 'real_estate_media_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
        ->addField(array('master', 'gruppenKennung', 'completionStatus', 'project_price_from', 'project_price_to', 'project_area_from', 'project_area_to', 'project_room_from', 'project_room_to'), 'real_estate_project_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
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
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Check permissions to edit table tl_real_estate
     */
    public function checkPermission()
    {
        return;
    }

    /**
     * Set information to the master object
     *
     * @param DataContainer $dc
    */
    public function setMasterObjectInformation(DataContainer $dc)
    {
        if ($dc->activeRecord->master = '' && $dc->activeRecord->gruppenKennung != '')
        {
            // ToDo: Ermitteln der primären Felder mit anschließender prüfung ob diese in das Master-Objekt geschrieben werden muss (nur published bei unblished alle kinder durchlaufen und neuen wert setzen)
        }
    }

    /**
     * Add reference flag
     *
     * @param array         $row
     * @param string        $label
     * @param DataContainer $dc
     * @param array         $args
     *
     * @return array
     */
    public function addProjectInformation($row, $label, DataContainer $dc, $args)
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