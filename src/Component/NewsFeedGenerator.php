<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoDynamicFeedBundle\Component;

use Contao\System;
use function GuzzleHttp\Promise\queue;
use HeimrichHannot\Haste\Util\StringUtil;

class NewsFeedGenerator
{
    const FEEDGENERATION_DYNAMIC = 'dynamic';
    const FEEDGENERATION_XML = 'xml';


    /**
     * @var FeedSourceInterface[] $feedSource
     */
    protected $feedSource = [];
    protected $feedSourceId = [];
    protected $maxItems = 0;

    /**
     * Add feed source
     *
     * @param FeedSourceInterface $source
     */
    public function addFeedSource(FeedSourceInterface $source)
    {
        $this->feedSource[$source->getAlias()] = $source;
    }

    /**
     * Get Feedsource by type
     *
     * @param string $key
     *
     * @return FeedSourceInterface|null
     */
    public function getFeedSource($key)
    {
        if (!isset($this->feedSource[$key]))
        {
            return null;
        }
        return $this->feedSource[$key];
    }

    public function getDcaSourceOptions ()
    {
        $options = [];
        foreach ($this->feedSource as $source)
        {
            $options[$source->getAlias()] = $source->getLabel();
        }
        return $options;
    }

    /**
     * @param array $arrFeed
     * @param string|int $varId Id oder unique alias of news source
     *
     * @return string|null
     */
    public function generateFeed($arrFeed, $varId=0)
    {
        $objChannel = null;
        if ($varId !== 0)
        {
            $objSource = static::getFeedSource($arrFeed['df_newsSource']);
            if ($objSource === null) {
                return null;
            }
            $objChannel = $objSource->getChannel($varId);
            if ($objChannel === null) {
                return null;
            }
            $strTitle = $objSource->getChannelTitle($objChannel);
            $strLabel = $objSource->getLabel();
            if ($strTitle !== null) {
                $arrFeed['title'] = str_replace($strLabel, $strTitle, $arrFeed['title']);
            }
        }
        if ($this->maxItems > 0)
        {
            $arrFeed['maxItems'] = $this->maxItems;
        }

        $news = new News();
        $objFeed = $news->generateDynamicFeed($arrFeed, $varId);
        if (isset($GLOBALS['TL_HOOKS']['dynamicfeedBeforeGeneration'])
            && is_array($GLOBALS['TL_HOOKS']['dynamicfeedBeforeGeneration']))
        {
            foreach ($GLOBALS['TL_HOOKS']['dynamicfeedBeforeGeneration'] as $callback)
            {
                $objFeed = System::importStatic($callback[0])->{$callback[1]}($objFeed, $arrFeed, $objChannel);
            }
        }

        $strFeed =   $objFeed->generateRss();

        return StringUtil::replaceNonXmlEntities($strFeed);
    }

    /**
     * @return int
     */
    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }
}