@extends('layouts.app-dark')

@section('title', 'Ranking')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Ranking</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Leaderboard da equipe</p>
        </div>
    </div>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('developers.ranking') }}" style="display: flex; gap: 12px;">
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

<div style="display: grid; gap: 16px;">
    @foreach($developers as $index => $dev)
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 20px;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $index < 3 ? '#fbbf24' : 'var(--bg-surface)' }}; display: flex; align-items: center; justify-content: center; color: {{ $index < 3 ? '#000' : 'var(--text-muted)' }}; font-weight: 700; font-size: 18px;">
            {{ $index + 1 }}
        </div>
        <div style="flex: 1;">
            <p style="color: white; font-weight: 600;">{{ $dev['name'] }}</p>
            <p style="color: var(--text-muted); font-size: 12px;">{{ $dev['cargo'] }}</p>
        </div>
        <div style="text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px;">Produtividade</p>
            <p style="color: var(--success); font-weight: 600; font-size: 18px;">{{ $dev['productivity'] }}</p>
        </div>
        <div style="text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px;">Tasks</p>
            <p style="color: white; font-weight: 600;">{{ $dev['tasks_completed'] }}</p>
        </div>
        <div style="text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px;">Bugs</p>
            <p style="color: #ef4444; font-weight: 600;">{{ $dev['bugs_created'] }}</p>
        </div>
        <div style="text-align: center;">
            <p style="color: var(--text-muted); font-size: 12px;">Qualidade</p>
            <p style="color: var(--primary); font-weight: 600;">{{ $dev['quality'] }}%</p>
        </div>
    </div>
    @endforeach
</div>
@endsection