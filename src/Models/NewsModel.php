<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoDynamicFeedBundle\Models;

/**
 * Class NewsModel
 *
 * @package HeimrichHannot\ContaoDynamicFeedBundle\Models
 *
 * @inheritdoc
 */
class NewsModel extends \Contao\NewsModel
{
    /**
     * @param string     $strSource News source
     * @param int|string $varId     Id or unique alias of news channel. 0 for all channels of type
     * @param int        $intLimit
     * @param int        $intOffset
     * @param array      $arrOptions
     *
     * @return \Contao\Model\Collection|NewsModel[]|NewsModel|null
     */
    public static function findPublishedByNewsSource($strSource, $varId = 0, $intLimit = 0, $intOffset = 0, $arrOptions)
    {
        if (!is_string($strSource) && empty($strSource))
        {
            return null;
        }
        if (!is_int($varId) && !is_string($varId))
        {
            return null;
        }
        $t         = static::$strTable;
        $objSource = \System::getContainer()->get('hh.dynamicfeed.feed_generator')->getFeedSource($strSource);
        if (!$objSource)
        {
            return null;
        }
        if ($varId !== 0)
        {
            $objChannel = $objSource->getChannel($varId);
            $objNews    = $objSource->getItemsByChannel($objChannel);
            if ($objNews === null)
            {
                return null;
            }
            $arrNewsIds = [];
            while ($objNews->next())
            {
                $arrNewsIds[] = $objNews->id;
            }
        }
        else
        {
            $objChannels = $objSource->getChannels();
            $arrNewsIds  = [];
            while ($objChannels->next())
            {
                $objNews = $objSource->getItemsByChannel($objChannels);
                if ($objNews === null)
                {
                    continue;
                }
                while ($objNews->next())
                {
                    $arrNewsIds[] = $objNews->id;
                }
            }
        }
        $arrColumns[] = "$t.id IN (" . implode(',', (empty($arrNewsIds) ? [] : array_unique($arrNewsIds))) . ")";

        return static::findPublished($arrColumns, $intLimit, $intOffset, $arrOptions);
    }

    /**
     * @param       $arrColumns
     * @param int   $intLimit
     * @param int   $intOffset
     * @param array $arrOptions
     *
     * @return \Contao\Model\Collection|NewsModel|NewsModel[]|null
     */
    private static function findPublished($arrColumns, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        $t = static::$strTable;
        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }
}