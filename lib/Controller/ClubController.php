<?php

namespace OCA\SPGVerein\Controller;

use OCP\AppFramework\Controller;
use OCA\SPGVerein\Repository\Club;

class ClubController extends Controller {

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function listMembers() {
        $club = new Club();

        return $club->getAllMembers();
    }

}
