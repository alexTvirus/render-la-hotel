<?php

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Collection;

function getInformationDataTable($pagination)
{
    return trans('project.data_table.information', [
        'first_item' => $pagination->firstItem(),
        'last_item' => $pagination->lastItem(),
        'total' => $pagination->total()
    ]);

}

function getArray($modal)
{
    if ($modal instanceof Collection) {
        $arrays = [];
        $modal->each(function ($item) use (&$arrays) {
            $arrays[] = $item->id;
        });
        return $arrays;
    }
    return null;
}


function getLinkSpinImg()
{
    return asset('assets/images/loadspinner.svg');
}

function getPaginationArrays($paginator)
{
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $min = 0;
    $max = $lastPage + 1;
    $tmp_min = $currentPage - 2;
    $tmp_max = $currentPage + 2;
    // next
    if ($currentPage - 1 <= $min) {
        $tmp_max++;
    }
    if ($currentPage - 2 <= $min) {
        $tmp_max++;
    }

    if ($currentPage + 1 >= $max) {
        $tmp_min--;
    }
    if ($currentPage + 2 >= $max) {
        $tmp_min--;
    }
    $tmp_min = $tmp_min <= $min ? 1 : $tmp_min;
    $tmp_max = $tmp_max > $lastPage ? $lastPage : $tmp_max;
    return [$tmp_min, $tmp_max];
}

function convertComicIdtoString($comicId)
{
    return convertString($comicId, config('settings.arrray_keys_convert_id'));
}

function convertString($input, $keyArray)
{
    $output = '';
    foreach (str_split($input) as $char) {
        $position = array_search($char, $keyArray);
        $output .= $position;
    }
    return $output;
}

function reverseConvert($input, $keyArray)
{
    $output = '';
    foreach (str_split($input) as $num) {
        $char = $keyArray[$num];
        $output .= $char;
    }
    return $output;
}
