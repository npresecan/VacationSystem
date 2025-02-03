<?php

namespace App\Entity;

use App\Repository\ApprovedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprovedRepository::class)]
class Approved
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Request::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Request $request = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Employee $teamLeader = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Employee $projectManager = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateApprovedTl = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateApprovedPm = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $statusTeamLeader = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $statusProjectManager = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $commentTl = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $commentPm = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function getTeamLeader(): ?Employee
    {
        return $this->teamLeader;
    }

    public function setTeamLeader(Employee $teamLeader): static
    {
        $this->teamLeader = $teamLeader;

        return $this;
    }

    public function getProjectManager(): ?Employee
    {
        return $this->projectManager;
    }

    public function setProjectManager(Employee $projectManager): static
    {
        $this->projectManager = $projectManager;

        return $this;
    }

    public function getDateApprovedTl(): ?\DateTimeInterface
    {
        return $this->dateApprovedTl;
    }

    public function setDateApprovedTl(\DateTimeInterface $dateApprovedTl): static
    {
        $this->dateApprovedTl = $dateApprovedTl;

        return $this;
    }

    public function getDateApprovedPm(): ?\DateTimeInterface
    {
        return $this->dateApprovedPm;
    }

    public function setDateApprovedPm(\DateTimeInterface $dateApprovedPm): static
    {
        $this->dateApprovedPm = $dateApprovedPm;

        return $this;
    }

    public function getStatusTeamLeader(): ?string
    {
        return $this->statusTeamLeader;
    }

    public function setStatusTeamLeader(string $statusTeamLeader): static
    {
        $this->statusTeamLeader = $statusTeamLeader;

        return $this;
    }

    public function getStatusProjectManager(): ?string
    {
        return $this->statusProjectManager;
    }

    public function setStatusProjectManager(string $statusProjectManager): static
    {
        $this->statusProjectManager = $statusProjectManager;

        return $this;
    }

    public function getCommentTl(): ?string
    {
        return $this->commentTl;
    }

    public function setCommentTl(?string $commentTl): static
    {
        $this->commentTl = $commentTl;
        return $this;
    }

    public function getCommentPm(): ?string
    {
        return $this->commentPm;
    }

    public function setCommentPm(?string $commentPm): static
    {
        $this->commentPm = $commentPm;
        return $this;
    }
}