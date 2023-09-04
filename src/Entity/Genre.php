<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_genres_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_genres_collection"})
     * @Assert\NotBlank
     * @Groups({"get_genres_collection"})
     */
    private $name;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     * @Groups({"get_genres_collection"})
     */
    private $homeOrder;

    /**
     * @ORM\ManyToMany(targetEntity=Book::class, inversedBy="genres")
     * @Groups({"get_genres_collection"})
     */
    private $books;

    /**
     * @ORM\OneToMany(targetEntity=Library::class, mappedBy="genre")
     */
    private $libraries;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->homeOrder = 0;
        $this->libraries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHomeOrder(): ?int
    {
        return $this->homeOrder;
    }

    public function setHomeOrder(int $homeOrder): self
    {
        $this->homeOrder = $homeOrder;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        $this->books->removeElement($book);

        return $this;
    }

    /**
     * @return Collection<int, Library>
     */
    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Library $library): self
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries[] = $library;
            $library->setGenre($this);
        }

        return $this;
    }

    public function removeLibrary(Library $library): self
    {
        if ($this->libraries->removeElement($library)) {
            // set the owning side to null (unless already changed)
            if ($library->getGenre() === $this) {
                $library->setGenre(null);
            }
        }

        return $this;
    }
}
