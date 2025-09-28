<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Enum;

enum Actions: string
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
}
