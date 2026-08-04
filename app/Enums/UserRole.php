<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Manager = 'manager';
    case Attendant = 'attendant';
    case Kitchen = 'kitchen';
    case Cashier = 'cashier';
    case Delivery = 'delivery';
    case Analyst = 'analyst';
}
