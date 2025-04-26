<?php

/**
 * echo convert(memory_get_usage(true)); // 123 kb
 */
function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function showMemory()
{
    echo "Memory usage (normal): " . convert(memory_get_usage()) . "<br>";
    echo "Memory usage (allocated by system): " . convert(memory_get_usage(true)) . "<br>";
}
