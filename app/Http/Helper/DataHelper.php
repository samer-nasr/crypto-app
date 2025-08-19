<?php

namespace App\Http\Helper;

class DataHelper
{
    protected $record;
    protected $previous_record;
    protected $records;
    protected $close_prices;

    public function __construct($record, $previous_record, $records, $close_prices)
    {
        $this->record           = $record;
        $this->previous_record  = $previous_record;
        $this->close_prices     = $close_prices;
        $this->records          = $records;
    }
    public function getAveragePrice()
    {
        return ($this->record->open_price
            + $this->record->close_price
            + $this->record->high_price
            + $this->record->low_price)
            / 4;
    }

    public function getPriceRange()
    {
        return $this->record->high_price - $this->record->low_price;
    }

    public function getPercentageChange()
    {
        return ($this->record->close_price - $this->record->open_price) / $this->record->open_price;
    }

    public function getPreviousAveragePrice()
    {
        if ($this->previous_record->avg_price) return $this->previous_record->avg_price;
        return ($this->previous_record->open_price
            + $this->previous_record->close_price
            + $this->previous_record->high_price
            + $this->previous_record->low_price)
            / 4;
    }

    public function getPreviousPercentageChange()
    {
        return ($this->previous_record->close_price - $this->previous_record->open_price) / $this->previous_record->open_price;
    }

    public function getSma($n, $i)
    {
        if ($i < $n - 1) return null;
        $closingPrices = array_slice($this->close_prices, $i - $n + 1, $n);
        return array_sum($closingPrices) / count($closingPrices);
    }

    public function getEma($n, $i)
    {
        if ($i < $n - 1) return null;
        if ($i == $n - 1) return $this->getSma($n, $i);
        $k = 2 / ($n + 1);
        $ema = ($k * $this->record->close_price) + ((1 - $k) * $this->previous_record->{"ema_$n"});
        return $ema;
    }

    public function getRsi14($i , $n)
    {
        if ($i < 14) return null;
        $gains = $losses = [];
        for ($j = 0; $j < 14; $j++) {
            $change     = $this->records[$i - $j]->close_price - $this->records[$i - $j - 1]->close_price;
            $gains[]    = max($change, 0);
            $losses[]   = max(-$change, 0);
        }

        $avgGain = array_sum(array_slice($gains, -14)) / 14;
        $avgLoss = array_sum(array_slice($losses, -14)) / 14;
        $rs = $avgGain / $avgLoss;
        $rsi_14 = 100 - (100 / (1 + $rs));
        return $rsi_14;
    }
}
