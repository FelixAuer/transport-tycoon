<?php
include_once __DIR__ . '/Cargo.php';
include_once __DIR__ . '/Facility.php';
include_once __DIR__ . '/Transporter.php';
include_once __DIR__ . '/CargoLocation.php';

class Cargo
{
    private Facility $destination;
    private CargoLocation $storageLocation;

    public function __construct(Facility $destination)
    {
        $this->destination = $destination;
    }

    public function getDestination(): Facility
    {
        return $this->destination;
    }

    public function moveTo(CargoLocation $storageLocation): void
    {
        $this->storageLocation = $storageLocation;
        $storageLocation->store($this);
    }

    public function arrived(): bool
    {
        return $this->storageLocation === $this->destination;
    }
}
