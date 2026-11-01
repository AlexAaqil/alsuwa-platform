<?php

namespace Modules\CRM\Enums;

enum DealStage: string
{
    case QUALIFICATION = 'qualification';
    case PROPOSAL = 'proposal';
    case NEGOTIATION = 'negotiation';
    case WON = 'won';
    case LOST = 'lost';
}