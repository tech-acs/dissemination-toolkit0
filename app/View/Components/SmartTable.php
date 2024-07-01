<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\SmartTableData;

class SmartTable extends Component
{
    public array $pageSizeOptions = [10, 20, 30, 40, 50, 75, 100, 200, 500, 1000];

    public function __construct(public SmartTableData $smartTableData, public ?string $customActionSubView = null)
    {
    }

    public function render()
    {
        return view('components.smart-table');
    }
}
