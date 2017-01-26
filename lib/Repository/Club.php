<?php

namespace OCA\SPGVerein\Repository;

use OCP\AppFramework\Http\JSONResponse;

class Club {

    private $startSymbols = "\x00\x00\x4c\x80";
    private $filename;

    function __construct() {
        $this->filename = "....";
    }

    public function getAllMembers() {
        $members = array();

        $handle = fopen($this->filename, "rb") or die("Couldn't get handle");
        if ($handle) {

            $previous = "";
            while (($buffer = fgets($handle, 5)) !== false) {

                $symbolSearchBuffer = $previous . $buffer;
                $startSymbolPos = strpos($symbolSearchBuffer, $this->startSymbols);
                if ($startSymbolPos !== false) {

                    $memberData = substr($symbolSearchBuffer, $startSymbolPos + strlen($this->startSymbols));
                    $memberData .= fgets($handle, 3200 - strlen($memberData));

                    $member = array(
                        "id" => (int) substr($memberData, 0, 10),
                        "title" => trim(substr($memberData, 10, 15)),
                    );

                    array_push($members, $member);
                }

                $previous = $buffer;
            }
            fclose($handle);
        }

        return new JSONResponse($members);
    }

}
