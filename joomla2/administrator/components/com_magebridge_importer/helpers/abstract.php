<?php
/*
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * MageBridge Importer Structure
 */
class HelperAbstract
{
    /**
     * Structural data of this component
     */
    static public function getStructure()
    {
        return array(
            'title' => 'MageBridge Importer',
            'menu' => array(
                'products' => 'PRODUCTS',
                'profiles' => 'PROFILES',
                'fields' => 'FIELDS',
            ),
            'views' => array(
                'products' => 'PRODUCTS',
                'product' => 'PRODUCT',
                'profiles' => 'PROFILES',
                'profile' => 'PROFILE',
                'fields' => 'FIELDS',
                'field' => 'FIELD',
            ),
        );
    }
}
