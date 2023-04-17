<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LibraryRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LibraryRepository::class)
 */
class Library
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $finished;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $purchased;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $favorite;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $wishlist;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $quote;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"get_books_collection", "get_users_item"})
     */
    private $rate;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="libraries")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_users_item"})
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="libraries")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_books_collection"})
     */
    private $user;

    public function __construct()
    {
        $this->finished = false;
        $this->purchased = false;
        $this->favorite = false;
        $this->wishlist = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(?string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    public function isPurchased(): ?bool
    {
        return $this->purchased;
    }

    public function setPurchased(bool $purchased): self
    {
        $this->purchased = $purchased;

        return $this;
    }

    public function isFavorite(): ?bool
    {
        return $this->favorite;
    }

    public function setFavorite(bool $favorite): self
    {
        $this->favorite = $favorite;

        return $this;
    }

    public function isWishlist(): ?bool
    {
        return $this->wishlist;
    }

    public function setWishlist(bool $wishlist): self
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }
}
