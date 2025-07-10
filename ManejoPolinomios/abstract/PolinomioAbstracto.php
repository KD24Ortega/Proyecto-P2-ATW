<?php
declare(strict_types=1);

require_once __DIR__ . '/../interface/Interface.php';


abstract class PolinomioAbstracto implements OperacionPolinomio
{
    protected array $terminos;

    public function __construct(array $terminos)
    {
        $this->terminos = [];
        foreach ($terminos as $grado => $coef) {
            $g = (int) $grado;
            $c = (float) $coef;
            if ($c !== 0.0) {
                $this->terminos[$g] = $c;
            }
        }
        krsort($this->terminos);
    }

    public function getTerminos(): array
    {
        return $this->terminos;
    }

    abstract public function evaluar(float $x): float;
    abstract public function derivada(): PolinomioAbstracto;
}
