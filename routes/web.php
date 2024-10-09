<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AreaHierarchyController;
use App\Http\Controllers\AuthHomeController;
use App\Http\Controllers\DimensionTableCreationController;
use App\Http\Controllers\Guest\CensusTableController;
use App\Http\Controllers\Guest\DataExplorerController;
use App\Http\Controllers\Guest\DatasetController;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\Guest\MapVisualizationController;
use App\Http\Controllers\Guest\RendererController;
use App\Http\Controllers\Guest\StoryController;
use App\Http\Controllers\Guest\VisualizationController;
use App\Http\Controllers\Guest\VizAjaxController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSuspensionController;
use App\Http\Controllers\VizBuilderWizardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('landing', [LandingController::class, 'index'])->name('landing');
    Route::get('data-explorer', [DataExplorerController::class, 'index'])->name('data-explorer');
    Route::get('visualization', [VisualizationController::class, 'index'])->name('visualization.index');
    Route::get('map-visualization', [MapVisualizationController::class, 'index'])->name('map-visualization.index');
    Route::get('visualization/{visualization}', [VisualizationController::class, 'show'])->name('visualization.show');
    Route::get('story', [StoryController::class, 'index'])->name('story.index');
    Route::get('story/{story}', [StoryController::class, 'show'])->name('story.show');
    Route::get('census-table', [CensusTableController::class, 'index'])->name('census-table.index');
    Route::get('census-table/{id}', [CensusTableController::class, 'show'])->name('census-table.show');
    Route::get('census-table/download/{censusTable}', [CensusTableController::class, 'download'])->name('census-table.download');
    Route::get('dataset', [DatasetController::class, 'index'])->name('dataset.index');

    Route::view('about', 'guest.about')->name('about');
    Route::view('contact', 'guest.contact')->name('contact');
    Route::get('renderer/visualization/{visualization}', RendererController::class);
    Route::get('notification', NotificationController::class)->name('notification.index');
    Route::get('api/visualization/{visualization}', [VizAjaxController::class, 'show']);
    Route::get('api/visualization', [VizAjaxController::class, 'index']);

    Route::middleware(['auth:sanctum', 'verified', 'enforce_2fa'])->prefix('manage')->name('manage.')->group(function () {
        Route::get('/home', AuthHomeController::class)->name('home');
        Route::resource('topic', TopicController::class);
        Route::resource('indicator', IndicatorController::class);
        Route::get('dimension/create-table', DimensionTableCreationController::class)->name('dimension.create-table');
        //Route::delete('dimension/delete-table', \App\Http\Controllers\DimensionTableDeletionController::class)->name('dimension.delete-table');
        Route::resource('dimension', \App\Http\Controllers\DimensionController::class);
        Route::resource('year', \App\Http\Controllers\YearController::class);
        Route::resource('dimension.entries', \App\Http\Controllers\DimensionEntryController::class);
        Route::get('dataset/{dataset}/remove', \App\Http\Controllers\DatasetRemovalController::class)->name('dataset.remove');
        Route::get('dataset/{dataset}/truncate', \App\Http\Controllers\DatasetTruncationController::class)->name('dataset.truncate');
        Route::resource('dataset', \App\Http\Controllers\DatasetController::class);
        Route::resource('dataset.import', \App\Http\Controllers\DatasetImportController::class)->only(['create', 'store']);
        Route::resource('visualization', \App\Http\Controllers\VisualizationController::class)->except(['create', 'show']);
        Route::post('upload-visualization/{visualization}', [\App\Http\Controllers\VisualizationController::class, 'upload'])->name('visualization.upload');
        //Route::get('visualization-builder', \App\Http\Controllers\VisualizationBuilderController::class)->name('visualization-builder');
        //Route::get('visualization-deriver', \App\Http\Controllers\VisualizationDeriverController::class)->name('visualization-deriver');
        Route::get('story/{story}/duplicate', \App\Http\Controllers\StoryDuplicationController::class)->name('story.duplicate');
        Route::resource('story', \App\Http\Controllers\StoryController::class);

        /*Route::controller(VizBuilderWizardController::class)->group(function () {
            Route::get('viz-builder-wizard/{currentStep}', 'show')
                ->whereIn('currentStep', [1, 2, 3])
                ->name('viz-builder-wizard.show.{currentStep}');
            Route::post('viz-builder-wizard/{currentStep}', 'update')
                ->whereIn('currentStep', [1, 2, 3])
                ->name('viz-builder-wizard.update.{currentStep}');
        });*/

        Route::controller(\App\Http\Controllers\VizBuilder\ChartWizardController::class)->group(function () {
            Route::get('viz-builder/chart/prepare-data', 'prepareData')->name('viz-builder.chart.prepare-data');
            Route::get('viz-builder/chart/design', 'design')->name('viz-builder.chart.design');
            Route::get('viz-builder/chart/{viz}/edit', 'edit')->name('viz-builder.chart.edit');
            Route::post('viz-builder/chart/create', 'create')->name('viz-builder.chart.create');
            Route::post('viz-builder/chart', 'save')->name('viz-builder.chart.save');
        });

        Route::controller(\App\Http\Controllers\VizBuilder\TableWizardController::class)->group(function () {
            Route::get('viz-builder/table/prepare-data', 'prepareData')->name('viz-builder.table.prepare-data');
            Route::get('viz-builder/table/design', 'design')->name('viz-builder.table.design');
            Route::get('viz-builder/table/{viz}/edit', 'edit')->name('viz-builder.table.edit');
            Route::get('viz-builder/table/create', 'create')->name('viz-builder.table.create');
            Route::post('viz-builder/table', 'save')->name('viz-builder.table.save');
        });

        /*Route::get('viz-builder-wizard/api/get', [VizBuilderWizardController::class, 'ajaxGetChart']);
        Route::post('viz-builder-wizard/api/put', [VizBuilderWizardController::class, 'ajaxSaveChart']);*/
        Route::get('viz-builder/chart/api/get', [\App\Http\Controllers\VizBuilder\ChartWizardController::class, 'ajaxGetChart']);
        //Route::post('viz-builder/chart/api/put', [VizBuilderWizardController::class, 'ajaxSaveChart']);


        /*Route::get('viz-builder-wizard/{step}', \App\Http\Controllers\VizBuilderWizardController::class)
            ->whereIn('step', ['step1-data', 'step2-viz', 'step3-save']);*/

        Route::resource('story-builder', \App\Http\Controllers\StoryBuilderController::class)->parameters(['story-builder' => 'story'])->only(['edit', 'update']);

        Route::resource('announcement', AnnouncementController::class)->only(['index', 'create', 'store']);
        //Route::get('usage_stats', UsageStatsController::class)->name('usage_stats');

        Route::middleware(['can:Super Admin'])->group(function () {
            Route::resource('role', RoleController::class)->only(['index', 'store', 'edit', 'destroy']);
            Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
            Route::get('user/{user}/suspension', UserSuspensionController::class)->name('user.suspension');

            Route::resource('area-hierarchy', AreaHierarchyController::class);
            Route::resource('area', AreaController::class)->except(['destroy']);
            Route::delete('area/truncate', [AreaController::class, 'destroy'])->name('area.destroy');

            Route::get('organization', [\App\Http\Controllers\OrganizationController::class, 'edit'])->name('organization.edit');
            Route::patch('organization/{organization}', [\App\Http\Controllers\OrganizationController::class, 'update'])->name('organization.update');
            Route::resource('tag', \App\Http\Controllers\TagController::class)->only(['index', 'edit', 'update']);
            Route::name('templates.')->group(function () {
                //Route::resource('templates/visualization', \App\Http\Controllers\VisualizationTemplateController::class)->only(['index', 'destroy']);
                Route::resource('templates/story', \App\Http\Controllers\StoryTemplateController::class)->only(['index', 'destroy']);
            });
        });

        Route::resource('census-table', \App\Http\Controllers\CensusTableController::class)->only('index', 'create', 'store', 'edit', 'update', 'destroy');
    });

    Route::get('/', function () {
        return redirect()->route('landing');
    });

    Route::fallback(function () {
        return redirect()->route('landing');
    });
});

/*Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});*/
