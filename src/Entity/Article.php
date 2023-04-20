<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Since;


/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailArticle",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getArticles")
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "deleteArticle",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getArticles", excludeIf = "expr(not is_granted('IS_AUTHENTICATED_FULLY'))"),
 * )
 *
 * @Hateoas\Relation(
 *      "update",
 *      href = @Hateoas\Route(
 *          "updateArticle",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getArticles", excludeIf = "expr(not is_granted('IS_AUTHENTICATED_FULLY'))"),
 * )
 *
 */
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getArticles", "getDetailArticle"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getArticles", "getDetailArticle"])]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getDetailArticle"])]
    #[Assert\NotBlank]
    private ?string $color = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups(["getDetailArticle"])]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups(["getDetailArticle"])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["getDetailArticle"])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getDetailArticle"])]
    #[AppAssert\ArticleCategory()]
    private ?Category $category = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["getDetailArticle"])]
    #[Since("2.0")]
    private ?bool $validated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(?bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }
}
