<?php

namespace OCA\SPGVerein\Controller;

require_once('fpdf.php');

use OCA\SPGVerein\Model\MemberGroup;
use OCA\SPGVerein\Repository\Club;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
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
    public function formats(): JSONResponse
    {
        return new JSONResponse(array_map(function ($format) {
            return array(
                'size' => $format['paper-size'],
                'columns' => $format['NX'],
                'rows' => $format['NY']
            );
        }, Labels::$_Avery_Labels));
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function downloadLabels(string $club, string $city): StreamResponse
    {
        $members = $this->club->getAllMembers($club);
        $members = array_filter($members, function ($member) use ($city) {
            return $member->getCity() === $city;
        });

        usort($members, function ($a, $b) {
            $m1 = array();
            $m2 = array();
            preg_match('/(.*)\s+((\d+)\s*([a-z])?)/', $a->getStreet(), $m1);
            preg_match('/(.*)\s+((\d+)\s*([a-z])?)/', $b->getStreet(), $m2);

            $cmp = strcmp($m1[1], $m2[1]);
            if ($cmp === 0) {
                $n1 = intval($m1[3]);
                $n2 = intval($m2[3]);

                if ($n1 < $n2)
                    $cmp = -1;
                else if ($n1 > $n2)
                    $cmp = 1;
                else
                    $cmp = 0;
            }

            return $cmp;
        });

        $format = trim(urldecode($this->request->getParam("format", "L7163")));
        $pdf = new Labels($format);
        $pdf->AddPage();

        $addressLine = trim(urldecode($this->request->getParam("addressLine", "")));
        $groupMembers = filter_var(urldecode($this->request->getParam("groupMembers", "false")), FILTER_VALIDATE_BOOLEAN);
        if ($groupMembers) {
            $member_groups = MemberGroup::groupByRelatedMemberId($members);

            foreach ($member_groups as $mg) {
                $text = sprintf("%s\n%s\n%s %s",
                    implode("\n", $mg->getFullnames()),
                    $mg->getStreet(),
                    $mg->getZipcode(),
                    $mg->getCity()
                );
                $pdf->Add_Label($text, $addressLine);
            }
        } else {
            foreach ($members as $m) {
                $text = sprintf("%s\n%s\n%s %s",
                    $m->getFullname(),
                    $m->getStreet(),
                    $m->getZipcode(),
                    $m->getCity()
                );
                $pdf->Add_Label($text, $addressLine);
            }
        }

        $path = tempnam(sys_get_temp_dir(), 'spgverein-');
        $pdf->Output('F', $path, true);

        $response = new StreamResponse($path);
        $response->addHeader("Content-type", "application/pdf");
        return $response;
    }

}