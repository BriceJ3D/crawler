<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResearchRepository")
 */
class Research
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="researches", cascade={"persist"})
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime")
     */
    private $search_date;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Url", mappedBy="research", cascade={"persist"})
     */
    private $urls;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $crawledUrls;

    /**
     * @ORM\Column(type="integer")
     */
    private $availableDomains;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->urls = new ArrayCollection();
        $this->crawledUrls = 0;
        $this->availableDomains = 0;
        $this->setEndDate(new \DateTime(date('Y-m-d H:i:s')));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getSearchDate(): ?\DateTimeInterface
    {
        return $this->search_date;
    }

    public function setSearchDate(\DateTimeInterface $search_date): self
    {
        $this->search_date = $search_date;

        return $this;
    }

    /**
     * @return Collection|Url[]
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }
    public function getUrlsLeft(): array
    {
        $urlsLeft = array();
        foreach($this->urls as $url){
            if ($url->getCrawled()==1){
                array_push($urlsLeft, $url);
            }
        }
        return $urlsLeft;
    }

    public function addUrl(Url $url): self
    {
        if (!$this->urls->contains($url)) {
            $this->urls[] = $url;
            $url->addResearch($this);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if ($this->urls->contains($url)) {
            $this->urls->removeElement($url);
            $url->removeResearch($this);
        }

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCrawledUrls(): ?int
    {
        return $this->crawledUrls;
    }

    public function setCrawledUrls(int $crawledUrls): self
    {
        $this->crawledUrls = $crawledUrls;

        return $this;
    }

    public function getAvailableDomains(): ?int
    {
        return $this->availableDomains;
    }

    public function setAvailableDomains(int $availableDomains): self
    {
        $this->availableDomains = $availableDomains;

        return $this;
    }
}
