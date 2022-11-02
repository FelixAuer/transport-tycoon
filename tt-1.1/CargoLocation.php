<?php

include_once __DIR__ . '/Cargo.php';
include_once __DIR__ . '/Facility.php';
include_once __DIR__ . '/Transporter.php';
include_once __DIR__ . '/CargoLocation.php';

interface CargoLocation
{
    public function store(Cargo $cargo): void;
}
