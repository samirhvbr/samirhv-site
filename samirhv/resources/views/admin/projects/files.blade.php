@extends('admin.layouts.app')

@section('title', 'Arquivos — '.$project->title)

@section('topbar-actions')
    <a href="{{ route('admin.projects.edit', $project) }}" class="admin-btn"><i class="fa-solid fa-pen"></i> Editar projeto</a>
    <a href="{{ route('admin.projects.index') }}" class="admin-btn">Voltar</a>
@endsection

@section('content')

    {{-- Upload --}}
    <div class="admin-card" style="max-width:760px">
        <h2>Enviar arquivo</h2>
        <form id="upload-form" method="POST" action="{{ route('admin.projects.files.store', $project) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <label for="file">Arquivo *</label>
                <input type="file" id="file" name="file" required>
                <div class="hint">Limite de 500&nbsp;MB pelo navegador. Para arquivos maiores, use no servidor: <code>php artisan files:add /caminho/arquivo --project={{ $project->slug }}</code></div>
                @error('file')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-row">
                    <label for="label">Rótulo (nome exibido)</label>
                    <input type="text" id="label" name="label" maxlength="255" placeholder="usa o nome do arquivo se vazio">
                </div>
                <div class="form-row">
                    <label for="version">Versão</label>
                    <input type="text" id="version" name="version" maxlength="30" placeholder="ex: 1.0.0">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                <div class="form-row">
                    <label for="os">Sistema operacional *</label>
                    <select id="os" name="os" required>
                        <option value="">— selecione —</option>
                        <option value="linux" @selected(old('os')==='linux')>Linux</option>
                        <option value="windows" @selected(old('os')==='windows')>Windows</option>
                        <option value="macos" @selected(old('os')==='macos')>macOS</option>
                    </select>
                    <div class="hint">Preenchido pelo nome do arquivo; ajuste se preciso.</div>
                    @error('os')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <label for="arch">Arquitetura</label>
                    <select id="arch" name="arch">
                        <option value="">— automática —</option>
                        <option value="x64" @selected(old('arch')==='x64')>x64</option>
                        <option value="arm64" @selected(old('arch')==='arm64')>arm64</option>
                        <option value="universal" @selected(old('arch')==='universal')>universal</option>
                    </select>
                    @error('arch')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <label for="file_type">Tipo</label>
                    <input type="text" id="file_type" name="file_type" maxlength="16" value="{{ old('file_type') }}" placeholder="deb, exe, msi…">
                    @error('file_type')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="upload-progress" style="display:none;margin-bottom:16px">
                <div style="background:var(--panel-2);border-radius:6px;height:10px;overflow:hidden">
                    <div id="upload-bar" style="height:100%;width:0;background:linear-gradient(90deg,var(--accent),rgba(99,102,241,.5));transition:width .15s ease"></div>
                </div>
                <div id="upload-pct" style="font-family:'JetBrains Mono',monospace;font-size:.74rem;color:var(--dim);margin-top:6px">0%</div>
            </div>
            <div id="upload-error" class="admin-alert admin-alert-error" style="display:none"></div>

            <button type="submit" id="upload-btn" class="admin-btn admin-btn-primary"><i class="fa-solid fa-upload"></i> Enviar</button>
        </form>
    </div>

    {{-- Lista --}}
    <div class="admin-card" style="padding:0;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Arquivo</th>
                    <th>SO</th>
                    <th>Versão</th>
                    <th>Tamanho</th>
                    <th>Downloads</th>
                    <th>Status</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    <tr>
                        <td>
                            <strong style="color:#f1f5f9">{{ $file->label }}</strong>
                            <div class="muted" style="font-family:'JetBrains Mono',monospace;font-size:.7rem">{{ $file->original_name }}@if($file->short_hash) · {{ $file->short_hash }}…@endif</div>
                        </td>
                        <td>
                            @if($file->os)
                                <span class="badge badge-muted" style="text-transform:capitalize">{{ $file->os }}</span>
                                @if($file->arch)<span class="muted" style="font-family:'JetBrains Mono',monospace;font-size:.66rem"> {{ $file->arch }}</span>@endif
                            @else
                                <span class="badge badge-warn">sem SO</span>
                            @endif
                        </td>
                        <td>{{ $file->version ? 'v'.$file->version : '—' }}</td>
                        <td>{{ $file->human_size }}</td>
                        <td><span style="font-family:'JetBrains Mono',monospace;color:#c7d2fe">{{ number_format($file->downloads_count, 0, ',', '.') }}</span></td>
                        <td>
                            @if(! $file->is_mirrored)
                                <span class="badge badge-warn">sem arquivo</span>
                            @elseif($file->is_available)
                                <span class="badge badge-ok">disponível</span>
                            @else
                                <span class="badge badge-muted">oculto</span>
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            <form method="POST" action="{{ route('admin.projects.files.available', [$project, $file]) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-btn admin-btn-sm">{{ $file->is_available ? 'Ocultar' : 'Disponibilizar' }}</button>
                            </form>
                            <button type="button" class="admin-btn admin-btn-sm" data-edit-file
                                data-action="{{ route('admin.projects.files.update', [$project, $file]) }}"
                                data-label="{{ $file->label }}"
                                data-version="{{ $file->version }}"
                                data-os="{{ $file->os }}"
                                data-arch="{{ $file->arch }}"
                                data-type="{{ $file->file_type }}"><i class="fa-solid fa-pen"></i> Editar</button>
                            <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" style="display:inline" onsubmit="return confirm('Remover o arquivo &quot;{{ $file->label }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="muted" style="text-align:center;padding:40px">Nenhum arquivo enviado ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal: editar metadados do arquivo (preenchido via JS a partir do botão Editar) --}}
    <dialog id="edit-modal" class="admin-card" aria-label="Editar arquivo">
        <h2 style="display:flex;align-items:center;gap:8px"><i class="fa-solid fa-pen"></i> Editar arquivo</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-row">
                <label for="edit-label">Rótulo (nome exibido)</label>
                <input type="text" id="edit-label" name="label" maxlength="255" placeholder="usa o nome do arquivo se vazio">
            </div>
            <div class="form-row">
                <label for="edit-version">Versão</label>
                <input type="text" id="edit-version" name="version" maxlength="30" placeholder="ex: 1.0.0">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                <div class="form-row">
                    <label for="edit-os">Sistema operacional *</label>
                    <select id="edit-os" name="os" required>
                        <option value="linux">Linux</option>
                        <option value="windows">Windows</option>
                        <option value="macos">macOS</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="edit-arch">Arquitetura</label>
                    <select id="edit-arch" name="arch">
                        <option value="">— automática —</option>
                        <option value="x64">x64</option>
                        <option value="arm64">arm64</option>
                        <option value="universal">universal</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="edit-type">Tipo</label>
                    <input type="text" id="edit-type" name="file_type" maxlength="16" placeholder="deb, exe, msi…">
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px">
                <button type="button" class="admin-btn admin-btn-sm" id="edit-cancel">Cancelar</button>
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm"><i class="fa-solid fa-check"></i> Salvar</button>
            </div>
        </form>
    </dialog>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/project-files.css') }}">
@endpush

@push('scripts')
<script>
(function () {
    const form = document.getElementById('upload-form');
    const btn = document.getElementById('upload-btn');
    const wrap = document.getElementById('upload-progress');
    const bar = document.getElementById('upload-bar');
    const pct = document.getElementById('upload-pct');
    const errBox = document.getElementById('upload-error');

    // Pré-preenche SO/arquitetura/tipo a partir do nome do arquivo escolhido
    // (espelha App\Support\FilenameInspector). O admin pode sobrescrever.
    function inferFromName(name) {
        const n = (name || '').toLowerCase();
        const ext = n.includes('.') ? n.split('.').pop() : '';
        let type;
        if (ext === 'deb') type = 'deb';
        else if (ext === 'rpm') type = 'rpm';
        else if (n.includes('.appimage')) type = 'appimage';
        else if (ext === 'exe') type = 'exe';
        else if (ext === 'msi') type = 'msi';
        else if (ext === 'dmg') type = 'dmg';
        else if (ext === 'pkg') type = 'pkg';
        else if (ext === 'zip') type = 'zip';
        else type = ext || '';
        let os = '';
        if (['deb', 'rpm', 'appimage'].includes(type) || n.includes('linux')) os = 'linux';
        else if (['exe', 'msi'].includes(type) || n.includes('win')) os = 'windows';
        else if (['dmg', 'pkg'].includes(type) || n.includes('mac') || n.includes('darwin')) os = 'macos';
        let arch = '';
        if (n.includes('arm64') || n.includes('aarch64')) arch = 'arm64';
        else if (n.includes('amd64') || n.includes('x86_64') || n.includes('x64')) arch = 'x64';
        else if (n.includes('universal')) arch = 'universal';
        // Versão: primeiro X.Y(.Z) no nome (o ponto exige — não casa "x86_64").
        const vm = (name || '').match(/\d+\.\d+(?:\.\d+)?/);
        const version = vm ? vm[0] : '';
        // Nome do produto: trecho antes da versão (separadores viram espaço).
        const base = (name || '').replace(/\.[^.]+$/, '');
        const cut = version ? base.indexOf(version) : -1;
        const before = cut > 0 ? base.slice(0, cut) : (version ? '' : base);
        const label = before.replace(/[_\-.]+/g, ' ').trim();
        return { os: os, arch: arch, file_type: type, version: version, label: label };
    }

    document.getElementById('file').addEventListener('change', function () {
        if (!this.files.length) return;
        const info = inferFromName(this.files[0].name);
        const osEl = document.getElementById('os');
        const archEl = document.getElementById('arch');
        const typeEl = document.getElementById('file_type');
        const labelEl = document.getElementById('label');
        const versionEl = document.getElementById('version');
        if (osEl && info.os) osEl.value = info.os;
        if (archEl && info.arch) archEl.value = info.arch;
        if (typeEl && info.file_type) typeEl.value = info.file_type;
        if (labelEl && info.label) labelEl.value = info.label;
        if (versionEl && info.version) versionEl.value = info.version;
    });

    form.addEventListener('submit', function (e) {
        const fileInput = document.getElementById('file');
        if (!fileInput.files.length) return; // deixa o required nativo agir

        e.preventDefault();
        errBox.style.display = 'none';
        wrap.style.display = 'block';
        btn.disabled = true;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function (ev) {
            if (ev.lengthComputable) {
                const p = Math.round((ev.loaded / ev.total) * 100);
                bar.style.width = p + '%';
                pct.textContent = p + '%';
            }
        });

        xhr.addEventListener('load', function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.reload();
            } else if (xhr.status === 413) {
                showError('Arquivo grande demais para o upload via navegador/servidor web. Suba pelo servidor: php artisan files:add /caminho --project={{ $project->slug }}');
            } else {
                let msg = 'Falha no envio (HTTP ' + xhr.status + ').';
                try { const j = JSON.parse(xhr.responseText); if (j.message) msg = j.message; } catch (_) {}
                showError(msg);
            }
        });
        xhr.addEventListener('error', function () { showError('Erro de rede durante o envio.'); });

        xhr.send(new FormData(form));
    });

    function showError(msg) {
        errBox.textContent = msg;
        errBox.style.display = 'block';
        wrap.style.display = 'none';
        btn.disabled = false;
    }
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('edit-modal');
    if (!modal || typeof modal.showModal !== 'function') return;
    const form = document.getElementById('edit-form');
    const set = function (id, v) { const el = document.getElementById(id); if (el) el.value = v || ''; };

    document.querySelectorAll('[data-edit-file]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = btn.dataset.action;
            set('edit-label', btn.dataset.label);
            set('edit-version', btn.dataset.version);
            set('edit-os', btn.dataset.os);
            set('edit-arch', btn.dataset.arch);
            set('edit-type', btn.dataset.type);
            modal.showModal();
        });
    });

    const cancel = document.getElementById('edit-cancel');
    if (cancel) cancel.addEventListener('click', function () { modal.close(); });
    // fecha ao clicar fora (no backdrop)
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.close(); });
})();
</script>
@endpush
