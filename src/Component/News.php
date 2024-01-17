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

use Contao\BackendUser;
use Contao\Config;
use Contao\Environment;
use Contao\Feed;
use Contao\FeedItem;
use Contao\PageModel;
use Contao\StringUtil;
use HeimrichHannot\ContaoDynamicFeedBundle\Models\NewsModel;

class News extends \Contao\News
{
    /**
     * @param array $arrFeed
     * @param string|int $varId ID or unique alias
     *
     * @return Feed|null
     */
    public function generateDynamicFeed($arrFeed, $varId = 0, $order = 'date DESC')
    {
        $arrArchives = StringUtil::deserialize($arrFeed['archives']);
        if (!is_array($arrArchives) || empty($arrArchives)) {
            return null;
        }
        $strType = ($arrFeed['format'] == 'atom') ? 'generateAtom' : 'generateRss';
        $strLink = $arrFeed['feedBase'] ?: Environment::get('url');
        $strFile = $arrFeed['feedName'];
        $bundles = \System::getContainer()->getParameter('kernel.bundles');

        $objFeed              = new Feed($strFile);
        $objFeed->link        = $strLink;
        $objFeed->title       = $arrFeed['title'];
        $objFeed->description = $arrFeed['description'];
        $objFeed->language    = $arrFeed['language'];
        $objFeed->published   = $arrFeed['tstamp'];

        if (!$arrFeed['maxItems'] > 0) {
            $arrFeed['maxItems'] = 0;
        }

        $objArticle = NewsModel::findPublishedByNewsSource(
            $arrFeed['df_newsSource'],
            $varId,
            $arrArchives,
            $arrFeed['maxItems'],
            0,
            [
                'df_newsSource' => $arrFeed['df_newsSource'],
                'order' => $order
            ]
        );


        // Parse the items
        if ($objArticle !== null) {
            $arrUrls = [];
            while ($objArticle->next()) {
                $jumpTo = $objArticle->getRelated('pid')->jumpTo;
                // No jumpTo page set (see #4784)
                if (!$jumpTo) {
                    continue;
                }
                // Get the jumpTo URL
                if (!isset($arrUrls[$jumpTo])) {
                    $objParent = PageModel::findWithDetails($jumpTo);
                    // A jumpTo page is set but does no longer exist (see #5781)
                    if ($objParent === null) {
                        $arrUrls[$jumpTo] = false;
                    } else {
                        $arrUrls[$jumpTo] = $objParent->getAbsoluteUrl(Config::get('useAutoItem') ? '/%s' : '/items/%s');
                    }
                }
                // Skip the event if it requires a jumpTo URL but there is none
                if ($arrUrls[$jumpTo] === false && $objArticle->source == 'default') {
                    continue;
                }
                $strUrl         = $arrUrls[$jumpTo];
                $objItem        = new FeedItem();
                $objItem->title = $objArticle->headline;
                if (isset($bundles['news_categories'])) {
                    $objItem->link = \NewsCategories\CategoryHelper::getCategoryNewsUrl($objArticle);
                } else {
                    $objItem->link = $this->getLink($objArticle, $strUrl);
                }

                if(str_contains($order,'tstamp')){
                    $objItem->published = $objArticle->tstamp;
                } else {
                    $objItem->published = $objArticle->date;
                }

                /** @var BackendUser $objAuthor */
                if (($objAuthor = $objArticle->getRelated('author')) !== null) {
                    $objItem->author = $objAuthor->name;
                }
                // Prepare the description
                if ($arrFeed['source'] == 'source_text') {
                    $strDescription = '';
                    $objElement     = \ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_news');
                    if ($objElement !== null) {
                        // Overwrite the request (see #7756)
                        $strRequest = \Environment::get('request');
                        \Environment::set('request', $objItem->link);
                        while ($objElement->next()) {
                            $strDescription .= $this->getContentElement($objElement->current());
                        }
                        \Environment::set('request', $strRequest);
                    }
                } else {
                    $strDescription = $objArticle->teaser;
                }
                $strDescription       = $this->replaceInsertTags($strDescription, false);
                $objItem->description = $this->convertRelativeUrls($strDescription, $strLink);
                // Add the article image as enclosure
                if ($objArticle->addImage) {
                    $objFile = \FilesModel::findByUuid($objArticle->singleSRC);
                    if ($objFile !== null) {
                        $objItem->addEnclosure($objFile->path, $strLink);
                    }
                }
                // Enclosures
                if ($objArticle->addEnclosure) {
                    $arrEnclosure = \StringUtil::deserialize($objArticle->enclosure, true);
                    if (is_array($arrEnclosure)) {
                        $objFile = \FilesModel::findMultipleByUuids($arrEnclosure);
                        if ($objFile !== null) {
                            while ($objFile->next()) {
                                $objItem->addEnclosure($objFile->path, $strLink);
                            }
                        }
                    }
                }
                $objFeed->addItem($objItem);
            }
        }
        return $objFeed;
    }

    /**
     * Generate an XML files and save them to the root directory
     *
     * @param array
     */
    protected function generateFiles($arrFeed)
    {
        // Don't generate xml-files for dynamic feeds
        if ($arrFeed["feedGeneration"] !== NewsFeedGenerator::FEEDGENERATION_DYNAMIC) {
            return parent::generateFiles($arrFeed);
        }
    }

}