<?php

namespace OCA\SPGVerein\Model;

use JsonSerializable;

class Member implements JsonSerializable {

    private $id;
    private $salutation;
    private $title;
    private $firstname;
    private $lastname;
    private $street;
    private $zipcode;
    private $city;
    private $relatedMemberId;

    function __construct(string $memberData) {
        $memberDataUtf8 = mb_convert_encoding($memberData, 'UTF-8', 'ISO-8859-1');

        // These lines ensures that data after lastname is aligned correctly:
        // if lastname contains none ascii code following fields need to be shifted
        $zipCode = substr($memberDataUtf8, 200, 6);
        $shift = $zipCode[0] === ' ' ? 1 : 0;
        $shift += $zipCode[1] === ' ' ? 1 : 0;
        $shift += $zipCode[2] === ' ' ? 1 : 0;

        // Same as above...
        $relatedId = substr($memberDataUtf8, 1432 + $shift, 10);
        $shift2 = $relatedId[0] === ' ' ? 1 : 0;
        $shift2 += $relatedId[1] === ' ' ? 1 : 0;
        $shift2 += $relatedId[2] === ' ' ? 1 : 0;

        $this->id = (int) substr($memberDataUtf8, 0, 10);
        $this->salutation = trim(substr($memberDataUtf8, 10, 15));
        $this->title = trim(substr($memberDataUtf8, 25, 35));
        $this->firstname = trim(substr($memberDataUtf8, 60, 35));
        $this->lastname = trim(substr($memberDataUtf8, 95, 70));
        $this->street = trim(substr($memberDataUtf8, 165, 35));
        $this->zipcode = substr($memberDataUtf8, 200 + $shift, 5);
        $this->city = trim(substr($memberDataUtf8, 205 + $shift, 40));
        $this->relatedMemberId = (int) substr($memberDataUtf8, 1432 + $shift + $shift2, 10);
    }

    public function jsonSerialize() {
        return array(
            "fullnames" => array($this->getFullname()),
            "street" => $this->street,
            "zipcode" => $this->zipcode,
            "city" => $this->city,
        );
    }

    public function getFullname(): string {
        $persons = $this->salutation;

        if ($persons !== "") {
            $persons .= " ";
        }

        $persons .= $this->title;

        if ($persons !== "") {
            $persons .= " ";
        }

        $persons .= $this->firstname . " " . $this->lastname;
        return $persons;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getStreet(): string {
        return $this->street;
    }

    public function getZipcode(): string {
        return $this->zipcode;
    }

    public function getCity(): string {
        return $this->city;
    }

    public function belongsToMember(): bool {
        return $this->relatedMemberId > 0;
    }

    public function getRelatedMemberId(): int {
        return $this->relatedMemberId;
    }

}
