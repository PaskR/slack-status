<?php

namespace App;

class Output
{
    public static function write(string $message = ''): void
    {
        echo($message);
    }

    public static function  writeln(string $message = ''): void
    {
        self::write("$message\n");
    }
}