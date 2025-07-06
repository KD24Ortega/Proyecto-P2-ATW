<?php
declare(strict_types=1);

require_once __DIR__ . '/../interface/Interface.php';
require_once __DIR__ . '/../abstract/Estadistica.php';

/**
 * Implementación concreta de Estadistica:
 * calcula media, mediana, moda y genera informes.
 */
class EstadisticaBasica extends Estadistica
{
    public function calcularMedia(array $datos): float
    {
        return array_sum($datos) / count($datos);
    }

    public function calcularMediana(array $datos): float
    {
        sort($datos, SORT_NUMERIC);
        $n    = count($datos);
        $half = (int) floor($n / 2);

        if ($n % 2 === 1) {
            return $datos[$half];
        }

        return ($datos[$half - 1] + $datos[$half]) / 2;
    }

    public function calcularModa(array $datos): array
    {
        $freq = [];
        foreach ($datos as $v) {
            $freq[$v] = ($freq[$v] ?? 0) + 1;
        }
        arsort($freq);
        $max = reset($freq);
        if ($max <= 1) {
            return [];
        }
        return array_keys(array_filter($freq, fn($f) => $f === $max));
    }

    /**
     * Genera un informe con media, mediana y moda
     * para cada conjunto de datos proporcionado.
     *
     * @param array<string, float[]> $data
     * @return array<string, array{media: float|null, mediana: float|null, moda: array}>
     */
    public function generarInforme(array $data): array
    {
        $out = [];
        foreach ($data as $id => $datos) {
            if (empty($datos)) {
                $out[$id] = ['media' => null, 'mediana' => null, 'moda' => []];
            } else {
                $out[$id] = [
                    'media'   => $this->calcularMedia($datos),
                    'mediana' => $this->calcularMediana($datos),
                    'moda'    => $this->calcularModa($datos),
                ];
            }
        }
        return $out;
    }
}
