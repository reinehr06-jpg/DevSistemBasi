@extends('layouts.app-dark')

@section('title', 'Desempenho')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Desempenho</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Métricas de performance da equipe</p>
        </div>
    </div>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('developers.performance') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
        <select name="period" style="padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            <option value="7" {{ request('period') == 7 ? 'selected' : '' }}>7 dias</option>
            <option value="15" {{ request('period') == 15 ? 'selected' : '' }}>15 dias</option>
            <option value="30" {{ request('period') == 30 ? 'selected' : '' }}>30 dias</option>
            <option value="60" {{ request('period') == 60 ? 'selected' : '' }}>60 dias</option>
            <option value="90" {{ request('period') == 90 ? 'selected' : '' }}>90 dias</option>
        </select>
        <button type="submit" style="background: var(--primary); color: white; padding: 12px 20px; border-radius: 8px; border: none; cursor: pointer;">Filtrar</button>
    </form>
</div>

<div style="overflow-x: auto; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 16px; text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 600;">Desenvolvedor</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Score</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Tasks</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Horas</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Bugs</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Eficiência</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $dev)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 16px; color: white; font-weight: 500;">{{ $dev['name'] }}</td>
                <td style="padding: 16px; text-align: center;"><span style="background: var(--primary); padding: 4px 12px; border-radius: 6px; color: white;">{{ $dev['score'] }}</span></td>
                <td style="padding: 16px; text-align: center; color: var(--success);">{{ $dev['tasks_completed'] }}</td>
                <td style="padding: 16px; text-align: center; color: white;">{{ $dev['hours_worked'] }}h</td>
                <td style="padding: 16px; text-align: center; color: #ef4444;">{{ $dev['bugs_created'] }}</td>
                <td style="padding: 16px; text-align: center;"><span style="color: {{ $dev['efficiency'] > 50 ? 'var(--success)' : '#ef4444' }};">{{ $dev['efficiency'] }}%</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-muted);">Sem dados no período</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection