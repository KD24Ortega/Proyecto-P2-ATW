<?php
declare(strict_types=1);

require_once __DIR__ . '/../abstract/PolinomioAbstracto.php';

class Polinomio extends PolinomioAbstracto
{
    public function evaluar(float $x): float
    {
        $res = 0.0;
        foreach ($this->terminos as $grado => $coef) {
            $res += $coef * ($x ** $grado);
        }
        return $res;
    }

    public function derivada(): PolinomioAbstracto
    {
        $d = [];
        foreach ($this->terminos as $g => $c) {
            if ($g > 0) {
                $d[$g - 1] = $c * $g;
            }
        }
        return new self($d);
    }

    public static function sumarPolinomios(array $p1, array $p2): array
    {
        $s = $p1;
        foreach ($p2 as $g => $c) {
            $g = (int)$g; $c = (float)$c;
            $s[$g] = ($s[$g] ?? 0.0) + $c;
            if ($s[$g] === 0.0) {
                unset($s[$g]);
            }
        }
        krsort($s);
        return $s;
    }

    public function __toString(): string
    {
        if (empty($this->terminos)) {
            return '0';
        }
        $parts = [];
        foreach ($this->terminos as $g => $c) {
            $sign = $c >= 0 ? '+' : '-';
            $abs  = abs($c);
            $term = $sign . $abs;
            if ($g > 0) {
                $term .= 'x';
                if ($g > 1) {
                    $term .= '^' . $g;
                }
            }
            $parts[] = $term;
        }
        $s = implode('', $parts);
        return ltrim($s, '+');
    }
}
