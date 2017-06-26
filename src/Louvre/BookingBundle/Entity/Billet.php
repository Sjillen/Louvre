<?php

namespace Louvre\BookingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Billet
{
    protected $description;

    protected $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->descritpion = $description;
    }

    public function getTickets()
    {
       return $this->tickets; 
    }
    
}

