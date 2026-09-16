<?php

use App\Http\Controllers\Admin\AccessAuditController;
use App\Http\Controllers\Admin\AiMemoryController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GitHubViewController;
use App\Http\Controllers\Admin\MonitorController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectFileController;
use App\Http\Controllers\Admin\TuraCredentialController;
use Illuminate\Support\Facades\Route;

// Já carregado dentro do grupo 'web' (ver bootstrap/app.php). Aqui só auth + admin.
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'admin', 'password.changed'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Projetos (CRUD)
        Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projetos/novo', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projetos', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projetos/{project}/editar', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projetos/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projetos/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Arquivos de um projeto (upload/gerência)
        Route::get('/projetos/{project}/arquivos', [ProjectFileController::class, 'index'])->name('projects.files.index');
        Route::post('/projetos/{project}/arquivos', [ProjectFileController::class, 'store'])->name('projects.files.store');
        Route::patch('/projetos/{project}/arquivos/{file}/disponivel', [ProjectFileController::class, 'toggleAvailable'])->name('projects.files.available');
        Route::put('/projetos/{project}/arquivos/{file}', [ProjectFileController::class, 'update'])->name('projects.files.update');
        Route::delete('/projetos/{project}/arquivos/{file}', [ProjectFileController::class, 'destroy'])->name('projects.files.destroy');

        // Tura Notes — credenciais de sincronização. Fala com o notes-server
        // deste host através do wrapper tura-credential (ver TuraCredentials).
        Route::get('/tura', [TuraCredentialController::class, 'index'])->name('tura.index');
        Route::post('/tura', [TuraCredentialController::class, 'store'])->name('tura.store');
        Route::delete('/tura', [TuraCredentialController::class, 'destroy'])->name('tura.destroy');

        // Monitor de versões (forks OSS: nossa versão × upstream no GitHub)
        Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor.index');

        // Auditorias
        Route::get('/auditoria', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/auditoria-acesso', [AccessAuditController::class, 'index'])->name('access-audit.index');

        // AI-MEMORY — consulta SOMENTE LEITURA ao SQLite do ai-memory (ver docs/AI-MEMORY.md).
        // {hexId} = lower(hex(id)) de 32 chars (os ids do ai-memory são BLOB/UUIDv7).
        Route::prefix('ai-memory')->name('ai-memory.')->controller(AiMemoryController::class)
            ->where(['hexId' => '[0-9a-fA-F]{32}'])
            ->group(function () {
                Route::get('/', 'dashboard')->name('dashboard');
                Route::get('/live', 'live')->name('live');   // JSON do "ao vivo" do Dashboard
                Route::get('/projetos', 'projects')->name('projects');
                Route::get('/projetos/{hexId}', 'projectShow')->name('projects.show');
                Route::get('/workspaces', 'workspaces')->name('workspaces');
                Route::get('/paginas', 'pages')->name('pages');
                Route::get('/paginas/{hexId}', 'pageShow')->name('pages.show');
                Route::get('/sessoes', 'sessions')->name('sessions');
                Route::get('/sessoes/{hexId}', 'sessionShow')->name('sessions.show');
                Route::get('/observacoes', 'observations')->name('observations');
                Route::get('/observacoes/{hexId}', 'observationShow')->name('observations.show');
                Route::get('/handoffs', 'handoffs')->name('handoffs');
                Route::get('/handoffs/{hexId}', 'handoffShow')->name('handoffs.show');
                Route::get('/busca', 'search')->name('search');
            });

        // GitHub View — dashboard de visualização animada de repositórios do GitHub
        // (porte do github-visualize). Sync síncrono (sem queue:work). Ver
        // .continue/migracao-github-visualize.md (§7).
        Route::prefix('github-view')->name('github-view.')->controller(GitHubViewController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/suggestions', 'suggestions')->name('suggestions'); // autocomplete do add-form (JSON)
                Route::post('/repos', 'store')->name('repos.store');
                Route::post('/import', 'importAll')->name('import'); // importa todos os repos do usuário

                Route::prefix('repos/{owner}/{name}')
                    ->where(['owner' => '[\w.-]+', 'name' => '[\w.-]+'])
                    ->group(function () {
                        Route::get('/', 'show')->name('repos.show');
                        Route::delete('/', 'destroy')->name('repos.destroy');
                        Route::post('/sync', 'sync')->name('repos.sync');
                        Route::get('/status', 'status')->name('repos.status');
                    });
            });

        // Perfil / troca de senha
        Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile');
        Route::post('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
