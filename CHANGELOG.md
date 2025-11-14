# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [5.1.0] - 2025-11-14

### Changed
- Replaced deprecated `Html::encodeJsVar()` with `FormatJson::encode()` for MediaWiki 1.41+ compatibility
- Updated use statements to import `MediaWiki\Json\FormatJson`
- Removed unused `MediaWiki\Html\Html` import

### Added
- Added note to README about [Miraheze/MatomoAnalytics](https://github.com/Miraheze/MatomoAnalytics) as an actively maintained alternative

### Fixed
- Fixed deprecation warnings when using MediaWiki 1.41 and newer versions

## [5.0.0] - 2023-10-27

### Changed
- Compatibility with MediaWiki 1.44
- Use Html instead of Xml for better compatibility
