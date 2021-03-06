# Changelog

## [1.1.5] - 2018-11-28

#### Fixed
- replace `sensio/framework-extra-bundle` route handling in controller with symfony Route annotation

## [1.1.4] - 2017-12-07

### Added
* `heimrichhannot/contao-news_categories` support to provide category tree news urls

## [1.1.3] - 2017-09-05

### Changed
* removed contao/config folder

## [1.1.2] - 2017-09-04 

### Changed
* renamed `HeimrichHannot\NewsBundle\NewsModel` to `HeimrichHannot\NewsBundle\Model\NewsModel`

## [1.1.1] - 2017-09-04

### Fixed
* empty feeds
* always returning same items

## [1.1.0] - 2017-09-04

### Added
* Hook `dynamicfeedBeforeGeneration`

### Changed 
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

