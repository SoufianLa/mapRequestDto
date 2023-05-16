<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AuthenticationDTO
{
    #[Assert\NotBlank(groups: ['a'])]
    #[Assert\Length(min: 10, max: 500)]
    public  $comment;

    #[Assert\NotBlank(groups: ['a'])]
    #[Assert\GreaterThanOrEqual(1, groups: ['a'])]
    #[Assert\LessThanOrEqual(5, groups: ['a'])]
    public  $rating;

}