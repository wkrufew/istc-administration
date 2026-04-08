<x-admin-layout>
    <div class="p-4 max-w-3xl mx-auto m-4 border-2 border-gray-50 rounded-lg">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Asignacion de un usuario a un rol</h1>
        </div>

        <div class="rounded-lg p-4">
            <div class="mb-4 space-y-4">
                <div class="grid grid-cols-12 gap-4 items-center">
                    <label for="name"
                        class="col-span-1 text-sm font-medium text-gray-800 dark:text-gray-100">Nombre:</label>
                    <div class="col-span-11">
                        <p
                            class="form-control block w-full px-3 py-2 text-base leading-6 text-gray-900 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            {{ $user->name }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <label for="email"
                        class="col-span-1 text-sm font-medium text-gray-800 dark:text-gray-100">Correo:</label>
                    <div class="col-span-11">
                        <p
                            class="form-control block w-full px-3 py-2 text-base leading-6 text-gray-900 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            {{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-4 flex flex-col justify-center items-center">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">LISTADO DE ROLES</h3>
                <form action="{{ route('administracion.administrativa.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @foreach ($roles as $role)
                        <div class="flex items-center mt-2">
                            <input type="radio" name="roles[]" value="{{ $role->id }}"
                                class="mr-2 rounded border-gray-300 text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50"
                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                            <label class="text-sm text-gray-800 dark:text-gray-100">{{ $role->name }}</label>
                        </div>
                    @endforeach
                    <div class="mt-4 flex justify-center">
                        <button type="submit"
                            class="mt-2 px-4 py-2 bg-lime-600 text-white rounded-full hover:bg-lime-700 focus:outline-none focus:bg-lime-700 text-sm font-semibold">Asignar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-admin-layout>
