<?php

namespace OCA\SPGVerein\Controller;

use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;
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
            return new JSONResponse($this->groupMembersByRelatedId($members));
        }

        return NULL;
    }

    private function groupMembersByRelatedId($members): array {
        $membersByRelatedId = array();

        foreach ($members as $member) {
            $id = $member["related-id"];
            if ($id > 0) {
                if (isset($membersByRelatedId[$id])) {
                    $membersByRelatedId[$id][] = $member;
                } else {
                    $membersByRelatedId[$id] = array($member);
                }
                unset($members[$member["id"]]);
            }
        }

        foreach (array_keys($membersByRelatedId) as $relatedId) {
            array_unshift($membersByRelatedId[$relatedId], $members[$relatedId]);
            unset($members[$relatedId]);
        }

        foreach ($members as $member) {
            $id = $member["id"];
            $membersByRelatedId[$id] = array($member);
        }

        return $this->unifyMembers($membersByRelatedId);
    }

    private function unifyMembers($members): array {
        $unifiedMembers = array();

        foreach ($members as $key => $member) {
            $unifiedMember = array(
                "id" => $key,
                "salutation" => "",
                "title" => "",
                "firstname" => "",
                "lastname" => $member[0]["lastname"],
                "street" => $member[0]["street"],
                "zipcode" => $member[0]["zipcode"],
                "city" => $member[0]["city"],
            );

            $length = sizeof($member);
            for ($i = 0; $i < $length; ++$i) {
                if ($i > 0) {
                    $unifiedMember["firstname"] = $unifiedMember["firstname"] . ", " . $member[$i]["firstname"];
                } else {
                    $unifiedMember["firstname"] = $member[$i]["firstname"];
                }
            }

            $unifiedMembers[$key] = $unifiedMember;
        }

        usort($unifiedMembers, function($a, $b) {
            return $a['lastname'] <=> $b['lastname'];
        });

        return $unifiedMembers;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listCities(): \OCP\AppFramework\Http\JSONResponse {
        $cities = array();

        $members = $this->club->getAllMembers();

        foreach ($members as $member) {
            array_push($cities, $member["city"]);
        }

        $uniqueCities = array_unique($cities);
        sort($uniqueCities);
        return new JSONResponse($uniqueCities);
    }

}
