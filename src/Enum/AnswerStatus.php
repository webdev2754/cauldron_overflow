<?php

namespace App\Enum;

enum AnswerStatus: string
{
    case NEEDS_APPROVAL = 'needs_approval';
    case SPAM = 'spam';
    case APPROVED = 'approved';
}
