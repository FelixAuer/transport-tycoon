<?php

include_once __DIR__ . '/Cargo.php';
include_once __DIR__ . '/Facility.php';
include_once __DIR__ . '/Transporter.php';
include_once __DIR__ . '/CargoLocation.php';

class Facility implements CargoLocation
{
    private string $name;
    private array $routes;
    private array $distances;
    private array $storage = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    public function setDistances(array $distances): void
    {
        $this->distances = $distances;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function routeTo(Facility $facility): Facility
    {
        return $this->routes[(string)$facility];
    }

    public function distanceTo(Facility $facility)
    {
        return $this->distances[(string)$facility];
    }

    public function nextCargo(): ?Cargo
    {
        return array_shift($this->storage);
    }

    public function store(Cargo $cargo): void
    {
        $this->storage[] = $cargo;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
