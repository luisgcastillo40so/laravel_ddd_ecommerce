<?php

namespace App\Domains\Catalog\Enums\Translation;

enum ProductResourceTranslationKey: string
{
    case TITLE = 'title';
    case SLUG = 'slug';
    case DESCRIPTION = 'description';

    case CATEGORIES = 'categories';
    case IMAGES = 'images';

    case IS_DISPLAYABLE = 'is_displayable';
}
