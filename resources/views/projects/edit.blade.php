<x-app-layout>
    <div class="bg-gray-950">   
        <br>
        <div class="container flex flex-col p-6 rounded-3xl bg-gray-900 max-w-2xl ">
            <!-- En-tête -->
            <h1 style="font-size:40px; font-family:jetBrains Mono; color:#ffffff ">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-speedometer" viewBox="0 0 16 16">
                <!-- ... (votre icône) ... -->
                </svg> Modifier le projet
            </h1>
            <hr class="bg-gray-950 mt-3"><br> 

            <!-- Contenu de la page -->
            
            
                <div class=" mx-auto rounded-lg shadow-lg p-6 md:p-8   bg-gray-700 w-[500px] items-center justify-center " >
                    <form action="{{ route('projects.update', $project) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="task_name" class="block  text-sm font-medium mb-2 text-white">Nom du projet :</label>
                            <input type="text" id="task_name" name="project_name" value="{{ $project->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 text-gray-700">
                        </div>

                        <div class="flex justify-center mt-6">
                            <button type="submit" class="px-4 py-2 hite rounded-md  transition duration-300 badge-success" >
                                <i class="fa fa-check mr-2"></i> Mettre à jour
                            </button>
                            <a href="{{ route('projects.index') }}" class="ml-4 px-4 py-2 transition duration-300 badge-del" >
                                <i class="fa fa-times mr-2"></i> Annuler
                            </a>
                        </div>
                    </form>

                    <div class="bg-gray-100 p-6 mt-8 rounded-lg">
                        <form action="{{ route('projects.addUsers', $project) }}" method="post">
                            @csrf
                            <label for="user_ids" class="block text-gray-600 text-sm font-medium mb-2">Ajouter des utilisateurs :</label>
                            <select id="user_ids" name="user_ids[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500 text-gray-700 select2">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="flex justify-center mt-4">
                                <button type="submit" class="px-4 py-2   transition duration-300 badge-ter" >
                                    <i class="fa fa-user-plus mr-2"></i> Ajouter Utilisateurs
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            
        </div>
    </div>

    <style>
            
            .badge-success {
                background-color: blue;
            }
            .badge-warning {
                background-color: #ffff7f;
            }
            .badge-ter {
                background-color: #9fcbfe;
            }
            .badge-del {
                background-color: #ff7e70;
            }
        </style>
    
</x-app-layout>
