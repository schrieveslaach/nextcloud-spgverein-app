<?php

namespace OCA\SPGVerein\Controller;

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