# Changelog
Alle relevanten Änderungen des Projektes werden in dieser Datei dokumentiert

## [Unreleased]
### Added
- Verlinkung zwischen den Dateien und Vereinen wurde hinzugefügt ([#29](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/29))
### Changed
- Update der abhängigen Bibliotheken
- Kompatibilität mit Nextcloud 22 und 23

## 0.10.3 – 2021-04-18
### Changed
- Bug-Fix: es wird sichergestellt, dass der Parser ausführbar ist ([#27](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/27))

## 0.10.2 – 2021-03-28
### Changed
- Bug-Fix: Release-Package wurde korrigiert ([#23](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/23)). Ältere Nextcloud-Versionen unterstützten Symlinks innerhalb der Release-Pakete.

## 0.10.1 – 2021-03-28
### Changed
- Bug-Fix: Link zu Anhängen korrigiert

## 0.10.0 – 2021-03-28
### Added
- Verwendung von Nextclouds eingebauter Suche ([#25](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/25))
### Changed
- Bug-Fix: Textdarstellung auf kleinen Geräten ([#21](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/21))

## 0.9.0 – 2021-03-21
### Added
- Changelog wurde übersetzt ([#15](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/15))
- Vereine werden anhand von Events verarbeitet und die Ergebnisse werden im App-Folder zwischen gespeichert ([#17](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/17))
- Support von SPG-Verein 4 hinzugefügt ([#3](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/3)).
### Changed
- Bug-Fix: Gleichzeitiges Exportieren von ODT-Dateien korrigiert ([#18](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/18))
- Bug-Fix: Zeilenrand wird richtig dargestellt, wenn Tabellenzellen unterschiedlich sind ([#21](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/21))

## 0.8.1 – 2021-01-31
### Changed
- Bug-Fix: Die Etiketten-Druchansicht zeigte die Zweckform-ID nicht an
- Bug-Fix: Das Parsen der verknüpften Mitgliedsnummer wertete nicht alle Bytes aus

## 0.8.0 – 2020-12-20
### Added
- Kompatibilität mit Nextcloud 20 hergestellt ([#12](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/12))
- Changelog in den App-Informationen in "Liste der Veränderungen" hinzugefügt ([#14](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/14))
- Fehlerbehandlung während des Parsens und Ausgabe von Fehlern im Frontend hinzugefügt ([#13](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/13))
- Etikettendruck einzelner Mitglieder

### Changed
- Update der abhängigen Bibliotheken

## 0.7.2 – 2020-09-05
### Added
- App-Informationen zu `info.xml` und zum Web-Interface hinzugefügt ([#11](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/11)).
- Update der abhängigen Bibliotheken

## 0.7.1 – 2020-09-01
### Added
- FPDF via Composer installiert
- Update der abhängigen Bibliotheken

### Changed
- Fehler beim Export bei fehlender Anrede korrigiert

## 0.7.0 – 2020-08-10
### Added
- Update der abhängigen Bibliotheken
- Abhärtung beim Parsen von SPG-Dateien ([#5](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/5)).

## 0.6.0 – 2020-05-28
### Added
- Verbesserung UI: an Nextcloud Design angepasst
- Anzeige bei nicht vorhandenen Mitgliedsdaten

## 0.5.1 – 2020-05-28
### Added
- Fehlerbehebung App-Signatur ([#2](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/issues/2))

## 0.5.0 – 2020-01-01
### Added
- Parsen von `mitgl.dat` als Mitglieder
- Gruppierung von Mitgliedern mit verwandter Mitglieds-ID 
- Anzeige der Mitglieder im Web-Interface nach Adresse sortiert
- Download Mitglieder als CSV
