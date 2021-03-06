<?php

namespace OCA\SPGVerein\Controller;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\Response;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCA\SPGVerein\Repository\Club;

class FileController extends Controller {

    private $club;

    public function __construct($AppName, IRequest $request, Club $club) {
        parent::__construct($AppName, $request);
        $this->club = $club;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function exportClub(string $club): Response {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        $writer = WriterEntityFactory::createODSWriter();
        $writer->openToFile($path);

        $members = $this->club->getAllMembers($club);
        usort($members, function ($a, $b) {
            return strcmp($a->getId(), $b->getId());
        });

        $values = [
            "ID",
            "Anrede",
            "Titel",
            "Nachname",
            "Vorname",
            "Geburtsdatum",
            "Eintrittsdatum",
            "Austrittsdatum",
            "Straße",
            "Postleitzahl",
            "Ort",
        ];
        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
        $writer->addRow($rowFromValues);

        foreach ($members as $member) {
            $values = [
                WriterEntityFactory::createCell($member->getId()),
                WriterEntityFactory::createCell($member->getSalutation()),
                WriterEntityFactory::createCell($member->getTitle()),
                WriterEntityFactory::createCell($member->getLastname()),
                WriterEntityFactory::createCell($member->getFirstname()),
                WriterEntityFactory::createCell($member->getBirth()),
                WriterEntityFactory::createCell($member->getAdmissionDate()),
                WriterEntityFactory::createCell($member->getResignationDate()),
                WriterEntityFactory::createCell($member->getStreet()),
                WriterEntityFactory::createCell($member->getZipcode()),
                WriterEntityFactory::createCell($member->getCity()),
            ];
            $rowFromValues = WriterEntityFactory::createRow($values);
            $writer->addRow($rowFromValues);
        }
        $writer->close();

        $content = file_get_contents($path);
        fclose($file);
        $response = new DataDownloadResponse($content, $club . ".ods","application/vnd.oasis.opendocument.spreadsheet");
        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function downloadFile(string $club, string $memberId, string $filename): Response {

        foreach ($this->club->getAllMembers($club) as $member) {

            if ($member->getId() === intval($memberId)) {
                foreach ($member->getFiles() as $file) {
                    if ($file->getName() === $filename) {

                        $response = new FileDisplayResponse($file);
                        $response->setHeaders(array(
                           "Content-type" => $file->getMimeType()
                        ));
                        return $response;
                    }
                }
            }
        }

        return new NotFoundResponse();
    }
}
