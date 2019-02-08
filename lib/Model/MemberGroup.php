<?php

namespace OCA\SPGVerein\Model;

use JsonSerializable;

class MemberGroup implements JsonSerializable
{

    private $memberId;
    private $members;

    function __construct($memberId)
    {
        $this->memberId = $memberId;
        $this->members = array();
    }

    public function jsonSerialize()
    {
        if (sizeof($this->members) === 1) {
            return $this->members[0]->jsonSerialize();
        }

        return array(
            "id" => $this->memberId,
            "fullnames" => $this->getFullnames(),
            "street" => $this->members[0]->getStreet(),
            "zipcode" => $this->members[0]->getZipcode(),
            "city" => $this->members[0]->getCity(),
        );
    }

    public function getStreet(): string
    {
        return $this->members[0]->getStreet();
    }

    public function getZipcode(): string
    {
        return $this->members[0]->getZipcode();
    }

    public function getCity(): string
    {
        return $this->members[0]->getCity();
    }

    public function getFullnames(): array
    {
        $persons = array();

        if (count($this->members) === 1 || !$this->haveAllMembersCommonLastnames()) {
            foreach ($this->members as $member) {
                array_push($persons, $member->getFullname());
            }
        } else {
            $persons = array($this->members[0]->getLastname());
            $firstnames = "";

            $cnt = count($this->members);
            foreach ($this->members as $i => $member) {
                if ($firstnames !== "") {
                    if ($i + 1 == $cnt) {
                        $firstnames .= " & ";
                    } else {
                        $firstnames .= ", ";
                    }
                }

                $firstnames .= $member->getFirstname();
            }

            array_push($persons, $firstnames);
        }
        return $persons;
    }

    private function haveAllMembersCommonLastnames(): bool
    {
        $lastnames = array();
        foreach ($this->members as $member) {
            array_push($lastnames, $member->getLastname());
        }

        return sizeof(array_unique($lastnames)) === 1;
    }

    function addMember($member): bool
    {
        if ($this->memberId !== $member->getId() && $this->memberId !== $member->getRelatedMemberId()) {
            return FALSE;
        }

        array_push($this->members, $member);
        return TRUE;
    }

    public static function groupByRelatedMemberId($members): array
    {
        $groups = array();

        foreach ($members as $member) {
            if ($member->belongsToMember()) {
                $relatedId = $member->getRelatedMemberId();

                if (!isset($groups[$relatedId])) {
                    $groups[$relatedId] = new MemberGroup($relatedId);
                }
                $groups[$relatedId]->addMember($member);
            } else {
                $id = $member->getId();
                if (!isset($groups[$id])) {
                    $groups[$id] = new MemberGroup($id);
                }
                $groups[$id]->addMember($member);
            }
        }

        $data = array();

        foreach ($groups as $key => $grp) {
            array_push($data, $grp);
        }

        return $data;
    }

}
