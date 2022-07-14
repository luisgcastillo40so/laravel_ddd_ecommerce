<?php

namespace App\Domains\Generic\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;

final class Database
{
    /**
     * @return int[]
     */
    public function insertByChunks(string $table, Enumerable $rows, int $chunkSize = 100, int $chunkSliceSize = 20): array
    {
        $maxId = DB::table($table)->max('id') ?? 0;

        $rows
            ->chunk($chunkSize)
            ->each(function (Enumerable $chunk) use ($table, $chunkSize, $chunkSliceSize): void {
                $operationsCount = ceil($chunkSize / $chunkSliceSize);

                $operations = [];
                for ($i = 0; $i < $operationsCount; $i += 1) {
                    $operations[] = static fn () => DB::table($table)->insert($chunk->slice($i * $chunkSliceSize, $chunkSliceSize)->toArray()); // Unable to use Closures from out of function scope due to serialization issues.
                }

                $this->concurrently($operations);
            });

        return DB::table($table)
            ->where('id', '>', $maxId)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return int[]
     */
    public function updateByChunks(string $table, Builder|EloquentBuilder $query, array $updates, int $chunkSize = 100, int $chunkSliceSize = 20): array
    {
        $query
            ->select(["{$table}.id"])
            ->chunkById($chunkSize, function (Enumerable $chunk) use ($table, $updates, $chunkSize, $chunkSliceSize): void {
                $operationsCount = ceil($chunkSize / $chunkSliceSize);

                $operations = [];
                for ($i = 0; $i < $operationsCount; $i += 1) {
                    $operations[] = static fn () => DB::table($table)->whereIntegerInRaw("{$table}.id", $chunk->slice($i * $chunkSliceSize, $chunkSliceSize)->pluck('id')->toArray())->update($updates); // Unable to use Closures from out of function scope due to serialization issues.
                }

                $this->concurrently($operations);
            });

        return $query->pluck("{$table}.id")->toArray();
    }

    /**
     * @param callable[] $operations
     */
    private function concurrently(array $operations): void
    {
        foreach ($operations as $operation) {
            $operation();
        }

        /*
         * TODO: Find a way to use Octane concurrency with
         * speed up in comparison to sequential operations.
         *
         * Octane::concurrently($operations);
         * */
    }
}
