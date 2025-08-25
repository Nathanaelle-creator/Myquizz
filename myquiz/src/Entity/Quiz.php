<?php
namespace App\Entity;


use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Categorie;
use App\Repository\QuizRepository;
use App\Entity\Question;



#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?int $nbQuestions = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\ManyToMany(targetEntity: Question::class)]
    #[ORM\JoinTable(name: 'quiz_question')]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }

    public function getScore(): ?int { return $this->score; }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getNbQuestions(): ?int { return $this->nbQuestions; }

    public function setNbQuestions(int $nb): self
    {
        $this->nbQuestions = $nb;
        return $this;
    }

    public function getCategorie(): ?Categorie { return $this->categorie; }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getUtilisateur(): ?User { return $this->utilisateur; }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }
    
}
