<?php

namespace OCA\SPGVerein\Repository;

class Club {

    private $startSymbols = "\x00\x00\x4c\x80";
    private $filename;

    function __construct() {
        $this->filename = "....";
    }

    public function getAllMembers() {
        $handle = fopen($this->filename, "rb") or die("Couldn't get handle");
        if ($handle) {

            $previous = "";
            $absolutePos = 0;
            while (($buffer = fgets($handle, 4)) !== false) {

                $absolutePos += 4;

                $symbolSearchBuffer = $previous . $buffer;
                $startSymbolPos = strpos($symbolSearchBuffer, $this->startSymbols);
                if ($startSymbolPos !== false) {

                    echo "Pos: $absolutePos...";

                    $memberData = fgets($handle, 3200 - (strlen($symbolSearchBuffer) - $startSymbolPos));

                    $absolutePos += strlen($memberData);

                    $memberId = substr($symbolSearchBuffer + $memberData, $startSymbolPos, 10);
                    //echo "found: $memberId";
                }

                $previous = $buffer;
                echo ".";
            }
            fclose($handle);
        }
    }

}
