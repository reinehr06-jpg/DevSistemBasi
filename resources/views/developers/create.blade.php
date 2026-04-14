@extends('layouts.app-dark')

@section('title', 'Novo Desenvolvedor')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: white;">Novo Desenvolvedor</h1>
        <p style="color: var(--text-muted); margin-top: 4px;">Cadastre um novo membro na equipe</p>
    </div>

    <form method="POST" action="{{ route('developers.store') }}" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px;">
        @csrf

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Dados Básicos</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label>
                    <input type="text" name="name" required style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Email *</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Senha</label>
                    <input type="password" name="password" placeholder="Auto-gerada se vazio" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Status</label>
                    <select name="active" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
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
                        <option value="Junior">Junior</option>
                        <option value="Pleno">Pleno</option>
                        <option value="Senior">Senior</option>
                        <option value="Lead">Lead</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Anos de Experiência</label>
                    <input type="number" name="experience_years" value="0" min="0" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Stack Principal</label>
                    <input type="text" name="stack_primary[]" placeholder="PHP, Laravel, MySQL" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;" id="stackPrimary">
                    <p style="color: var(--text-muted); font-size: 11px; margin-top: 4px;">Separe por vírgulas</p>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Stack Secundária</label>
                    <input type="text" name="stack_secondary[]" placeholder="React, Node.js" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;" id="stackSecondary">
                    <p style="color: var(--text-muted); font-size: 11px; margin-top: 4px;">Separe por vírgulas</p>
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
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Manager</label>
                    <select name="manager_id" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="">Selecione</option>
                        @foreach($managers as $manager)
                        <option value="{{ $manager->id }}">{{ $manager->name }}</option>
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
                    <input type="number" name="hours_per_day" value="8" min="1" max="12" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Custo/Hora</label>
                    <input type="number" name="cost_per_hour" step="0.01" placeholder="Opcional" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Timezone</label>
                    <select name="timezone" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="America/Sao_Paulo">São Paulo (GMT-3)</option>
                        <option value="America/Fortaleza">Fortaleza (GMT-3)</option>
                        <option value="America/Manaus">Manaus (GMT-4)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Modo de Trabalho</label>
                    <select name="work_mode" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="remoto">Remoto</option>
                        <option value="hibrido">Híbrido</option>
                        <option value="presencial">Presencial</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">IA</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="ai_monitoring" value="1" checked style="width: 18px; height: 18px;">
                        <span style="color: var(--text-gray);">Monitoramento por IA</span>
                    </label>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nível de Análise</label>
                    <select name="ai_level" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="basico">Básico</option>
                        <option value="completo">Completo</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <h3 style="color: white; font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">Permissões</h3>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Role</label>
                <select name="role" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <option value="developer">Developer</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" style="flex: 1; background: var(--success); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Criar Desenvolvedor</button>
            <a href="{{ route('developers.index') }}" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer; text-align: center; text-decoration: none;">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const stackPrimary = document.getElementById('stackPrimary').value.split(',').map(s => s.trim()).filter(s => s);
    const stackSecondary = document.getElementById('stackSecondary').value.split(',').map(s => s.trim()).filter(s => s);
    
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