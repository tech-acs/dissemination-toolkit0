<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SourceRequest;
use App\Models\Source;
use Illuminate\Support\Facades\Log;

class SourceController extends Controller
{
    private array $databases = [
        'MySQL 5.7+/MariaDB 10.3+' => 'mysql',
        'PostgreSQL 10.0+' => 'pgsql',
        'SQLite 3.8.8+' => 'sqlite',
        'SQL Server 2017+' => 'sqlsrv',
    ];

    public function index()
    {
        $records = Source::orderBy('rank')->get();
        return view('manage.data-source.index', compact('records'));
    }

    public function create()
    {
        return view('manage.data-source.create')
            ->with(['databases' => $this->databases]);
    }

    public function store(SourceRequest $request)
    {
        dump('store');

        Source::create($request->only([
            'name', 'title', 'start_date', 'end_date', 'rank', 'host', 'port', 'database',
            'username', 'password', 'connection_active', 'driver'
        ]));
        return redirect()->route('manage.data-source.index')->withMessage('Record created');
    }

    public function edit(Source $dataSource)
    {
        return view('manage.data-source.edit', compact('dataSource'))
            ->with(['databases' => $this->databases]);
    }

    public function update(Source $source, SourceRequest $request)
    {
        dump('update');
        $source->update($request->only([
            'name', 'title', 'start_date', 'end_date', 'rank', 'host', 'port', 'database',
            'username', 'password', 'connection_active', 'driver'
        ]));
        // return redirect()->route('manage.data-source.index')->withMessage('Record updated');
    }

    public function destroy(Source $source)
    {
        $source->delete();
        return redirect()->route('manage.data-source.index')->withMessage('Record deleted');
    }

    public function test(Source $source)
    {
        $results = $source->test();
        $passesTest = $results->reduce(function ($carry, $item) {
            return $carry && $item['passes'];
        }, true);
        
        Log::info($results);
        if ($passesTest) {
            return redirect()->route('manage.data-source.index')
                ->withMessage('Connection test successful');
        } else {
            return redirect()->route('source.index')
                ->withErrors($results->pluck('message')->filter()->all());
        }
    }
}
