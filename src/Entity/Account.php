<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $q;

    /**
     * @ORM\OneToOne(targetEntity="User",
     * inversedBy="account")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQ(): ?string
    {
        return $this->q;
    }

    public function setQ(string $q): self
    {
        $this->q = $q;

        return $this;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}
