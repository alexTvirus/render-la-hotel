<?php


namespace App\Models\Traits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * Trait SearchableTrait
 * @package Nicolaslopezj\Searchable
 * @property array $searchable
 * @property string $table
 * @property string $primaryKey
 * @method string getTable()
 */
trait SearchableTraitExtend
{
    use SearchableTrait {
        SearchableTrait::scopeSearchRestricted as scopeSearchRestrictedParent;
    }

    public function scopeSearchRestricted(Builder $q, $search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
    {
        $query = clone $q;
        $query->select($this->getTable() . '.*');
        $this->makeJoins($query);
        if ($search === false) {
            return $q;
        }

        $search = mb_strtolower(trim($search));
        preg_match_all('/(?:")((?:\\\\.|[^\\\\"])*)(?:")|(\S+)/', $search, $matches);
        $words = $matches[1];
        for ($i = 2; $i < count($matches); $i++) {
            $words = array_filter($words) + $matches[$i];
        }

        $selects = [];
        $this->search_bindings = [];
        $relevance_count = 0;

        foreach ($this->getColumns() as $column => $relevance) {
            $relevance_count += $relevance;

            if (!$entireTextOnly) {
                $queries = $this->getSearchQueriesForColumn($column, $relevance, $words);
            } else {
                $queries = [];
            }

            if (($entireText === true && count($words) > 1) || $entireTextOnly === true) {
                $queries[] = $this->getSearchQuery($column, $relevance, [$search], 50, '', '');
                $queries[] = $this->getSearchQuery($column, $relevance, [$search], 30, '%', '%');
            }

            foreach ($queries as $select) {
                if (!empty($select)) {
                    $selects[] = $select;
                }
            }
        }

        $this->addSelectsToQuery($query, $selects);

        // Default the threshold if no value was passed.
        if (is_null($threshold)) {
            $threshold = $relevance_count / count($this->getColumns());
        }

        if (!empty($selects)) {
            $this->filterQueryWithRelevance($query, $selects, $threshold);
        }

        $this->makeGroupBy($query);

        if (is_callable($restriction)) {
            $query = $restriction($query);
        }
        if (method_exists($this, 'addJoinSub')) {
            $this->addJoinSub($query);
        }
        $this->mergeQueries($query, $q);
        return $q;
    }

}
