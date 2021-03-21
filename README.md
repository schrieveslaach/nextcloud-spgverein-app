[![pipeline status](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/badges/master/pipeline.svg)](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/commits/master)
[![Download](https://img.shields.io/badge/download-spgverein.tar.gz-blue.svg)](https://gitlab.com/schrieveslaach/nextcloud-spgverein-app/-/jobs/artifacts/master/raw/spgverein.tar.gz?job=package)

<script src="https://liberapay.com/schrieveslaach/widgets/button.js"></script>
<noscript><a href="https://liberapay.com/schrieveslaach/donate"><img alt="Donate using Liberapay" src="https://liberapay.com/assets/widgets/donate.svg"></a></noscript>

# SPG Verein

[SPG-Verein](https://spg-direkt.de/) is a proprietary windows program for managing club members. For example, this program supports a club to generate direct debits. The nextcloud app *spgverein* implements additional behaviour for the club. 

- [x] Web-Interface to view all members of the club
  - [x] Access the member attachments
- [x] Download address lists as PDF to print them on labels
- [x] Export members as ODS file
- [ ] Provide all members as contacts in the addressbook
- [ ] Provide all member birth dates in the calendar
- [x] Support of SPG-Verein version 3
- [x] Support of SPG-Verein version 4

## Screenshots

![Screenshot SPG Verein](assets/screencast.gif)

## Usage

Install the Nextcloud app [*spgverein* from the app store](https://apps.nextcloud.com/apps/spgverein) and synchronize the folder that contains the files that either end with `*mitgl.dat` or that start with `spg_verein_` and have the extension `.mdf`. Then, you will be able to access your club members in the Nextcloud UI.
