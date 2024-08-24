<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaskComponent extends Component
{
    public $tasks = [];
    public $users = [];
    public $id;
    public $title;
    public $description;
    public $user_id;
    public $modal = false;
    public $modal_crear = false;
    public $modal_share = false;
    public $poll = false;

    public function mount()
    {
        $this->renderAllTasks();
    }

    public function renderAllTasks()
    {
        // $misTareas = Task::where('user_id', auth()->user()->id)->get();
        if ($this->poll) {
            $this->tasks = Task::onlyTrashed()->where('user_id', Auth::user()->id)->get();
        }else{
            $misTareas = Auth::user()->tasks;
            $misTareasCompartidas = Auth::user()->sharedTasks;
    
            $this->tasks = $misTareas->merge($misTareasCompartidas);
            
            $this->users = User::where('id','!=',Auth::user()->id)->get();
        }
    }

    public function render()
    {
        return view('livewire.task-component');
    }

    public function clearFields()
    {
        $this->title = '';
        $this->description = '';
    }

    public function openCreateTask()
    {
        $this->clearFields();
        $this->modal_crear = true;
        $this->modal = true;
    }

    public function closeCreateTask()
    {
        $this->modal = false;
        $this->modal_crear = false;
        $this->modal_share = false;
    }

    public function createTask()
    {
        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => Auth::user()->id,
        ]);

        $this->clearFields();
        $this->closeCreateTask();
        $this->mount();
    }

    public function editTask(Task $task){
        $this->id = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->modal = true;
    }

    public function updateTask()
    {
        $task = Task::find($this->id);

        $task->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->clearFields();
        $this->closeCreateTask();
        $this->mount();
    }

    public function deleteTask($id)
    {
        Task::destroy($id);
        $this->mount();
    }

    public function openShareModal(Task $task)
    {
        $this->id = $task->id; 
        $this->modal_share = true;
    }

    public function shareTask()
    {
        $user = User::find($this->user_id);
        $task = Task::find($this->id);

        $task->sharedWith()->attach($user->id, ['permission' => 'view']);
        // $user->sharedTasks()->attach($task->id, ['permission' => 'view']);

        $this->closeCreateTask();
        $this->mount();
    }

    public function taskUnshared($id)
    {
        $task = Task::find($id);
        $task->sharedWith()->detach(Auth::user()->id);
        $this->mount();
    }

    public function deletedTasks()
    {
        $this->poll = true;
        $this->mount();
    }

    public function restoreTasks($id)
    {
        $task = Task::withTrashed()->find($id);
        $task->restore(); // Restaurar el registro
        $this->mount();
    }

    public function myTasks()
    {
        $this->poll = false;
        $this->mount();
    }
}
