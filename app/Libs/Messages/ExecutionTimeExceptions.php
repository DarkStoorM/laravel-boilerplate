<?php

namespace App\Libs\Messages;

class ExecutionTimeExceptions
{
    public static string $FINISHED_BEFORE_STARTING = 'Tried to call finish() before starting this timer';
}
