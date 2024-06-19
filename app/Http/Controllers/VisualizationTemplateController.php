<?php

namespace App\Http\Controllers;

use App\Service\TemplateStores\VisualizationTemplateStore;

class VisualizationTemplateController extends Controller
{
    public function index()
    {
        $records = (new VisualizationTemplateStore())->getAll();
        return view('manage.templates.visualization.index', compact('records'));
    }

    public function delete(string $templateId)
    {
        $template = (new VisualizationTemplateStore())->get($templateId);
        $template->delete();
        return redirect()->route('manage.templates.visualization.index');
    }
}
