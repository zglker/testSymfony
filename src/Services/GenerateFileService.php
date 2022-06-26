<?php
namespace Console\Services;

use DateTime;
use DateTimeInterface;

class GenerateFileService
{
    public function convertTime(string $time): string
    {
        if($dateTime = DateTime::createFromFormat('D, d M Y H:i:s O', $time)) {
            return $dateTime->format(DateTimeInterface::ISO8601);
        }
        return false;
    }


    public function getOrderTotal(array $items): float
    {
        $total = 0;
        foreach($items as $item) {
            $total += floatval($item->quantity) * floatval($item->unit_price);
        }
        return $total;
    }

    public function getOrderTotalWithDiscount(float $total, $discounts) : string
    {
        $discountsArray = (array)$discounts;
        if(count($discountsArray) > 0) {
            usort($discountsArray, function ($a, $b) {
                return $a->priority <=> $b->priority;
            });
            foreach($discountsArray as $discount) {
                switch ($discount->type) {
                    case 'DOLLAR':
                        $total = bcsub($total, $discount->value, 2);
                        break;
                    case 'PERCENTAGE':
                        $total = $total * (100 - intval($discount->value)) / 100;
                        break;
                    default:
                        break;
                }
            }
        }
       return number_format($total, 2);
    }

    public function getAverageUnitPrice(float $total, $totalUnits) : float
    {
        return bcdiv($total, $totalUnits,2);
    }

    public function getTotalUnitsCount($items) : int
    {
        return array_reduce($items, function($carry, $item) {
            $carry += $item->quantity;
            return $carry;
        }, 0);
    }

    public function generate(array $newDataHeader, array $newData, string $format) : string
    {
        $fileName = 'output-'.time();
        switch ($format) {
            case 'jsonl':
                //Create a jsonl file

                $file = fopen($fileName.'.'.$format, 'w');
                foreach ($newData as $line) {
                    $d = array_combine($newDataHeader, $line);
                    //put data into csv file
                    fputs($file, json_encode($d)."\n");
                }
                fclose($file);
                break;
            default:
                //Create a CSV file
                $file = fopen($fileName.'.'.$format, 'w');
                //put data into csv file
                fputcsv($file, $newDataHeader);
                foreach ($newData as $line) {
                    //put data into csv file
                    fputcsv($file, $line);
                }
                fclose($file);
                break;
        }
        return $fileName;
    }


}