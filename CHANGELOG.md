# Preparse Field Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 2.1.2 - 2024-07-12
### Fixed
 - Added namespace aliasing to prevent integrations with other plugins/modules from breaking

## 2.1.1 - 2024-07-12
### Fixed
 - Fixed a bug where the `preparseFieldService` could not be found([#100](https://github.com/jalendport/craft-preparse/issues/100))

## 2.1.0 - 2024-07-11
### Changed
- Migrated to `jalendport/craft-preparse`

## 2.0.2 - 2022-12-05
### Fixed
- Updated reference to Twigfield

## 2.0.1 - 2022-12-02
### Changed
- Updated to use craft-code-editor instead of craft-twigfield ([#87](https://github.com/jalendport/craft-preparse/pull/87) - thanks @khalwat)

## 2.0.0 - 2022-08-08
### Added
- Initial Craft 4 release

## 1.4.1 - 2022-12-02
### Changed
- Updated to use craft-code-editor instead of craft-twigfield ([#86](https://github.com/jalendport/craft-preparse/pull/86) - thanks @khalwat)

## 1.4.0 - 2022-08-08
### Added
- Added support for craft-twigfield ([#81](https://github.com/jalendport/craft-preparse/pull/81) - thanks @khalwat)

## 1.3.0 - 2022-08-06
### Added
- Added datetime column type option ([#63](https://github.com/jalendport/craft-preparse/pull/63) - thanks @mmikkel)

## 1.2.5 - 2021-07-02
### Fixed
- Reverted [#66](https://github.com/jalendport/craft-preparse/pull/66) due to bug where sometimes the element couldn't be re-fetched from the database ([#70](https://github.com/jalendport/craft-preparse/issues/70), [#71](https://github.com/jalendport/craft-preparse/issues/71), [#72](https://github.com/jalendport/craft-preparse/issues/72), [#73](https://github.com/jalendport/craft-preparse/issues/73))
- Fixed a bug causing missing Matrix blocks on elements in certain cases ([#69](https://github.com/jalendport/craft-preparse/issues/69))

## 1.2.4 - 2021-02-24
### Fixed
- Fixed a bug preventing elements from saving successfully in certain multisite setups ([#70](https://github.com/jalendport/craft-preparse/pull/70))

## 1.2.3 - 2021-02-23
### Fixed
- Fixed a bug causing missing Matrix blocks on new elements ([#66](https://github.com/jalendport/craft-preparse/pull/66) - thanks @monachilada)

## 1.2.2 - 2020-11-30
### Fixed
- Fixed a bug causing missing Matrix blocks on revisions ([#65](https://github.com/jalendport/craft-preparse/pull/65) - thanks @brandonkelly)

## 1.2.1 - 2020-06-25
### Fixed
- Fixed incorrect branch names in README and composer.json

## 1.2.0 - 2020-06-25
Transfer of ownership...

### Added
- Added a class alias so sites with Preparse currently installed will continue to function smoothly after the namespace change

## 1.1.0 - 2019-08-03
### Fixed
- Fixes compability issues with Craft 3.2 (Thanks, @brandonkelly).

### Added
- Added `SortableFieldInterface` to field type.

### Changed
- Changed composer requirement for `craftcms/cms` to `^3.2.0`.

## 1.0.7 - 2019-08-03
### Changed
- Replaced `unset()` on `$_FILES` with setting it to an empty array (fixes #52).

## 1.0.6 - 2019-03-21
### Fixed
- Fixed a bug where warnings weren’t showing up when editing an existing preparse field’s column type.

## 1.0.5.1 - 2019-02-27
### Fixed
- Fixed an error that occurred when updating to preparse 1.0.5 on Craft 3.0.x

## 1.0.5 - 2019-02-27
### Added
- Adds Craft 3 migrations. (thanks @carlcs). 

## 1.0.4 - 2018-12-16
### Added
- Adds support for showing preparse fields in element indexes (#33) (thanks @benface). 

## 1.0.3 - 2018-10-24
### Fixed
- Fixed an issue (#45) that would occure when uploading files through a front-end form for elements with a preparse field (thanks @aaronwaldon and @ademers). 

## 1.0.2 - 2018-08-01
### Fixed
- Fixed a bug that would keep preparse fields on assets from parsing on first save/upload (#37). 
- Fixes a bug where preparse fields could not be hidden in asset element modals and matrixblocks.

## 1.0.1 - 2018-07-30
### Added
- Added support for DECIMAL column types.

### Fixed
- Fixed an issue that would result in a duplicate key exception in multisite installations. 

## 1.0.0 - 2017-12-02
### Added
- Initial Craft 3 release.
