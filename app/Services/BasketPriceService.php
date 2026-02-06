<?php

declare(strict_types = 1);

namespace App\Services;

use Illuminate\Support\Collection;

class BasketPriceService
{
    public function summary(Collection $items): array
    {
        $totalItems = 0;

        $totalBase = '0.00';
        $totalFinal = '0.00';

        foreach ($items as $item) {
            $qty = (int) $item->quantity;
            $totalItems += $qty;

            $baseUnit = $item->money($item->product->price);
            $finalUnit = $baseUnit;

            $lineBase = bcmul($baseUnit, (string) $qty, 2);
            $lineFinal = bcmul($finalUnit, (string) $qty, 2);

            $totalBase = bcadd($totalBase, $lineBase, 2);
            $totalFinal = bcadd($totalFinal, $lineFinal, 2);
        }

        return [
            'total_Items' => $totalItems,
            'total_Base' => $totalBase,
            'total_Final' => $totalFinal,
        ];
    }

    private function money($value): string
    {
        return bcadd((string) $value, '0', 2);
    }
}