<?php

function write(string $message = ''): void
{
    echo($message);
}

function writeln(string $message = ''): void
{
    write("$message\n");
}