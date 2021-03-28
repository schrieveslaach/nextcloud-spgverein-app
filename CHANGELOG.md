# Changelog
All notable changes to this project will be documented in this file.

## 0.10.1 – 2021-03-28
### Changed
- Fixed: broken attachment link

## 0.10.0 – 2021-03-28
### Added
- Use Nextcloud's unified search API ([#25](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/25))
### Changed
- Fixed: text wrapping on small devices ([#21](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/21))

## 0.9.0 – 2021-03-21
### Added
- Translated changelog ([#15](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/15))
- Parse the clubs/members on node events to store the results in the app data folder ([#17](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/17))
- Add support of parsing version 4 files ([#3](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/3)).
### Changed
- Fixed: simultaneous ODT export ([#18](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/18))
- Fixed: row border when tables cells have different heights ([#21](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/21))

## 0.8.1 – 2021-01-31
### Changed
- Fixed: The label printing UI did not show the format ID
- Fixed: The parser did not parsed all bytes of the related member ID

## 0.8.0 – 2020-12-20
### Added
- Nextcloud 20 compatibility ([#12](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/12))
- Add changelog information to distribution ([#14](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/14))
- Add error handling during parsing and display errors to the user ([#13](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/13))
- Label printing of single member

### Changed
- Update library dependencies

## 0.7.2 – 2020-09-05
### Added
- Add usage instructions to `info.xml` and to the web interface (#11).
- Update library dependencies

## 0.7.1 – 2020-09-01
### Added
- Install FPDF via composer
- Fix export error when salutation is empty
- Update library dependencies

## 0.7.0 – 2020-08-10
### Added
- Update JS Dependencies
- Make parsing of SPG files more robust ([#5](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/5)).

## 0.6.0 – 2020-05-28
### Added
- Improve UI: align it with Nextcloud design
- Show that there is no data aivalable to display.

## 0.5.1 – 2020-05-28
### Added
- Fix app signing ([#2](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/2))

## 0.5.0 – 2020-01-01
### Added
- Parse `mitgl.dat` file as members
- Group members by related member id
- Show members in web interface sorted by districts
- Download members as CSV
