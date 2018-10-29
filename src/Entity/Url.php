<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UrlRepository")
 */
class Url
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
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Research", inversedBy="urls", cascade={"persist"})
     */
    private $research;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Domain", mappedBy="Url", cascade={"persist"})
     */
    private $domains;

    /**
     * @ORM\Column(type="boolean")
     */
    private $crawled;

    public function __construct($address){
        $this->setAddress($address);
        $this->research = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->setCrawled(false);
    }

    public function __toString(){
        return $this->getAddress();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|Research[]
     */
    public function getResearch(): Collection
    {
        return $this->research;
    }

    public function addResearch(Research $research): self
    {
        if (!$this->research->contains($research)) {
            $this->research[] = $research;
        }

        return $this;
    }

    public function removeResearch(Research $research): self
    {
        if ($this->research->contains($research)) {
            $this->research->removeElement($research);
        }

        return $this;
    }

    /**
     * @return Collection|Domain[]
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function addDomain(Domain $domain): self
    {
        if (!$this->domains->contains($domain)) {
            $this->domains[] = $domain;
            $domain->addUrl($this);
        }

        return $this;
    }

    public function removeDomain(Domain $domain): self
    {
        if ($this->domains->contains($domain)) {
            $this->domains->removeElement($domain);
            $domain->removeUrl($this);
        }

        return $this;
    }

    public function getCrawled(): ?bool
    {
        return $this->crawled;
    }

    public function setCrawled(bool $crawled): self
    {
        $this->crawled = $crawled;

        return $this;
    }
}
