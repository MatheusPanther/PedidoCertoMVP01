<?php

namespace App\Enums;

enum Channel: string
{
    case Whatsapp = 'whatsapp';
    case Instagram = 'instagram';
    case Manual = 'manual';
    case Ai = 'ai';
}
