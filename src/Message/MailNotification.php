<?php

namespace App\Message;

use App\Entity\User;

class MailNotification
{
    private $description;
    private $id;
    private $from;
    private $user;

    public function __construct(User $user, string $description, int $id, string $from)
    {
        $this->description = $description;
        $this->id = $id;
        $this->from = $from;
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFrom(): string
    {
        return $this->from;
    }
}
