<?php

namespace App\Http\Controllers;

use App\Service\TemplateStores\StoryTemplateStore;

class StoryTemplateController extends Controller
{
    public function index()
    {
        $records = (new StoryTemplateStore())->getAll();
        return view('manage.templates.story.index', compact('records'));
    }

    public function delete(string $templateId)
    {
        $template = (new StoryTemplateStore())->get($templateId);
        $template->delete();
        return redirect()->route('manage.templates.story.index');
    }
}
