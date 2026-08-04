<?php

namespace App\Enums;

enum ConversationStatus: string
{
    case New = 'new';
    case AiHandling = 'ai_handling';
    case WaitingCustomer = 'waiting_customer';
    case WaitingAttendant = 'waiting_attendant';
    case HumanHandling = 'human_handling';
    case OrderStarted = 'order_started';
    case OrderConfirmed = 'order_confirmed';
    case Closed = 'closed';
    case Lost = 'lost';
}
