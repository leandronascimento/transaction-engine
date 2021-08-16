<?php

namespace Domain\ValueObjects;

use Domain\Exceptions\InvalidCnpjException;

class Cnpj
{
    private string $cnpj;

    /**
     * @throws InvalidCnpjException
     */
    public function __construct(string $cnpj)
    {
        if (!$this->isValid($cnpj)) {
            throw new InvalidCnpjException();
        }

        $this->cnpj = $this->clear($cnpj);
    }

    private function isValid(string $cnpj): bool
    {
        $cnpj = $this->clear($cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        for ($i = 0, $j = 5, $total = 0; $i < 12; $i++) {
            $total += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $rest = $total % 11;

        if ($cnpj[12] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }

        for ($i = 0, $j = 6, $total = 0; $i < 13; $i++) {
            $total += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $rest = $total % 11;

        return $cnpj[13] == ($rest < 2 ? 0 : 11 - $rest);
    }

    private function clear(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', (string) $cnpj);
    }

    public function __toString(): string
    {
        return $this->cnpj;
    }
}
