<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @UniqueEntity("isbn")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_books_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"get_books_collection"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $editor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $collection;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $summary;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @Groups({"get_books_collection"})
     */
    private $isbn;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $pages;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=2048, nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $image;

    /**
     * @ORM\Column(type="integer", length=64, nullable=true)
     * @Groups({"get_books_collection"})
     */
    private $publicationDate;


    /**
     * @ORM\ManyToMany(targetEntity=Author::class, mappedBy="books", cascade={"persist"})
     * @Groups({"get_books_collection"})
     */
    private $authors;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, mappedBy="books")
     * @Groups({"get_books_collection"})
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity=Library::class, mappedBy="book", orphanRemoval=true, cascade={"persist"})
     */
    private $libraries;


    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->libraries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(?string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->collection;
    }

    public function setCollection(?string $collection): self
    {
        $this->collection = $collection;

        return $this;
    }


    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }


    public function getPages(): ?int
    {
        return $this->pages;
    }

    public function setPages(?int $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addBook($this);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeBook($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
            $genre->addBook($this);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->removeElement($genre)) {
            $genre->removeBook($this);
        }

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
            $library->setBook($this);
        }

        return $this;
    }

    public function removeLibrary(Library $library): self
    {
        if ($this->libraries->removeElement($library)) {
            // set the owning side to null (unless already changed)
            if ($library->getBook() === $this) {
                $library->setBook(null);
            }
        }

        return $this;
    }



    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPublicationDate(): ?int
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?int $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }
}
