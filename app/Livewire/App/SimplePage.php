<?php

namespace App\Livewire\App;

class SimplePage
{
    public static function make(string $title, string $description): array
    {
        return compact('title', 'description');
    }
}
