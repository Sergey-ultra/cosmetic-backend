<?php

namespace App\Services;

enum EntityStatus: string
{
   case PUBLISHED = 'published';
   case DELETED = 'deleted';
   case MODERATED = 'moderated';
   case REJECTED = 'rejected';
   case DRAFT = 'draft';
}
