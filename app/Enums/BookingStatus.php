<?php

namespace App\Enums;

use BenSampo\Enum\Enum;


final class BookingStatus extends Enum
{
    const PENDING =   1;
    const COMPLETED   =   2;
    const PARTIALLY_PAID   =   3;
    const CANCEL    =   4;
    const PENDING_CANCEL = 5;
}
