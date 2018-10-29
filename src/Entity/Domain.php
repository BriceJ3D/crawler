<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 */
class Domain
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dispo;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $expiration_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domainName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Url", inversedBy="domains", cascade={"persist"})
     */
    private $Url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $RefIP;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastCrawledDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trustFlow;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trustMetrics;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TopicalTrustFlow", mappedBy="domain",cascade={"persist"})
     */
    private $topicalTrustFlows;

    public function __construct($domainName){
        $this->domainName = $domainName;
        $this->Url = new ArrayCollection();
        $this->topicalTrustFlows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomainName(): ?string
    {
        return $this->domainName;
    }

    public function setDomainName(string $domainName): self
    {
        $this->domainName = $domainName;

        return $this;
    }

    public function getDispo(): ?string
    {
        return $this->dispo;
    }

    public function setDispo(?string $dispo): self
    {
        $this->dispo = $dispo;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(?\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function __toString()
    {
        return $this->getDomainName();
    }

    /**
     * @return Collection|Url[]
     */
    public function getUrl(): Collection
    {
        return $this->Url;
    }

    public function addUrl(Url $url): self
    {
        if (!$this->Url->contains($url)) {
            $this->Url[] = $url;
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if ($this->Url->contains($url)) {
            $this->Url->removeElement($url);
        }

        return $this;
    }

    public function getTags(): array
    { 
        $tags = array();
        foreach($this->getUrl() as $url){
            foreach ($url->getResearch() as $research){
                foreach($research->getTags() as $tag){
                    array_push($tags, $tag);
                }
            }
        }
        $tags = array_unique($tags);
        return $tags;
    }

    public function getRefIP(): ?int
    {
        return $this->RefIP;
    }

    public function setRefIP(?int $RefIP): self
    {
        $this->RefIP = $RefIP;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLastCrawledDate(): ?\DateTimeInterface
    {
        return $this->lastCrawledDate;
    }

    public function setLastCrawledDate(?\DateTimeInterface $lastCrawledDate): self
    {
        $this->lastCrawledDate = $lastCrawledDate;

        return $this;
    }

    public function getTrustFlow(): ?int
    {
        return $this->trustFlow;
    }

    public function setTrustFlow(?int $trustFlow): self
    {
        $this->trustFlow = $trustFlow;

        return $this;
    }

    public function getTrustMetrics(): ?int
    {
        return $this->trustMetrics;
    }

    public function setTrustMetrics(?int $trustMetrics): self
    {
        $this->trustMetrics = $trustMetrics;

        return $this;
    }

    /**
     * @return Collection|TopicalTrustFlow[]
     */
    public function getTopicalTrustFlows(): Collection
    {
        return $this->topicalTrustFlows;
    }

    public function addTopicalTrustFlow(TopicalTrustFlow $topicalTrustFlow): self
    {
        if (!$this->topicalTrustFlows->contains($topicalTrustFlow)) {
            $this->topicalTrustFlows[] = $topicalTrustFlow;
            $topicalTrustFlow->setDomain($this);
        }

        return $this;
    }

    public function removeTopicalTrustFlow(TopicalTrustFlow $topicalTrustFlow): self
    {
        if ($this->topicalTrustFlows->contains($topicalTrustFlow)) {
            $this->topicalTrustFlows->removeElement($topicalTrustFlow);
            // set the owning side to null (unless already changed)
            if ($topicalTrustFlow->getDomain() === $this) {
                $topicalTrustFlow->setDomain(null);
            }
        }

        return $this;
    }
    
}
