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
    public function listMembers($grouping): \OCP\AppFramework\Http\JSONResponse {
        $members = $this->club->getAllMembers();

        if ($grouping === "none") {
            return new JSONResponse($members);
        } elseif ($grouping === "related-id") {
            return new JSONResponse(
                    MemberGroup::groupByRelatedMemberId($members)
            );
        }

        return NULL;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listCities(): \OCP\AppFramework\Http\JSONResponse {
        $cities = array();

        $members = $this->club->getAllMembers();

        foreach ($members as $member) {
            array_push($cities, $member->getCity());
        }

        $uniqueCities = array_unique($cities);
        sort($uniqueCities);
        return new JSONResponse($uniqueCities);
    }

}
