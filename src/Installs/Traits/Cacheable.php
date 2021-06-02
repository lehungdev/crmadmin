<?php
namespace App\Traits;

use App\Core\QueryBuilderWithCache;

trait Cacheable
{
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilderWithCache(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor(),
            $this->cacheTime()
        );
    }

    protected function cacheTime()
    {
        return property_exists($this, 'cacheTime') ? $this->cacheTime : 0;
    }
}
