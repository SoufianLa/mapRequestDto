<?php

namespace App\Attribute;


#[\Attribute(\Attribute::TARGET_PARAMETER)]
class MapRequestDTO {

    private string $validationGroup;

    public function __construct($group="")
    {
        $this->validationGroup = $group;
    }

    /**
     * @return string
     */
    public function getValidationGroup(): string
    {
        return $this->validationGroup;
    }

    /**
     * @param string $validationGroup
     */
    public function setValidationGroup(string $validationGroup): void
    {
        $this->validationGroup = $validationGroup;
    }

}