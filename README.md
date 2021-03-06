# Contao dynamic feed bundle

Generates an dynamic feed for your news articles.

## Requires
* Contao 4.4
* PHP7

## Usage

### Dynamic rss feed

The dynamic rss feed is based on feed sources and their channels. Feed sources can be categories, tags, etc. A channel can be a single categorie, a single tag, e.g.

#### Register a feed source
1. Your class must implement `FeedSourceInterface`
2. Create a service for your class and add the `hh.dynamicfeed.feed_source` tag.
3. Create a new feed in the Contao-Backend (News -> Create Feed), select dynamic feed and the corresponding feed source.

```
// Example for codefog/tags-bundle
// services.yml

HeimrichHannot\CustomBundle\FeedSources\TagFeedSource:
        tags: [hh.dynamicfeed.feed_source]
```

The bundle will then add following routes for your feed source:
* `/share/[feedAlias|feedId]`
* `/share/[feedAlias|feedId]/[channelId|channelAlias]`
* `/share/[feedAlias|feedId]/source_channels` (list of available channels)