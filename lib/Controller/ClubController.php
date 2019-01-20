<?php

namespace OCA\SPGVerein\Controller;

use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCA\SPGVerein\Model\MemberGroup;
use OCA\SPGVerein\Repository\Club;

class ClubController extends Controller {

    private $club;

    public function __construct($AppName, IRequest $request, Club $club) {
        parent::__construct($AppName, $request);
        $this->club = $club;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listMembers(string $club): \OCP\AppFramework\Http\JSONResponse {
        $members = $this->club->getAllMembers($club);
        return new JSONResponse($members);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listCities(string $club): JSONResponse {
        $cities = array();

        $members = $this->club->getAllMembers($club);

        foreach ($members as $member) {
            array_push($cities, $member->getCity());
        }

        $uniqueCities = array_unique($cities);
        sort($uniqueCities);
        return new JSONResponse($uniqueCities);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listClubs(): JSONResponse {
        return new JSONResponse($this->club->getAllClubs());
    }
}
