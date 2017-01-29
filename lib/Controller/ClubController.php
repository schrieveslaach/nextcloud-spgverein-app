<?php

namespace OCA\SPGVerein\Controller;

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
    public function listMembers(): \OCP\AppFramework\Http\JSONResponse {
        return $this->club->getAllMembers();
    }

}
