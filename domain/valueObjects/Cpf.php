<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

use Domain\Exceptions\InvalidCpfException;

class Cpf
{
    private string $cpf;

    /**
     * @throws InvalidCpfException
     */
    public function __construct(string $cpf)
    {
        if (!$this->isValid($cpf)) {
            throw new InvalidCpfException();
        }

        $this->cpf = $this->clear($cpf);
    }

    private function isValid(string $cpf): bool
    {
        $cpf = $this->clear($cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    private function clear(string $cpf): string
    {
        return preg_replace('/[^0-9]/is', '', $cpf);
    }

    public function __toString(): string
    {
        return $this->cpf;
    }
}
