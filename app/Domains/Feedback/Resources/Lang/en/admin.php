<?php

use App\Domains\Admin\Enums\Translation\AdminNavigationGroupTranslationKey;
use App\Domains\Admin\Enums\Translation\AdminPagePropertyTranslationKey;
use App\Domains\Admin\Enums\Translation\AdminResourcePropertyTranslationKey;
use App\Domains\Admin\Providers\DomainServiceProvider;
use App\Domains\Components\Generic\Utils\LangUtils;
use App\Domains\Feedback\Admin\Pages\ManageFeedbackSettings;
use App\Domains\Feedback\Admin\Resources\FeedbackResource;

return [
    FeedbackResource::class => [
        AdminResourcePropertyTranslationKey::LABEL->name => 'Feedback',
        AdminResourcePropertyTranslationKey::PLURAL_LABEL->name => 'Feedback',
        AdminResourcePropertyTranslationKey::NAVIGATION_LABEL->name => 'Feedback',
        AdminResourcePropertyTranslationKey::NAVIGATION_GROUP->name => LangUtils::translateEnum(DomainServiceProvider::TRANSLATION_NAMESPACE, AdminNavigationGroupTranslationKey::FEEDBACK),
    ],
    ManageFeedbackSettings::class => [
        AdminPagePropertyTranslationKey::TITLE->name => 'Feedback Settings',
        AdminPagePropertyTranslationKey::NAVIGATION_LABEL->name => 'Feedback',
        AdminPagePropertyTranslationKey::NAVIGATION_GROUP->name => LangUtils::translateEnum(DomainServiceProvider::TRANSLATION_NAMESPACE, AdminNavigationGroupTranslationKey::SETTINGS),
    ],
];
