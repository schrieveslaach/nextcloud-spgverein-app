<?php

namespace OCA\SPGVerein\Controller;

require_once('fpdf.php');

use OCA\SPGVerein\Model\MemberGroup;
use OCA\SPGVerein\Repository\Club;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\StreamResponse;
use OCP\IRequest;

class LabelController extends Controller
{
    private $club;

    public function __construct($AppName, IRequest $request, Club $club)
    {
        parent::__construct($AppName, $request);
        $this->club = $club;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function downloadLabels(string $club, string $city): Response
    {
        $members = $this->club->getAllMembers($club);
        $members = array_filter($members, function ($member) use ($city) {
            return $member->getCity() === $city;
        });

        $pdf = new Labels('L7163');
        $pdf->AddPage();

        $member_groups = MemberGroup::groupByRelatedMemberId($members);

        foreach ($member_groups as $mg) {
            $text = sprintf("%s\n%s\n%s %s",
                implode(" ", $mg->getFullnames()),
                $mg->getStreet(),
                $mg->getZipcode(),
                $mg->getCity()
            );
            $pdf->Add_Label($text);
        }

        $path = tempnam(sys_get_temp_dir(), 'spgverein-');
        $pdf->Output('F', $path, true);

        $response = new StreamResponse($path);
        $response->addHeader("Content-type", "application/pdf");
        return $response;
    }

}