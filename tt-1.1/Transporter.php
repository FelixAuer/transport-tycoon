<?php
include_once __DIR__ . '/Cargo.php';
include_once __DIR__ . '/Facility.php';
include_once __DIR__ . '/Transporter.php';
include_once __DIR__ . '/CargoLocation.php';

/**
 * Models a transporter, like a truck or a ship.
 * The transporter works as a state machine to handle its cargo.
 */
class Transporter implements CargoLocation
{
    public const STATUS_IDLE = 0;
    public const STATUS_TRANSPORTING = 1;
    public const STATUS_RETURNING = 2;

    private string $name;
    private ?Cargo $cargo = null;
    private Facility $home;
    private Facility $currentLocation;

    private int $status = self::STATUS_IDLE;
    private int $cooldown = 1;

    public function __construct(string $name, Facility $home)
    {
        $this->name = $name;
        $this->home = $home;
        $this->currentLocation = $home;
    }

    public function store(Cargo $cargo): void
    {
        $this->cargo = $cargo;
    }

    public function tick(): void
    {
        $this->cooldown--;
        if ($this->cooldown > 0) {
            return;
        }

        switch ($this->status) {
            case self::STATUS_IDLE:
                $cargo = $this->currentLocation->nextCargo();
                if ($cargo) {
                    $cargo->moveTo($this);
                    $this->travelTo($this->currentLocation->routeTo($cargo->getDestination()));
                    $this->status = self::STATUS_TRANSPORTING;
                    //        echo "Loading Cargo heading to {$cargo->getDestination()} on {$this->name}. Traveling to {$this->facility}...\n";
                }

                return;
            case self::STATUS_TRANSPORTING:
                //    echo "Unloading Cargo heading to {$this->cargo->getDestination()} from {$this->name}. Returning home to {$this->home}...\n";
                $this->cargo->moveTo($this->currentLocation);
                $this->cargo = null;
                $this->travelTo($this->home);
                $this->status = self::STATUS_RETURNING;

                return;
            case self::STATUS_RETURNING:
                //          echo "{$this->name} arrived at {$this->home}. Waiting for cargo...\n";
                $this->status = self::STATUS_IDLE;

                return;
        }
    }

    public function isIdle(): bool
    {
        return $this->status == self::STATUS_IDLE;
    }

    protected function travelTo(Facility $facility): void
    {
        $this->cooldown = $this->currentLocation->distanceTo($facility);
        $this->currentLocation = $facility;
    }
}
