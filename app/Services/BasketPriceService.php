<?php

declare(strict_types = 1);

namespace App\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class BasketPriceService
{
    public function summary(Collection $items): array
    {
        $totalItems = 0;

        $totalCents = 0;

        foreach ($items as $item) {
            $qty = (int) $item->quantity;
            $totalItems += $qty;

            $priceCents = $this->toCents($item->product->price);
            $totalCents = $priceCents * $qty;
        }

        return [
            'total_Items' => $totalItems,
            'total_Base' => $this->fromCents($totalCents),
            'total_Final' => $this->fromCents($totalCents),
        ];
    }

    private function toCents(mixed $price): int
    {
        if (!is_string($price)) {
            throw new InvalidArgumentException('Цена должна быть DECIMAL');
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
            throw new \http\Exception\InvalidArgumentException("Неправильный формат суммы {price}");
        }

        [$int, $fraction] = array_pad(explode('.', $price), 2, '00');
        $fraction = str_pad($fraction, 2, '0');

        return ((int) $int * 100) + (int) $fraction;
    }

    private function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}