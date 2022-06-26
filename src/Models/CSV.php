<?php
namespace Console\Models;

class CSV
{
    public static $header = [
        "order_id",
        "order_datetime",
        "total_order_value",
        "average_unit_price",
        "distinct_unit_count",
        "total_units_count",
        "customer_state"
    ];

    public static function getHeader(){
        return self::$header;
    }
}