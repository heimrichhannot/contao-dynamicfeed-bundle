# Changelog

## [1.1.0] - 2017-09-04

## Added
* Hook `dynamicfeedBeforeGeneration`

## Changed 
* renamed database tables
* default value for link tag is now Enviroment::get('url') instead of Enviroment::get('base') (corresponding with RSS standard). This value is overwritten by the base url value that can set in feed setting and with the newly introduced hook

### Fixed
* news source field always visible

## [1.0.3] - 2017-09-01

### Fixed
* entity not defined xml error
* variable name spelling mistakes

## [1.0.2] - 2017-08-31

### Fixed
* dynamic feed ignore selected news archives

## [1.0.1] - 2017-08-23

### Changed
* updated FeedSourceInterface annotations
* updated NewsModel annotations

## [1.0.0] - 2017-08-23

###Added
* decoubled dynamic newsfeed from [contao-news-bundle](https://github.com/heimrichhannot/contao-news-bundle)
* NewsArchiveFeedSource as example implementation