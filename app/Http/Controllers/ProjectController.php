<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\ProjectUpdatedNotification;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        // Récupérez la liste des projets de l'utilisateur connecté
        $user = auth()->user();
        $ownedProjects = $user->ownedProjects;
        $sharedProjects = $user->sharedProjects;

        return view('projects.index', compact('ownedProjects', 'sharedProjects'));
    }

    public function show(Project $project)
    {
        // Récupérer les tâches associées à ce projet
        $tasks = $project->tasks;

        return view('projects.show', compact('project', 'tasks'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_name' => 'required|max:255',
            
        ]);

        $project = auth()->user()->ownedProjects()->create([
            'name' => $validatedData['project_name'],
            'created_at' => now(),
            'deadline' => now(),
        ]);

        return redirect()->route('projects.index')->with('success', 'Projet créé avec succès.');
    }

    public function showt(Project $project)
    {
        // Récupérez les tâches assignées à ce projet
        $tasks = $project->tasks;

        // Vérifiez si l'utilisateur actuel est assigné à au moins une tâche du projet
        $user = Auth::user();

        $isAssigned = false;

        foreach ($tasks as $task) {
            if ($task->users->contains('id', $user->id)) {
                $isAssigned = true;
                break; // Sortez de la boucle dès qu'une correspondance est trouvée
            }
        }

        return view('projects.showt', compact('project', 'tasks', 'isAssigned'));
    }



    public function edit(Project $project)
    {
        $users = User::all(); // Récupérez tous les utilisateurs pour l'ajout d'utilisateurs au projet
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'project_name' => 'required|max:255',
            'user_ids' => 'array', // Assurez-vous que user_ids est un tableau
            'user_ids.*' => 'exists:users,id', // Assurez-vous que les IDs des utilisateurs existent dans la table users
        ]);

        $project->update([
            'name' => $validatedData['project_name'],
        ]);

        if (isset($validatedData['user_ids'])) {
            $project->users()->attach($validatedData['user_ids']);
        } else {
            $project->users()->attach([]); // Synchroniser avec un tableau vide pour supprimer toutes les associations
        }

        // Envoyer une notification aux utilisateurs associés au projet
        $associatedUsers = $project->users;
        foreach ($associatedUsers as $user) {
            $user->notify(new ProjectUpdatedNotification($project));
        }

        return redirect()->route('projects.index')->with('success', 'Projet mis à jour avec succès.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projet supprimé avec succès.');
    }

    public function addUsers(Request $request, Project $project)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'user_ids' => 'array',
        ]);

        // Ajouter des utilisateurs au projet
        $project->users()->attach($validatedData['user_ids']);

        // Envoyer des notifications par e-mail aux utilisateurs ajoutés
        $addedUsers = User::whereIn('id', $validatedData['user_ids'])->get();
        foreach ($addedUsers as $user) {
            // Envoyer la notification d'ajout au projet
            // $user->notify(new AddedToProjectNotification($project));
        }

        return redirect()->route('projects.index')->with('success', 'Utilisateurs ajoutés au projet.');
    }

    public function complete(Project $project)
    {
        $user = Auth::user();
        // Marquer le projet comme terminé
        $project->update([
            'completed' => true,
            'completed_by' => $user->id,
        ]);
        $project->load('users');

        // Envoyer des notifications par e-mail aux utilisateurs associés au projet
        $associatedUsers = $project->users;
        foreach ($associatedUsers as $user) {
            // Envoyer la notification d'achèvement du projet
            // $user->notify(new ProjectCompletedNotification($project));
        }

        return redirect()->route('projects.index')->with('success', 'Projet marqué comme terminé.');
    }

}
