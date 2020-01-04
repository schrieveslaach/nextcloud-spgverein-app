<?php

namespace OCA\SPGVerein\Model;

use JsonSerializable;

class Member implements JsonSerializable
{

    private $id;
    private $salutation;
    private $title;
    private $firstname;
    private $lastname;
    private $street;
    private $zipcode;
    private $city;
    private $relatedMemberId;

    private $birth;
    private $admissionDate;

    private $files;

    function __construct(string $memberData)
    {
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

        $this->id = (int)substr($memberDataUtf8, 0, 10);
        $this->salutation = trim(substr($memberDataUtf8, 10, 15));
        $this->title = trim(substr($memberDataUtf8, 25, 35));
        $this->firstname = trim(substr($memberDataUtf8, 60, 35));
        $this->lastname = trim(substr($memberDataUtf8, 95, 70));
        $this->street = trim(substr($memberDataUtf8, 165, 35));
        $this->zipcode = substr($memberDataUtf8, 200 + $shift, 5);
        $this->city = trim(substr($memberDataUtf8, 205 + $shift, 40));
        $this->relatedMemberId = (int)substr($memberDataUtf8, 1432 + $shift + $shift2, 10);

        $i = 0;
        $re = '/\d{2}\.\d{2}\.\d{4}/m';
        while (!preg_match($re, substr($memberDataUtf8, 305 + $shift + $shift2 + $i, 10))) {
            $i = $i - 1;
        }
        $this->birth = substr($memberDataUtf8, 305 + $shift + $shift2 + $i, 10);
        $this->admissionDate = substr($memberDataUtf8, 419 + $shift + $shift2 + $i, 10);

        $this->files = array();
    }

    public function jsonSerialize()
    {
        return array(
            "id" => $this->id,
            "fullnames" => array($this->getFullname()),
            "street" => $this->street,
            "zipcode" => $this->zipcode,
            "city" => $this->city,
            "birth" => $this->birth,
            "admissionDate" => $this->admissionDate,
            "files" => $this->jsonFiles()
        );
    }

    private function jsonFiles(): array
    {
        $files = array();

        foreach ($this->files as $file) {
            array_push($files, $file->getName());
        }

        return $files;
    }

    public function getFullname(): string
    {
        $persons = $this->salutation;

        if ($persons !== "") {
            $persons .= " ";
        }

        $persons .= $this->title;

        if ($persons !== "") {
            $persons .= " ";
        }

        return $persons . $this->firstname . " " . $this->lastname;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSalutation(): string
    {
        return $this->salutation;
    }

    public function getBirth()
    {
        return $this->birth;
    }

    public function getAdmissionDate()
    {
        return $this->admissionDate;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function belongsToMember(): bool
    {
        return $this->relatedMemberId > 0;
    }

    public function getRelatedMemberId(): int
    {
        return $this->relatedMemberId;
    }

    public function addFile($file)
    {
        array_push($this->files, $file);
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
