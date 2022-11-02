<?php
include_once __DIR__ . '/Cargo.php';
include_once __DIR__ . '/Facility.php';
include_once __DIR__ . '/Transporter.php';
include_once __DIR__ . '/CargoLocation.php';

// read the input or use AABABBAB as default if none is provided.
$cargoList = $argv[1] ?? 'AABABBAB';

/*
 * Setup the world map.
 * Since everything is hard coded and doesnt change we can get away with letting
 * a facility handle the connections to its neighbours.
 * For a more complex world map we should build a more flexible system in which a location doesn't have
 * to know about its neighbours.
 */
$factory = new Facility('Factory');
$port = new Facility('Port');
$locationA = new Facility('A');
$locationB = new Facility('B');

$factory->setDistances([
    (string)$port => 1,
    (string)$locationB => 5,
]);
$factory->setRoutes([
    (string)$port => $port,
    (string)$locationA => $port,
    (string)$locationB => $locationB,
]);
$port->setDistances([
    (string)$factory => 1,
    (string)$locationA => 4,
]);
$port->setRoutes([
    (string)$factory => $factory,
    (string)$locationA => $locationA,
]);
$locationA->setDistances([
    (string)$port => 4,
]);
$locationA->setRoutes([
    (string)$port => $port,
]);
$locationB->setDistances([
    (string)$factory => 5,
]);
$locationB->setRoutes([
    (string)$factory => $factory,
]);

$transporters = [
    new Transporter('Truck 1', $factory),
    new Transporter('Truck 2', $factory),
    new Transporter('Ship', $port),
];

/*
 * Setup cargo objects and move them to the factory.
 */
$cargoDestinations = [
    'A' => $locationA,
    'B' => $locationB
];
$cargo = [];
foreach (str_split($cargoList) as $destination) {
    $c = new Cargo($cargoDestinations[$destination]);
    $c->moveTo($factory);
    $cargo[] = $c;
}

/*
 * Let the simulation run, one hour at a time.
 * First handle all non-idle transporters and let them load/unload/move their cargo.
 * Then handle idle transporters in case cargo arrived at their current location.
 * End if all cargo has arrived at their destination.
 */
echo "Cargo: " . $cargoList . "\n";
$time = 0;
while (true) {
    // echo "$time hours passed.\n";
    /** @var Transporter $transporter */
    foreach (array_filter($transporters, static fn($t) => !$t->isIdle()) as $transporter) {
        $transporter->tick();
    }
    foreach (array_filter($transporters, static fn($t) => $t->isIdle()) as $transporter) {
        $transporter->tick();
    }
    if (empty(array_filter($cargo, static function ($c) {
        /** @var Cargo $c */
        return !$c->arrived();
    }))) {
        // no more cargo left that hasn't already arrived at its destination.
        break;
    }
    $time++;
}
echo "Finished after $time hours!";