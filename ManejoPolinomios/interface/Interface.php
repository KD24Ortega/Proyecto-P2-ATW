<?php
declare(strict_types=1);


interface OperacionPolinomio
{
    public function evaluar(float $x): float;
    public function derivada(): PolinomioAbstracto;
}

?>