<?php

namespace App\Components\Mediable\Services\Media;

use App\Components\Generic\Utils\PathUtils;
use App\Components\Generic\Utils\StringUtils;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

final class PathGenerator extends DefaultPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        /** @var Model $model */
        $model = $media->model;

        $folder = StringUtils::pluralBasename($model::class);
        $subfolder = isset($model->slug) ? sprintf('%s-%s', $model->id, $model->slug) : $model->id;

        return PathUtils::join([$folder, $subfolder, $media->getKey()]);
    }
}
