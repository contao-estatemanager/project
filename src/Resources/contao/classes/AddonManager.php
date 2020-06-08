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

use Contao\Config;
use Contao\Environment;
use ContaoEstateManager\EstateManager;

class AddonManager
{
    /**
     * Addon name
     * @var string
     */
    public static $name = 'Project';

    /**
     * Addon config key
     * @var string
     */
    public static $key  = 'addon_project_license';

    /**
     * Is initialized
     * @var boolean
     */
    public static $initialized  = false;

    /**
     * Is valid
     * @var boolean
     */
    public static $valid  = false;

    /**
     * Licenses
     * @var array
     */
    private static $licenses = [
        '9f2c8d1aff463c787110b6120c3860cc',
        '440c2eb7abc7156cd3caa80ae36e249f',
        'c890c18ca32a7a6443ae2473cf8d95ed',
        '14d2cee178ffe15910ce51175dbf5e34',
        'a0f4e60e521b592501bfe03690a0b86b',
        '68cc17a4cc79715774f8bea9c1c23a6e',
        'ade5e9f22a3de73e76008f137969024e',
        'f25d65a4b7a88cabef25419a95b42599',
        '0322b6cece24db5aefba5e03d7d81801',
        'bbee7682ad66c2964c65435c96f56221',
        '0c08e39d70b38325d41343ee9f65138b',
        'f326dc70ae606f6a9b88de28ba696800',
        '771d663ed70bf780381d91062160fd42',
        'cbf5aea228860619220c4ce57b2018c2',
        '94d87a61eb6e68eadc7aacffde53e72e',
        '233f9f2c801322242ab64fadb6cca53f',
        '6c35ff0ffade75d118befae50c8e9944',
        '09994a651e0a0231f06e5c3be9686f06',
        'b8c45155e445a5af1f7f7733a20d9148',
        'c7f017159398db0e930ea68d35199054',
        '28eaced068b6e8c0a9ffe65f497ad107',
        '50d89147a3cfd5ef4312dff7eb9f0dfc',
        '7bc56a97a5b8deb568d6f6ac07c74d11',
        'aee2d5ae07af45bc52b6a8f920533d7f',
        'b1aaf69f35dd497c0e096fa19e66f53c',
        '056aa2dc491367fceab390d2dc1ad04b',
        'b0ecc7ae1a2d27d32c13ecd782fb28bf',
        '2694ba6572d38f6b498e9bb631f208da',
        'a9cfb687105940515bb4985cf6ebdc56',
        '710ec035f13a04267be241e206dcb018',
        'c3b15a08fb4439162d8e4e1f7b19413f',
        'f9cdd518d6e02c48850d472a50d933b9',
        'b8d6f1c0b92ece147f3bf8f04f5198ac',
        '5817ddce00fcae0ca68ebe963fc47342',
        '861416b23579e89aefd6d9db303d0045',
        '250ed7279730409338583a77e0e20cb4',
        '6dc3134a7b02abd21c9c41b16bc99e65',
        '714025ef6883d02bdcfe6c560ce95605',
        '96b40eabde75ecf103789e0eb19a4967',
        '80850fc04b879f607d2d2ee0d5d995fb',
        '4d9aaceb91cf223a668f6702b24a28d2',
        '19b465668fe66e706e7939e047357b04',
        'e56c02a013f40235303b9fcc627804a5',
        '8feb2e0a5d08c11156b46adb8a5ac324',
        '58fa5a28fdef5e884e8061529e91c2df',
        '6a3eb52eb91346d047938770f9a93f10',
        'bfd0a675e2782ae2122248d4e7ae3b61',
        'ddea2c83ca66ec610f4c677f21820993',
        '71e16c05d62f94a9514bf87cb2c12ac4',
        '0ca8d2e49c5cd6c553501af01c93df6a'
    ];

    public static function getLicenses()
    {
        return static::$licenses;
    }

    public static function valid()
    {
        if(strpos(Environment::get('requestUri'), '/contao/install') !== false)
        {
            return true;
        }

        if (static::$initialized === false)
        {
            static::$valid = EstateManager::checkLicenses(Config::get(static::$key), static::$licenses, static::$key);
            static::$initialized = true;
        }

        return static::$valid;
    }

}
