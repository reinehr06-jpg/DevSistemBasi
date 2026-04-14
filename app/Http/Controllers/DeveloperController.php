<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\Team;
use App\Models\User;
use App\Models\DevTask;
use App\Models\Bug;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class DeveloperController extends Controller
{
    public function index(Request $request)
    {
        $query = Developer::with(['user', 'team']);

        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->cargo) {
            $query->where('cargo', $request->cargo);
        }

        if ($request->stack) {
            $query->where('stack_primary', 'like', "%{$request->stack}%");
        }

        if ($request->status) {
            $query->where('active', $request->status === 'ativo');
        }

        if ($request->score_min) {
            $query->where('score', '>=', $request->score_min);
        }

        if ($request->score_max) {
            $query->where('score', '<=', $request->score_max);
        }

        $developers = $query->orderBy('score', 'desc')->paginate(20);

        $stacks = Developer::whereNotNull('stack_primary')
            ->pluck('stack_primary')
            ->flatten()
            ->unique()
            ->values();

        return view('developers.index', compact('developers', 'stacks'));
    }

    public function create()
    {
        $teams = Team::where('active', true)->get();
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();

        return view('developers.create', compact('teams', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'cargo' => 'required|in:Junior,Pleno,Senior,Lead',
            'experience_years' => 'nullable|integer|min:0',
            'stack_primary' => 'nullable|array',
            'stack_secondary' => 'nullable|array',
            'team_id' => 'nullable|exists:teams,id',
            'manager_id' => 'nullable|exists:users,id',
            'hours_per_day' => 'nullable|integer|min:1|max:12',
            'cost_per_hour' => 'nullable|numeric|min:0',
            'timezone' => 'nullable|string',
            'work_mode' => 'nullable|in:remoto,hibrido,presencial',
            'ai_monitoring' => 'nullable|boolean',
            'ai_level' => 'nullable|in:basico,completo',
            'role' => 'nullable|in:developer,manager,admin',
        ]);

        $password = $validated['password'] ?? substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%'), 0, 12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => $validated['role'] ?? 'developer',
        ]);

        Developer::create([
            'user_id' => $user->id,
            'cargo' => $validated['cargo'],
            'experience_years' => $validated['experience_years'] ?? 0,
            'stack_primary' => $validated['stack_primary'] ?? [],
            'stack_secondary' => $validated['stack_secondary'] ?? [],
            'team_id' => $validated['team_id'] ?? null,
            'manager_id' => $validated['manager_id'] ?? null,
            'hours_per_day' => $validated['hours_per_day'] ?? 8,
            'cost_per_hour' => $validated['cost_per_hour'] ?? null,
            'timezone' => $validated['timezone'] ?? 'America/Sao_Paulo',
            'work_mode' => $validated['work_mode'] ?? 'remoto',
            'ai_monitoring' => $validated['ai_monitoring'] ?? true,
            'ai_level' => $validated['ai_level'] ?? 'basico',
            'role' => $validated['role'] ?? 'developer',
        ]);

        return redirect()->route('developers.index')->with('success', 'Desenvolvedor criado com sucesso');
    }

    public function show(Developer $developer)
    {
        $developer->load(['user', 'team', 'manager']);

        $tasks = DevTask::where('assigned_to', $developer->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $bugs = Bug::where('assigned_to', $developer->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $completedTasks = DevTask::where('assigned_to', $developer->user_id)
            ->where('status', 'concluido')
            ->count();

        $inProgressTasks = DevTask::where('assigned_to', $developer->user_id)
            ->where('status', 'em_andamento')
            ->count();

        $bugsCreated = Bug::where('created_by', $developer->user_id)->count();
        $bugsFixed = Bug::where('fixed_by', $developer->user_id)->count();

        return view('developers.show', compact(
            'developer', 'tasks', 'bugs',
            'completedTasks', 'inProgressTasks', 'bugsCreated', 'bugsFixed'
        ));
    }

    public function edit(Developer $developer)
    {
        $teams = Team::where('active', true)->get();
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();

        $developer->load('user');

        return view('developers.edit', compact('developer', 'teams', 'managers'));
    }

    public function update(Request $request, Developer $developer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $developer->user_id,
            'cargo' => 'required|in:Junior,Pleno,Senior,Lead',
            'experience_years' => 'nullable|integer|min:0',
            'stack_primary' => 'nullable|array',
            'stack_secondary' => 'nullable|array',
            'team_id' => 'nullable|exists:teams,id',
            'manager_id' => 'nullable|exists:users,id',
            'hours_per_day' => 'nullable|integer|min:1|max:12',
            'cost_per_hour' => 'nullable|numeric|min:0',
            'timezone' => 'nullable|string',
            'work_mode' => 'nullable|in:remoto,hibrido,presencial',
            'ai_monitoring' => 'nullable|boolean',
            'ai_level' => 'nullable|in:basico,completo',
            'role' => 'nullable|in:developer,manager,admin',
            'active' => 'nullable|boolean',
        ]);

        $developer->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $developer->update(Arr::except($validated, ['name', 'email']));

        return redirect()->route('developers.show', $developer->id)->with('success', 'Desenvolvedor atualizado');
    }

    public function destroy(Developer $developer)
    {
        $developer->update(['active' => false]);
        $developer->user->update(['active' => false]);

        return redirect()->route('developers.index')->with('success', 'Desenvolvedor desativado');
    }

    public function performance(Request $request)
    {
        $query = Developer::with(['user', 'team']);

        if ($request->developer_id) {
            $query->where('id', $request->developer_id);
        }

        $developers = $query->get();

        $period = $request->period ?? 30;
        $startDate = now()->subDays($period);

        $data = $developers->map(function ($dev) use ($startDate) {
            $tasksCompleted = DevTask::where('assigned_to', $dev->user_id)
                ->where('status', 'concluido')
                ->where('completed_at', '>=', $startDate)
                ->count();

            $bugsCreated = Bug::where('created_by', $dev->user_id)
                ->where('created_at', '>=', $startDate)
                ->count();

            $hoursWorked = \App\Models\WorkLog::where('user_id', $dev->user_id)
                ->where('created_at', '>=', $startDate)
                ->sum('hours');

            return [
                'id' => $dev->id,
                'name' => $dev->user->name,
                'score' => $dev->score,
                'tasks_completed' => $tasksCompleted,
                'bugs_created' => $bugsCreated,
                'hours_worked' => $hoursWorked,
                'efficiency' => $tasksCompleted > 0 && $hoursWorked > 0 
                    ? round(($tasksCompleted / $hoursWorked) * 100, 1) 
                    : 0,
            ];
        });

        return view('developers.performance', compact('data', 'developers'));
    }

    public function ranking(Request $request)
    {
        $period = $request->period ?? 30;
        $startDate = now()->subDays($period);

        $developers = Developer::with('user')
            ->where('active', true)
            ->get()
            ->map(function ($dev) use ($startDate) {
                $tasksCompleted = DevTask::where('assigned_to', $dev->user_id)
                    ->where('status', 'concluido')
                    ->where('completed_at', '>=', $startDate)
                    ->count();

                $bugsCreated = Bug::where('created_by', $dev->user_id)
                    ->where('created_at', '>=', $startDate)
                    ->count();

                $quality = $bugsCreated > 0 ? round(($tasksCompleted / ($tasksCompleted + $bugsCreated)) * 100, 1) : 100;

                return [
                    'id' => $dev->id,
                    'name' => $dev->user->name,
                    'cargo' => $dev->cargo,
                    'score' => $dev->score,
                    'tasks_completed' => $tasksCompleted,
                    'bugs_created' => $bugsCreated,
                    'quality' => $quality,
                    'productivity' => $tasksCompleted * 10 - $bugsCreated * 5,
                ];
            })
            ->sortByDesc('productivity')
            ->values();

        return view('developers.ranking', compact('developers', 'period'));
    }

    public function teams()
    {
        $teams = Team::with(['manager', 'developers.user'])
            ->withCount('developers')
            ->get();

        return view('developers.teams', compact('teams'));
    }

    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Team::create($validated);

        return back()->with('success', 'Time criado');
    }

    public function updateTeam(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $team->update($validated);

        return back()->with('success', 'Time atualizado');
    }

    public function destroyTeam(Team $team)
    {
        $team->update(['active' => false]);

        return back()->with('success', 'Time desativado');
    }
}