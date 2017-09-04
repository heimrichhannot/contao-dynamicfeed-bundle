<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoDynamicFeedBundle\Components;


use Contao\NewsArchiveModel;
use HeimrichHannot\ContaoDynamicFeedBundle\Component\FeedSourceInterface;
use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use Model\Collection;

class NewsArchiveFeedSource implements FeedSourceInterface
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
     * Return the label for the feed source
     *
     * Example: $GLOBALS['TL_LANG']['tl_news_feed']['source_tag']
     *
     * @return string
     */
    public function getLabel()
    {
        return \System::getContainer()->get('translator')->trans('hh.dynamicfeed.feedsource.newsarchive.label');
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
        $objArchive = NewsArchiveModel::findByIdOrAlias($varChannel);
        return $objArchive;
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
        $objArchives = NewsArchiveModel::findAll();
        return $objArchives;
    }

    /**
     * Return news belonging to the channel
     *
     * @param NewsArchiveModel $objChannel
     * @param integer          $maxItems Max items to return. 0 = all items
     *
     * @return Collection|Model|NewsModel[]|NewsModel|null
     */
    public static function getItemsByChannel($objChannel, $maxItems = 0)
    {
        if (is_int($maxItems) && $maxItems > 0)
        {
            $opt['limit'] = $maxItems;
        }
        $objNews = NewsModel::findByPid($objChannel->id);
        return $objNews;
    }

    /**
     * Returns the title of the channel.
     *
     * Return null, if channel not exist.
     *
     * @param NewsArchiveModel $objChannel
     *
     * @return string|null
     */
    public static function getChannelTitle($objChannel)
    {
        return $objChannel ? $objChannel->title : null;
    }
}