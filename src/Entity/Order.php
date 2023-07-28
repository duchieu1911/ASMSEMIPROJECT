<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'Orders')]
    private Collection $category;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'category')]
    private Collection $Orders;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->Orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(self $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(self $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getOrders(): Collection
    {
        return $this->Orders;
    }

    public function addOrder(self $order): static
    {
        if (!$this->Orders->contains($order)) {
            $this->Orders->add($order);
            $order->addCategory($this);
        }

        return $this;
    }

    public function removeOrder(self $order): static
    {
        if ($this->Orders->removeElement($order)) {
            $order->removeCategory($this);
        }

        return $this;
    }
}
