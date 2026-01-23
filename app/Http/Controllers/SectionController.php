<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function list()
    {
        $sections = Section::query()
            ->where('active', true)
            ->orderBy('sort')
            ->get();
    }

    public function detail(int $id)
    {
        $sections = Section::query()
            ->where('active', true)
            ->find($id);

        if (!$sections) {
            return $this->notFoundResponse();
        }

        return $this->okResponse($sections);
    }
}
