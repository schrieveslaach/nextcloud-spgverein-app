<?php

namespace OCA\SPGVerein\Controller;

use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCA\SPGVerein\Repository\Club;
use OCA\SPGVerein\Repository\ClubException;
use OCP\IURLGenerator;

class ClubController extends Controller {

    private $club;

    /** @var IURLGenerator */
    private $urlGenerator;

    public function __construct($AppName, IRequest $request, Club $club, IURLGenerator $urlGenerator) {
        parent::__construct($AppName, $request);
        $this->club = $club;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listMembers(string $club): \OCP\AppFramework\Http\JSONResponse {
        try {
            $members = $this->club->getAllMembers($club);
            return new JSONResponse($members);
        }
        catch(ClubException $e) {
            $r = new JSONResponse([
                "type" => "https://httpstatuses.com/500",
                "detail" => $e->getMessage()
            ], 500);
            $headers = $r->getHeaders();
            $headers["Content-Type"] = "application/problem+json";
            $r->setHeaders($headers);
            return $r;
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listClubs(): JSONResponse {
        $urlGenerator = $this->urlGenerator;
        return new JSONResponse(array_map(function($club) use ($urlGenerator) {
            $club['link'] = $urlGenerator->linkToRoute('files.view.index', ['fileid' => $club['id']]);
            return $club;
        }, $this->club->getAllClubs()));
    }
}
