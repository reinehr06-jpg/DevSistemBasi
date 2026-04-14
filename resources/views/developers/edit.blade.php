@extends('layouts.app-dark')

@section('title', 'Editar Desenvolvedor')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">Editar Desenvolvedor</h1>
        <p style="color: var(--text-muted); margin-top: 4px;">Atualize os dados do desenvolvedor</p>
    </div>

    <form method="POST" action="{{ route('developers.update', $developer->id) }}" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Dados Básicos</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label>
                    <input type="text" name="name" value="{{ $developer->user->name }}" required style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Email *</label>
                    <input type="email" name="email" value="{{ $developer->user->email }}" required style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Status</label>
                    <select name="active" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="1" {{ $developer->active ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ !$developer->active ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Profissional</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Cargo *</label>
                    <select name="cargo" required style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="Junior" {{ $developer->cargo == 'Junior' ? 'selected' : '' }}>Junior</option>
                        <option value="Pleno" {{ $developer->cargo == 'Pleno' ? 'selected' : '' }}>Pleno</option>
                        <option value="Senior" {{ $developer->cargo == 'Senior' ? 'selected' : '' }}>Senior</option>
                        <option value="Lead" {{ $developer->cargo == 'Lead' ? 'selected' : '' }}>Lead</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Anos de Experiência</label>
                    <input type="number" name="experience_years" value="{{ $developer->experience_years }}" min="0" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Stack Principal</label>
                    <input type="text" name="stack_primary_text" value="{{ implode(', ', $developer->stack_primary ?? []) }}" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;" id="stackPrimaryEdit">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Stack Secundária</label>
                    <input type="text" name="stack_secondary_text" value="{{ implode(', ', $developer->stack_secondary ?? []) }}" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;" id="stackSecondaryEdit">
                </div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Organização</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Time</label>
                    <select name="team_id" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="">Selecione</option>
                        @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ $developer->team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Manager</label>
                    <select name="manager_id" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="">Selecione</option>
                        @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ $developer->manager_id == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Trabalho</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Horas por Dia</label>
                    <input type="number" name="hours_per_day" value="{{ $developer->hours_per_day }}" min="1" max="12" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Modo de Trabalho</label>
                    <select name="work_mode" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="remoto" {{ $developer->work_mode == 'remoto' ? 'selected' : '' }}>Remoto</option>
                        <option value="hibrido" {{ $developer->work_mode == 'hibrido' ? 'selected' : '' }}>Híbrido</option>
                        <option value="presencial" {{ $developer->work_mode == 'presencial' ? 'selected' : '' }}>Presencial</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" style="flex: 1; background: var(--success); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Salvar</button>
            <a href="{{ route('developers.show', $developer->id) }}" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer; text-align: center; text-decoration: none;">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const stackPrimary = document.getElementById('stackPrimaryEdit').value.split(',').map(s => s.trim()).filter(s => s);
    const stackSecondary = document.getElementById('stackSecondaryEdit').value.split(',').map(s => s.trim()).filter(s => s);
    
    const input1 = document.createElement('input');
    input1.type = 'hidden';
    input1.name = 'stack_primary';
    input1.value = JSON.stringify(stackPrimary);
    this.appendChild(input1);
    
    const input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'stack_secondary';
    input2.value = JSON.stringify(stackSecondary);
    this.appendChild(input2);
});
</script>
@endsection