<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
        ->addLegend('dynamic_feed_legend', 'archives_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
        ->addField('feedGeneration','dynamic_feed_legend',\Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
        ->addField('news_source','dynamic_feed_legend',\Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default','tl_news_feed');

$dc = &$GLOBALS['TL_DCA']['tl_news_feed'];

$dc['__selector__'][] = 'feedGeneration';
$dc['subpalettes']['feedGeneration_dynamic'] = 'news_source';
$dc['subpalettes']['feedGeneration_xml'] = '';

$fields = [
    'feedGeneration' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_feed']['feedGeneration'],
        'default'   => \HeimrichHannot\ContaoDynamicFeedBundle\Component\NewsFeedGenerator::FEEDGENERATION_XML,
        'filter'    => true,
        'inputType' => 'select',
        'options'   => [
            'xml' => $GLOBALS['TL_LANG']['tl_news_feed']['feedGeneration_xml'],
            'dynamic' => $GLOBALS['TL_LANG']['tl_news_feed']['feedGeneration_dynamic'],
        ],
        'eval'      => [
            'tl_class' => 'w50',
            'submitOnChange' => true
        ],
        'sql'       => "varchar(32) NOT NULL default '".\HeimrichHannot\ContaoDynamicFeedBundle\Component\NewsFeedGenerator::FEEDGENERATION_XML."'"
    ],
    'news_source'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_feed']['news_source'],
        'exclude'          => true,
        'inputType'        => 'select',
        'filter'           => true,
        'eval'             => ['tl_class' => 'w50'],
        'options_callback' => ['hh.dynamicfeed.feed_generator', 'getDcaSourceOptions'],
        'sql'              => "varchar(32) default NULL"
    ]
];


$dc['fields'] = array_merge($dc['fields'], $fields);