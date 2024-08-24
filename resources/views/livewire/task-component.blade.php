<div wire:poll="renderAllTasks">
    <h1 class="text-blue-600 text-3xl">Bienvenido al gestor de tareas</h1>

    <h2 class="drop-shadow-[0_7px_4px_rgba(241,148,138,.6)] text-center font-bold text-[20px] transition-all duration-[.3s] hover:tracking-[.1em]">{{$poll ? 'Tareas eliminadas' : 'Mis tareas'}}</h2>

    @if ($poll)
        <button wire:click='myTasks' class="bg-orange-600 text-white px-2 my-2 border rounded hover:bg-orange-800 transition-all">Mis tareas</button>
    @else
        <button wire:click='openCreateTask' class="bg-blue-600 text-white px-2 my-2 border rounded hover:bg-blue-800 transition-all">Nuevo</button>
        <button wire:click='deletedTasks' class="bg-blue-600 text-white px-2 my-2 border rounded hover:bg-blue-800 transition-all">Tareas eliminadas</button>
    @endif

    <table class="w-full bg-slate-600">
        <thead class="text-white">
            <tr class="text-center font-bold {{$poll ? 'bg-red-600' : 'bg-blue-600'}}">
                <td class="border border-cyan-200 p-2"></td>
                <td class="border border-cyan-200 p-2">Título</td>
                <td class="border border-cyan-200 p-2">Descripción</td>
                <td class="border border-cyan-200">-</td>
            </tr>
        </thead>
        <tbody class="text-white">
            @if ($tasks->count() > 0)
                @foreach ($tasks as $task)
                    <tr class="text-center hover:bg-slate-500">
                        <td class="border border-cyan-200 p-2">
                            <p class="rounded-xl font-bold text-xs shadow {{$task->user_id === auth()->user()->id ? 'bg-cyan-700 shadow-cyan-500' : 'bg-yellow-700 shadow-yellow-500'}}">
                                {{$task->user_id === auth()->user()->id ? 'Propietario' : $task->user->name}}
                            </p>
                        </td>
                        <td class="border border-cyan-200 p-2">{{$task->title}}</td>
                        <td class="border border-cyan-200 p-2">{{$task->description}}</td>
                        <td class="border border-cyan-200 p-2 flex justify-center gap-2 h-fit">
                            @if ($task->user_id === auth()->user()->id)
                                @if ($poll)
                                    <button wire:click='restoreTasks({{$task->id}})' class="bg-blue-700 px-1 border rounded hover:bg-blue-800 transition-all">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                    </button>
                                @else
                                    <button wire:click='editTask({{$task}})' class="bg-blue-700 px-1 border rounded hover:bg-blue-800 transition-all">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button wire:click='openShareModal({{$task}})' class="bg-purple-700 px-1 border rounded hover:bg-purple-800 transition-all">
                                        <i class="fa-solid fa-share"></i>
                                    </button>
                                    <button wire:click='deleteTask({{$task->id}})' wire:confirm='¿Seguro de borrarlo?' class="bg-red-600 px-1 border rounded hover:bg-red-800 transition-all">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            @else
                                <button wire:click='taskUnshared({{$task->id}})' class="bg-amber-600 px-1 border rounded hover:bg-amber-800 transition-all">DES</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="3">
                        <p class="text-2xl py-4">No tienes tareas</p>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Modal --}}
    @if ($modal)
        <div class="bg-gray-800/40 w-full h-full absolute top-0 left-0 flex justify-center items-center">
            <div class="bg-white p-10 rounded-lg flex flex-col gap-4 w-1/2 transition-all">
                <h1 class="text-2xl font-bold w-full">{{$modal_crear ? 'Crear nueva tarea' : 'Edita la tarea'}}</h1>

                <form action="" class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label for="title" class="font-bold">Título</label>
                        <input wire:model='title' type="text" name="title" id="title" class="rounded-lg bg-gray-700 text-lg text-white" placeholder="Escriba el título">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="description" class="font-bold">Descripción</label>
                        <input wire:model='description' type="text" name="description" id="description" class="rounded-lg bg-gray-700 text-lg text-white" placeholder="Escriba la descripción">
                    </div>
                </form>

                <div class="flex gap-1">
                    <button wire:click='{{$modal_crear ? 'createTask' : 'updateTask'}}' class="bg-slate-800 text-white p-1 w-full rounded-3xl text-lg">{{$modal_crear ? 'Crear tarea' : 'Actualizar tarea'}}</button>
                    <button wire:click='closeCreateTask' class="border border-slate-800 text-slate-800 p-1 w-full rounded-3xl text-lg">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    @if ($modal_share)
        <div class="bg-gray-800/40 w-full h-full absolute top-0 left-0 flex justify-center items-center">
            <div class="bg-white p-10 rounded-lg flex flex-col gap-4 w-1/2 transition-all">
                <h1 class="text-2xl font-bold w-full">Compartir tarea</h1>

                <form action="" class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label for="user_id" class="font-bold">Usuario a compartir</label>
                        <select wire:model='user_id' name="user_id" id="user_id" class="rounded-lg bg-gray-700 text-lg text-white">
                            <option value="">Seleccione un usuario</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="flex flex-col gap-1">
                        <label for="description" class="font-bold">Permiso</label>
                        <select wire:model='user_id' name="user_id" id="user_id" class="rounded-lg bg-gray-700 text-lg text-white">
                            <option value="">Seleccione un usuario</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div> --}}
                </form>

                <div class="flex gap-1">
                    <button wire:click='shareTask' class="bg-slate-800 text-white p-1 w-full rounded-3xl text-lg">Compartir tarea</button>
                    <button wire:click='closeCreateTask' class="border border-slate-800 text-slate-800 p-1 w-full rounded-3xl text-lg">Cancelar</button>
                </div>
            </div>
        </div>
    @endif
</div>
