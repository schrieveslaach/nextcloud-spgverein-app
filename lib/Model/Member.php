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
    private $resignationDate;

    private $files;

    function __construct($jsonData)
    {
        $this->id = (int) $jsonData->id;
        if(isset($jsonData->salutation)) {
            $this->salutation = $jsonData->salutation;
        }
        if(isset($jsonData->title)) {
            $this->title = $jsonData->title;
        }
        if(isset($jsonData->firstName)) {
            $this->firstname = $jsonData->firstName;
        }
        if(isset($jsonData->lastName)) {
            $this->lastname = $jsonData->lastName;
        }
        if(isset($jsonData->street)) {
            $this->street = $jsonData->street;
        }
        if(isset($jsonData->zipcode)) {
            $this->zipcode = $jsonData->zipcode;
        }
        if(isset($jsonData->city)) {
            $this->city = $jsonData->city;
        }
        if(isset($jsonData->relatedMemberId)) {
            $this->relatedMemberId = $jsonData->relatedMemberId;
        }

        if(isset($jsonData->birth)) {
            $this->birth = $jsonData->birth;
        }
        if(isset($jsonData->admissionDate)) {
            $this->admissionDate = $jsonData->admissionDate;
        }
        if(isset($jsonData->resignationDate)) {
            $this->resignationDate = $jsonData->resignationDate;
        }
        if(isset($jsonData->bankAccount)) {
            $this->bankAccount = new BankAccount($jsonData->bankAccount);
        }

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
            "resignationDate" => $this->resignationDate,
            "bankAccount" => $this->bankAccount,
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
        $persons = trim($this->salutation);

        if (trim($persons) !== "") {
            $persons .= " ";
        }

        $persons .= trim($this->title);

        if (trim($persons) !== "") {
            $persons .= " ";
        }

        return trim($persons . $this->firstname . " " . $this->lastname);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSalutation()
    {
        return $this->salutation;
    }

    public function getBirth()
    {
    	if(empty($this->birth)) {
    		return null;
		}
        return \DateTime::createFromFormat('Y-m-dT', $this->birth);
    }

    public function getAdmissionDate()
    {
    	if(empty($this->admissionDate)) {
    		return null;
		}
        return \DateTime::createFromFormat('Y-m-dT', $this->admissionDate);
    }

    public function getResignationDate()
    {
        if (empty($this->resignationDate)) {
            return null;
        }
        return \DateTime::createFromFormat('Y-m-dT', $this->resignationDate);
	}

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function getCity(): ?string
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
