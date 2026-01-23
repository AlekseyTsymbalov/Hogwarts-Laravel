<?php

namespace App\Http\Controllers;

use App\Models\Section;

class SectionsController extends Controller
{
    public function list()
    {
        $sections = Section::query()
            ->where('active', true)
            ->orderBy('sort')
            ->get();

        return $this->okResponse($sections);
    }

    public function detail(int $id)
    {
        $section = Section::query()
            ->where('active', true)
            ->find($id);

        if (!$section) {
            return $this->notFoundResponse();
        }

        return $this->okResponse($section);
    }
}
