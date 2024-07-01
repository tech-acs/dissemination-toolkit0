<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AreaHierarchyController;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSuspensionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('landing', [LandingController::class, 'index'])->name('landing');
    Route::get('data-explorer', [\App\Http\Controllers\Guest\DataExplorerController::class, 'index'])->name('data-explorer');
    Route::get('visualization', [\App\Http\Controllers\Guest\VisualizationController::class, 'index'])->name('visualization.index');
    Route::get('map-visualization', [\App\Http\Controllers\Guest\MapVisualizationController::class, 'index'])->name('map-visualization.index');
    Route::get('visualization/{visualization}', [\App\Http\Controllers\Guest\VisualizationController::class, 'show'])->name('visualization.show');
    Route::get('story', [\App\Http\Controllers\Guest\StoryController::class, 'index'])->name('story.index');
    Route::get('story/{story}', [\App\Http\Controllers\Guest\StoryController::class, 'show'])->name('story.show');
    Route::get('census-table', [\App\Http\Controllers\Guest\CensusTableController::class, 'index'])->name('census-table.index');
    Route::get('census-table/{id}', [\App\Http\Controllers\Guest\CensusTableController::class, 'show'])->name('census-table.show');
    Route::get('census-table/download/{censusTable}', [\App\Http\Controllers\Guest\CensusTableController::class, 'download'])->name('census-table.download');
    Route::view('about', 'guest.about')->name('about');
    Route::view('contact', 'guest.contact')->name('contact');
    Route::get('renderer/visualization/{visualization}', \App\Http\Controllers\Guest\RendererController::class);
    Route::get('notification', NotificationController::class)->name('notification.index');
    Route::get('api/visualization/{visualization}', [\App\Http\Controllers\Guest\VizAjaxController::class, 'show']);
    Route::get('api/visualization', [\App\Http\Controllers\Guest\VizAjaxController::class, 'index']);

    Route::middleware(['auth:sanctum', 'verified', 'enforce_2fa'])->prefix('manage')->name('manage.')->group(function () {
        Route::get('/home', \App\Http\Controllers\AuthHomeController::class)->name('home');
        Route::resource('topic', \App\Http\Controllers\TopicController::class);
        Route::resource('indicator', \App\Http\Controllers\IndicatorController::class);
        Route::get('dimension/create-table', \App\Http\Controllers\DimensionTableCreationController::class)->name('dimension.create-table');
        Route::delete('dimension/delete-table', \App\Http\Controllers\DimensionTableDeletionController::class)->name('dimension.delete-table');
        Route::resource('dimension', \App\Http\Controllers\DimensionController::class);
        Route::resource('year', \App\Http\Controllers\YearController::class);
        Route::resource('dimension.entries', \App\Http\Controllers\DimensionEntryController::class);
        Route::get('dataset/{dataset}/remove', \App\Http\Controllers\DatasetRemovalController::class)->name('dataset.remove');
        Route::get('dataset/{dataset}/truncate', \App\Http\Controllers\DatasetTruncationController::class)->name('dataset.truncate');
        Route::resource('dataset', \App\Http\Controllers\DatasetController::class);
        Route::resource('dataset.import', \App\Http\Controllers\DatasetImportController::class)->only(['create', 'store']);
        Route::resource('visualization', \App\Http\Controllers\VisualizationController::class)->except(['create', 'show']);
        Route::post('upload-visualization/{visualization}', [\App\Http\Controllers\VisualizationController::class, 'upload'])->name('visualization.upload');
        Route::get('visualization-builder', \App\Http\Controllers\VisualizationBuilderController::class)->name('visualization-builder');
        Route::get('visualization-deriver', \App\Http\Controllers\VisualizationDeriverController::class)->name('visualization-deriver');
        Route::get('story/{story}/duplicate', \App\Http\Controllers\StoryDuplicationController::class)->name('story.duplicate');
        Route::resource('story', \App\Http\Controllers\StoryController::class);

        Route::controller(\App\Http\Controllers\VizBuilderWizardController::class)->group(function () {
            Route::get('viz-builder-wizard/{currentStep}', 'show')
                ->whereIn('currentStep', [1, 2, 3])
                ->name('viz-builder-wizard.show.{currentStep}');
            Route::post('viz-builder-wizard/{currentStep}', 'update')
                ->whereIn('currentStep', [1, 2, 3])
                ->name('viz-builder-wizard.update.{currentStep}');
        });
        Route::post('viz-builder-wizard/api/put', [\App\Http\Controllers\VizBuilderWizardController::class, 'ajaxSaveChart']);
        Route::get('viz-builder-wizard/api/get', [\App\Http\Controllers\VizBuilderWizardController::class, 'ajaxGetChart']);

        /*Route::get('viz-builder-wizard/{step}', \App\Http\Controllers\VizBuilderWizardController::class)
            ->whereIn('step', ['step1-data', 'step2-viz', 'step3-save']);*/

        Route::resource('story-builder', \App\Http\Controllers\StoryBuilderController::class)->parameters(['story-builder' => 'story'])->only(['edit', 'update']);

        /*Route::get('story/builder/{story}/edit', [\App\Http\Controllers\StoryBuilderController::class, 'edit'])->name('story.builder.edit');
        Route::patch('story/builder/{id}', [\App\Http\Controllers\StoryBuilderController::class, 'update'])->name('story.builder.update');
        Route::post('story/builder/upload/image', [\App\Http\Controllers\StoryBuilderController::class, 'uploadImage'])->name('story.builder.upload.image');
        Route::post('story/builder/upload/file', [\App\Http\Controllers\StoryBuilderController::class, 'uploadFile'])->name('story.builder.upload.file');
        Route::get('story/builder/artifacts/{topic_id}', [\App\Http\Controllers\StoryBuilderController::class, 'getArtifacts'])->name('story.builder.artifacts');
        Route::get('story/builder/topics', [\App\Http\Controllers\StoryBuilderController::class, 'getTopics'])->name('story.builder.topics');*/

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
                Route::resource('templates/visualization', \App\Http\Controllers\VisualizationTemplateController::class)->only(['index', 'destroy']);
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
