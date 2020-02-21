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
    public function downloadLabels(string $club): StreamResponse
    {
        $members = $this->club->getAllMembers($club);

        $cities = str_getcsv(urldecode($this->request->getParam("cities", "")));
        error_log("cities " . implode(" ", $cities));
        $members = array_filter($members, function ($member) use ($cities) {
            return in_array($member->getCity(), $cities);
        });

		$resignedMembers = filter_var(urldecode($this->request->getParam("resignedMembers", "false")), FILTER_VALIDATE_BOOLEAN);
		$members = array_filter($members, function ($member) use ($resignedMembers) {
			return $member->getResignationDate() == null || $resignedMembers;
		});

        $format = trim(urldecode($this->request->getParam("format", "L7163")));
        $pdf = new Labels($format);
        $pdf->AddPage();

        $addressLine = trim(urldecode($this->request->getParam("addressLine", "")));
        $groupMembers = filter_var(urldecode($this->request->getParam("groupMembers", "false")), FILTER_VALIDATE_BOOLEAN);

        $offset = intval(urldecode($this->request->getParam("offset", "0")));
        for ($i = 0; $i < $offset; ++$i) {
            $pdf->Add_Label('', '');
        }

        $textFormat = "%s\n%s\n\n%s %s";
        if ($groupMembers) {
            $member_groups = MemberGroup::groupByRelatedMemberId($members);

            foreach ($member_groups as $mg) {
                $text = sprintf($textFormat,
                    implode("\n", $mg->getFullnames()),
                    $mg->getStreet(),
                    $mg->getZipcode(),
                    $mg->getCity()
                );
                $pdf->Add_Label($text, $addressLine);
            }
        } else {
            foreach ($members as $m) {
                $text = sprintf($textFormat,
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
