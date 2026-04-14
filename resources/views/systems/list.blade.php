@extends('layouts.app-dark')

@section('title', 'Sistemas')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Sistemas</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Lista de Sistemas</p>
        </div>
        <button onclick="openCreateModal()" style="background: var(--success); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            + Novo Sistema
        </button>
    </div>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('systems.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Buscar por nome..." value="{{ request('search') }}" style="flex: 1; min-width: 200px; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
        <select name="status" style="padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            <option value="">Todos Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativo</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativo</option>
        </select>
        <select name="language" style="padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            <option value="">Todas Linguagens</option>
            <option value="PHP">PHP</option>
            <option value="Node.js">Node.js</option>
            <option value="Python">Python</option>
            <option value="Go">Go</option>
        </select>
        <button type="submit" style="background: var(--primary); color: white; padding: 12px 20px; border-radius: 8px; border: none; cursor: pointer;">Filtrar</button>
    </form>
</div>

<div style="overflow-x: auto; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 16px; text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 600;">Nome</th>
                <th style="padding: 16px; text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 600;">Slug</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Cor</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Status</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Linguagem</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Framework</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Servidores</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($systems as $system)
            <tr style="border-bottom: 1px solid var(--border-color); cursor: pointer;" onclick="window.location='{{ route('systems.show', $system->id) }}'">
                <td style="padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="width: 12px; height: 12px; border-radius: 50%; background: {{ $system->color }};"></span>
                        <span style="color: white; font-weight: 500;">{{ $system->name }}</span>
                    </div>
                </td>
                <td style="padding: 16px; color: var(--text-muted);">/{{ $system->slug }}</td>
                <td style="padding: 16px; text-align: center;">
                    <span style="width: 24px; height: 24px; border-radius: 50%; display: inline-block; background: {{ $system->color }};"></span>
                </td>
                <td style="padding: 16px; text-align: center;">
                    <span class="{{ $system->active ? 'badge-online' : 'badge-offline' }}">{{ $system->active ? 'Ativo' : 'Inativo' }}</span>
                </td>
                <td style="padding: 16px; text-align: center;">
                    @if($system->detected_language)
                    <span style="background: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 11px; color: white;">{{ $system->detected_language }}</span>
                    @else
                    <span style="color: var(--text-muted);">-</span>
                    @endif
                </td>
                <td style="padding: 16px; text-align: center;">
                    @if($system->detected_framework)
                    <span style="background: #10b981; padding: 4px 8px; border-radius: 4px; font-size: 11px; color: white;">{{ $system->detected_framework }}</span>
                    @else
                    <span style="color: var(--text-muted);">-</span>
                    @endif
                </td>
                <td style="padding: 16px; text-align: center; color: var(--text-muted);">{{ $system->servers_count ?? 0 }}</td>
                <td style="padding: 16px; text-align: center;" onclick="event.stopPropagation()">
                    <div style="display: flex; gap: 4px; justify-content: center;">
                        <button onclick="openEditModal({{ $system->id }}, '{{ $system->name }}', '{{ $system->slug }}', '{{ $system->color }}', '{{ $system->repository_url }}', {{ $system->active ? 'true' : 'false' }})" style="background: var(--hover); color: var(--text-gray); padding: 6px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px;">Editar</button>
                        <button onclick="deleteSystem({{ $system->id }})" style="background: #ef4444; color: white; padding: 6px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px;">✕</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">Nenhum sistema encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 20px; display: flex; justify-content: center;">
    {{ $systems->links() }}
</div>

<style>
.badge-online { background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
.badge-offline { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
</style>
@endsection