<?php
declare(strict_types=1);

/**
 * Operación sobre un conjunto de datos numéricos:
 * media, mediana y moda.
 */
interface OperacionEstadistica
{
    public function calcularMedia(array $datos): float;
    public function calcularMediana(array $datos): float;
    public function calcularModa(array $datos): array;
}
