<?php

namespace App\Modules\Gantt\Controllers;

use App\Core\Controllers\BaseModuleController;
use App\Models\Project;
use App\Modules\Gantt\Models\GanttTask;
use App\Modules\Gantt\Models\GanttTaskLink;
use App\Support\CurrentContext;
use App\Services\IdObfuscatorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ProjectGanttController extends BaseModuleController
{
    public function __construct(
        protected CurrentContext $currentContext,
        protected IdObfuscatorService $idObfuscatorService,
    ) {}

    public function main(): View
    {
        return view('gantt.main');
    }

    public function data(): JsonResponse
    {
        $taskRecords = GanttTask::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->orderBy('id')
            ->get();

        $taskIds = $taskRecords->pluck('id');

        $tasks = $taskRecords
            ->map(fn (GanttTask $task) => [
                'id' => $this->idObfuscatorService->encode($task->id),
                'database_id' => $this->idObfuscatorService->encode($task->id),
                'text' => $task->text,
                'parent' => $task->parent_id ? $this->idObfuscatorService->encode($task->parent_id) : '',
                'start_date' => $task->start_date,
                'end_date' => $task->end_date,
                'duration' => $task->calculateDurationInDays(),
                'type' => $task->type,
                'duration_unit' => 'day',
                'open' => true,
            ]);

        $links = GanttTaskLink::query()
            ->whereIn('source_task_id', $taskIds)
            ->whereIn('target_task_id', $taskIds)
            ->get()
            ->map(fn (GanttTaskLink $link) => [
                'id' => $this->idObfuscatorService->encode($link->id),
                'source' => $this->idObfuscatorService->encode($link->source_task_id),
                'target' => $this->idObfuscatorService->encode($link->target_task_id),
                'type' => $link->type,
            ]);

        return response()->json([
            'data' => $tasks,
            'links' => $links,
        ]);
    }

    public function calculateDuration(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $task = new GanttTask();
        $task->start_date = $request->start_date;
        $task->end_date = $request->end_date;

        return response()->json([
            'duration' => $task->calculateDurationInDays(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
       $request->validate([
        'text' => 'required|string|max:255',
        'parent' => 'nullable|string',
        'start_date' => 'nullable|required_with:end_date|date_format:Y-m-d H:i',
        'end_date' => 'nullable|required_with:start_date|date_format:Y-m-d H:i|after_or_equal:start_date',
        'type' => 'required|in:task,project,milestone',
    ]);

        Project::query()
            ->where('account_id', $this->currentContext->accountId())
            ->findOrFail($this->currentContext->projectId());

        $parentId = null;

        if ($request->parent) {
            $parentId = $this->idObfuscatorService->decode($request->parent);

            abort_if($parentId === null, 404);

            GanttTask::query()
                ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
                ->findOrFail($parentId);
        }

        $result = GanttTask::create([
            'text' => $request->text,
            'parent_id' => $parentId,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'account_id' => $this->currentContext->accountId(),
            'project_id' => $this->currentContext->projectId(),
        ]);

        return response()->json([
            'action' => 'inserted',
            'tid' => $this->idObfuscatorService->encode($result->id),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'text' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'required_with:end_date', 'date_format:Y-m-d H:i'],
            'end_date' => ['nullable', 'required_with:start_date', 'date_format:Y-m-d H:i', 'after_or_equal:start_date'],
            'type' => 'required|in:task,project,milestone',
        ]);

        $databaseId = $this->idObfuscatorService->decode($id);

        abort_if($databaseId === null, 404);

        $task = GanttTask::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->findOrFail($databaseId);

        $task->update([
            'text' => $request->text,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
        ]);

        return response()->json([
            'action' => 'updated',
            'tid' => $id,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $databaseId = $this->idObfuscatorService->decode($id);
        abort_if($databaseId === null, 404);

        $task = GanttTask::query()
            ->forCurrentTenant($this->currentContext->accountId(), $this->currentContext->projectId())
            ->findOrFail($databaseId);

        $task->delete();

        return response()->json([
            'action' => 'deleted',
            'tid' => $this->idObfuscatorService->encode($task->id),
        ]);
    }   

    public function previewImport(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|extensions:mpp',
        ]);

        Project::query()
            ->where('account_id', $this->currentContext->accountId())
            ->findOrFail($this->currentContext->projectId());

        $file = $request->file('file');

        try {
            $response = Http::timeout(80)
                ->attach('file', $file->get(), $file->getClientOriginalName())
                ->post('https://export.dhtmlx.com/gantt/project', [
                    'type' => 'msproject-parse',
                    'data' => json_encode([
                        'durationUnit' => 'day',
                        'taskProperties' => ['Summary', 'Milestone', 'Duration'],
                    ]),
                ]);
        } catch (ConnectionException $exception) {
            return response()->json(['message' => 'Unable to connect to the DHTMLX import service.'], 504);
        }

        abort_unless($response->successful(), 502, 'DHTMLX could not import the selected file.');

        $project = $response->json();
        $tasks = data_get($project, 'data.data') ?? data_get($project, 'data.tasks');
        $links = data_get($project, 'data.links', []);

        validator(['tasks' => $tasks, 'links' => $links], [
            'tasks' => 'required|array|min:1',
            'tasks.*.id' => 'required|integer|min:1|distinct',
            'tasks.*.parent' => 'nullable|integer|min:0',
            'links' => 'present|array',
            'links.*.source' => 'required|integer|min:1',
            'links.*.target' => 'required|integer|min:1',
        ])->validate();

        $sourceIds = array_fill_keys(array_column($tasks, 'id'), true);

        $tasks = collect($tasks)->map(function ($task) use ($sourceIds) {
            $parent = $task['parent'] ?? '';

            if ($parent && !isset($sourceIds[$parent])) {
                throw ValidationException::withMessages(['tasks' => 'Parent task tidak ditemukan dalam hasil impor.']);
            }

            $task['id'] = $this->idObfuscatorService->encode((int) $task['id']);
            $task['parent'] = $parent ? $this->idObfuscatorService->encode((int) $parent) : '';
            unset($task['database_id']);

            return $task;
        })->values()->all();

        $links = collect($links)->map(function ($link) use ($sourceIds) {
            if (!isset($sourceIds[$link['source']], $sourceIds[$link['target']])) {
                throw ValidationException::withMessages(['links' => 'Task penghubung tidak ditemukan dalam hasil impor.']);
            }

            $link['source'] = $this->idObfuscatorService->encode((int) $link['source']);
            $link['target'] = $this->idObfuscatorService->encode((int) $link['target']);

            return $link;
        })->values()->all();

        return response()->json([
            'data' => ['data' => $tasks, 'links' => $links],
            'config' => data_get($project, 'config', (object) []),
        ]);
    }

    public function storeImport(Request $request): JsonResponse
    {
        $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.id' => 'required|string|max:255|distinct:strict',
            'tasks.*.parent' => 'nullable|string|max:255',
            'tasks.*.text' => 'required|string|max:255',
            'tasks.*.start_date' => 'nullable|required_with:tasks.*.end_date|date_format:Y-m-d H:i',
            'tasks.*.end_date' => 'nullable|required_with:tasks.*.start_date|date_format:Y-m-d H:i|after_or_equal:tasks.*.start_date',
            'tasks.*.type' => 'required|in:task,project,milestone',
            'links' => 'sometimes|array',
            'links.*.source' => 'required|string|max:255',
            'links.*.target' => 'required|string|max:255',
            'links.*.type' => 'required|integer|between:0,3',
        ]);

        $accountId = $this->currentContext->accountId();
        $projectId = $this->currentContext->projectId();

        Project::query()
            ->where('account_id', $accountId)
            ->findOrFail($projectId);

        $pending = [];
        $originalIds = [];

        foreach ($request->tasks as $index => $row) {
            $sourceId = $this->idObfuscatorService->decode($row['id']);

            if ($sourceId === null || $sourceId < 1 || isset($originalIds[$sourceId])) {
                throw ValidationException::withMessages(["tasks.$index.id" => 'ID task tidak valid atau duplikat.']);
            }

            $parent = $row['parent'] ?? null;
            $hasParent = !in_array($parent, [null, '', 0, '0'], true);
            $parentId = $hasParent ? $this->idObfuscatorService->decode($parent) : null;

            if ($hasParent && ($parentId === null || $parentId < 1)) {
                throw ValidationException::withMessages(["tasks.$index.parent" => 'ID parent tidak valid.']);
            }

            $originalIds[$sourceId] = $row['id'];
            $row['id'] = $sourceId;
            $row['parent'] = $parentId;
            $pending[] = $row;
        }

        $pendingLinks = [];

        foreach ($request->input('links', []) as $index => $link) {
            $sourceId = $this->idObfuscatorService->decode($link['source']);
            $targetId = $this->idObfuscatorService->decode($link['target']);

            if (
                $sourceId === null || $targetId === null ||
                !isset($originalIds[$sourceId], $originalIds[$targetId])
            ) {
                throw ValidationException::withMessages([
                    "links.$index" => 'Task pada garis hubungan tidak ditemukan dalam hasil impor.',
                ]);
            }

            $pendingLinks[] = [
                'source' => $sourceId,
                'target' => $targetId,
                'type' => (int) $link['type'],
            ];
        }

        $taskIds = DB::transaction(function () use ($pending, $pendingLinks, $accountId, $projectId) {
            $taskIds = [];

            while (count($pending) > 0) {
                $progress = false;

                foreach ($pending as $index => $row) {
                    $parent = $row['parent'] ?? null;

                    if ($parent !== null && !array_key_exists($parent, $taskIds)) {
                        continue;
                    }

                    $task = GanttTask::create([
                        'account_id' => $accountId,
                        'project_id' => $projectId,
                        'parent_id' => $parent !== null ? $taskIds[$parent] : null,
                        'text' => $row['text'],
                        'start_date' => $row['start_date'] ?? null,
                        'end_date' => $row['end_date'] ?? null,
                        'type' => $row['type'],
                    ]);

                    $taskIds[$row['id']] = $task->id;
                    unset($pending[$index]);
                    $progress = true;
                }

                if (!$progress) {
                    throw ValidationException::withMessages([
                        'tasks' => 'Hubungan task dan subtask tidak valid.',
                    ]);
                }
            }

            foreach ($pendingLinks as $link) {
                GanttTaskLink::create([
                    'source_task_id' => $taskIds[$link['source']],
                    'target_task_id' => $taskIds[$link['target']],
                    'type' => $link['type'],
                ]);
            }

            return $taskIds;
        });

        $savedTaskIds = [];

        foreach ($taskIds as $sourceId => $databaseId) {
            $savedTaskIds[$originalIds[$sourceId]] = $this->idObfuscatorService->encode($databaseId);
        }

        return response()->json([
            'message' => 'Hasil impor berhasil disimpan.',
            'total' => count($taskIds),
            'task_ids' => (object) $savedTaskIds,
        ], 201);
    }
}   
