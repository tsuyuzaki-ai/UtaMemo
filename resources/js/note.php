<?php

// 並び替え
function index()
{
    $repertoires = array_values($this->getDummyData());

    usort($repertoires, function($a, $b){
        if($a['is_favorite'] != $b['is_favorite']){
            return $b['is_favorite']
        }
    })
}