<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserInput
{
    #[Assert\NotBlank(groups: ['Registration'])]
    #[Assert\Email(groups: ['Registration'])]
    #[Groups(['user:write'])]
    public ?string $email = null;

    #[Assert\NotBlank(groups: ['Registration'])]
    #[Assert\Length(min: 3, max: 50, groups: ['Registration'])]
    #[Groups(['user:write'])]
    public ?string $username = null;

    #[Assert\NotBlank(groups: ['Registration'])]
    #[Assert\Length(min: 8, max: 4096, groups: ['Registration'])]
    #[Groups(['user:write'])]
    public ?string $plainPassword = null;

    #[Assert\NotBlank(groups: ['Registration'])]
    #[Assert\Length(min: 3, max: 100, groups: ['Registration'])]
    #[Assert\Regex(pattern: '/^[a-zA-Z]+\s[a-zA-Z]+$/', message: 'The fullname must contain only letters.', groups: ['Registration'])]
    #[Groups(['user:write'])]
    public ?string $fullname = null;
}
