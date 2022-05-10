<?php

namespace OCA\SPGVerein\Model;

use JsonSerializable;

class BankAccount implements JsonSerializable {

   private $iban;

   function __construct($jsonData) {
      $this->iban = $jsonData->iban;
   }

   public function jsonSerialize() {
      return array(
         "iban" => $this->iban,
      );
   }
}

