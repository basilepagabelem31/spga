@extends('layouts.app')

@section('title', 'Gestion des Partenaires')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestion des Partenaires</h1>
        <button @click="showAddModal = true" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out flex items-center">
            <i class="fas fa-plus mr-2"></i> Ajouter un Partenaire
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Succès!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erreur!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Nom</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Téléphone</th>
                        <th class="py-3 px-6 text-left">Adresse</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($partners as $partner)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $partner->name }}</td>
                            <td class="py-3 px-6 text-left">{{ $partner->email }}</td>
                            <td class="py-3 px-6 text-left">{{ $partner->phone_number }}</td>
                            <td class="py-3 px-6 text-left">{{ $partner->address }}</td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-4">
                                    <button
                                        @click="openEditModal({{ $partner->id }}, '{{ $partner->name }}', '{{ $partner->email }}', '{{ $partner->phone_number }}', '{{ $partner->address }}')"
                                        class="w-6 h-6 transform hover:text-blue-500 hover:scale-110"
                                        title="Modifier"
                                    >
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button
                                        @click="openDeleteModal({{ $partner->id }})"
                                        class="w-6 h-6 transform hover:text-red-500 hover:scale-110"
                                        title="Supprimer"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 px-6 text-center text-gray-500">Aucun partenaire trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $partners->links() }}
        </div>
    </div>

    <div x-data="{ showAddModal: false, showEditModal: false, showDeleteModal: false, currentPartner: {}, deleteFormAction: '' }"
         x-init="
            window.openEditModal = (id, name, email, phone_number, address) => {
                currentPartner = { id: id, name: name, email: email, phone_number: phone_number, address: address };
                showEditModal = true;
            };
            window.openDeleteModal = (id) => {
                deleteFormAction = '{{ url('partners') }}/' + id;
                showDeleteModal = true;
            };
         ">

        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity" @click="showAddModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg z-50">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Ajouter un nouveau Partenaire</h3>
                    <form action="{{ route('partners.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="add_name" class="block text-gray-700 text-sm font-bold mb-2">Nom du partenaire:</label>
                            <input type="text" name="name" id="add_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="add_email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                            <input type="email" name="email" id="add_email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="add_phone_number" class="block text-gray-700 text-sm font-bold mb-2">Téléphone:</label>
                            <input type="text" name="phone_number" id="add_phone_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="add_address" class="block text-gray-700 text-sm font-bold mb-2">Adresse:</label>
                            <input type="text" name="address" id="add_address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="showAddModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition duration-300 ease-in-out">Annuler</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity" @click="showEditModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg z-50">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Modifier le Partenaire</h3>
                    <form x-bind:action="'{{ url('partners') }}/' + currentPartner.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nom du partenaire:</label>
                            <input type="text" name="name" id="edit_name" x-model="currentPartner.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                            <input type="email" name="email" id="edit_email" x-model="currentPartner.email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_phone_number" class="block text-gray-700 text-sm font-bold mb-2">Téléphone:</label>
                            <input type="text" name="phone_number" id="edit_phone_number" x-model="currentPartner.phone_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="edit_address" class="block text-gray-700 text-sm font-bold mb-2">Adresse:</label>
                            <input type="text" name="address" id="edit_address" x-model="currentPartner.address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="showEditModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition duration-300 ease-in-out">Annuler</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity" @click="showDeleteModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md z-50">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Confirmer la suppression</h3>
                    <p class="mb-6 text-gray-700">Êtes-vous sûr de vouloir supprimer ce partenaire ? Cette action est irréversible.</p>
                    <div class="flex justify-end">
                        <button type="button" @click="showDeleteModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition duration-300 ease-in-out">Annuler</button>
                        <form x-bind:action="deleteFormAction" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection