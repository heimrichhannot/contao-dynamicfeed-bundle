<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoNewsAlertBundle\Components;


use Contao\NewsArchiveModel;
use HeimrichHannot\ContaoDynamicFeedBundle\Component\FeedSourceInterface;
use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\NewsBundle\NewsModel;
use Model\Collection;

class NewsArchiveTopics implements FeedSourceInterface
{
    /**
     * Returns the alias of the topic source, e.g. category, tag, collection,...
     *
     * Can be the database column in tl_news.
     * Should be unique.
     *
     * @return string
     */
    public static function getAlias()
    {
        return 'archive';
    }

    /**
     * Return all available topics.
     *
     * @return array
     */
    public static function getTopics()
    {
        $objArchives = NewsArchiveModel::findAll();
        $arrArchives = [];
        while ($objArchives->next())
        {
            $arrArchives[] = $objArchives->title;
        }
        return $arrArchives;
    }

    /**
     * Returns topics by news item
     *
     * @param $objItem \NewsModel
     *
     * @return array
     */
    public static function getTopicsByItem($objItem)
    {
        $strArchive = NewsArchiveModel::findById($objItem->pid)->title;
        return [$strArchive];
    }

    /**
     * Return the label for the feed source
     *
     * Example: $GLOBALS['TL_LANG']['tl_news_feed']['source_tag']
     *
     * @return string
     */
    public function getLabel()
    {
        // TODO: Implement getLabel() method.
    }

    /**
     * Returns a single news channel.
     *
     * Channels are collections of news entries, e.g. a category, a tag, etc.
     * Channels should have an unique identifier and an unique alias
     * The channel My Category can lead to /share/category/my-category or /share/category/4 (if 4 is the id).
     *
     * @param string|integer $channel identifier or unique alias of the channel
     *
     * @return Collection|Model|null
     */
    public function getChannel($varChannel)
    {
        // TODO: Implement getChannel() method.
    }

    /**
     * Return all available channels.
     *
     * Channels: see getChannel() doc
     *
     * @return Collection|Model|null
     */
    public static function getChannels()
    {
        // TODO: Implement getChannels() method.
    }

    /**
     * Return news belonging to the channel
     *
     * @param Collection|Model $channel
     * @param integer          $maxItems Max items to return. 0 = all items
     *
     * @return Collection|Model|NewsModel[]|NewsModel|null
     */
    public static function getItemsByChannel($objChannel, $maxItems = 0)
    {
        // TODO: Implement getItemsByChannel() method.
    }

    /**
     * Returns the title of the channel.
     *
     * Return null, if channel not exist.
     *
     * @param Model $objChannel
     *
     * @return string|null
     */
    public static function getChannelTitle($objChannel)
    {
        // TODO: Implement getChannelTitle() method.
    }
}