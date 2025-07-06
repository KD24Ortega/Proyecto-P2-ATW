<?php
declare(strict_types=1);

require_once __DIR__ . '/../interface/Interface.php';

/**
 * Clase abstracta que declara los métodos
 * definidos por la interfaz OperacionEstadistica.
 */
abstract class Estadistica implements OperacionEstadistica
{
    abstract public function calcularMedia(array $datos): float;
    abstract public function calcularMediana(array $datos): float;
    abstract public function calcularModa(array $datos): array;
}
