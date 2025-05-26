<script lang="ts" setup>
const user = ref({
  id: 1,
  lastname: 'Dupont',
  firstname: 'Jean',
  email: 'jean.dupont@example.com',
  role: 'user',
  createdAt: '2023-05-15'
})

const users = ref([
  {
    id: 1,
    lastname: 'Dupont',
    firstname: 'Jean',
    email: 'jean.dupont@example.com',
    role: 'user'
  },
  {
    id: 2,
    lastname: 'Martin',
    firstname: 'Sophie',
    email: 'sophie.martin@example.com',
    role: 'user'
  }
])
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <!-- Header -->
    <header class="bg-slate-800 p-4 shadow-md">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Mon Espace</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm">{{ user.firstname }} {{ user.lastname }}</span>
          <button class="p-2 rounded-full bg-slate-700 hover:bg-slate-600">
            <Icon name="heroicons:user-circle" class="w-5 h-5" />
          </button>
        </div>
      </div>
    </header>

    <div class="container mx-auto p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <aside class="md:col-span-1 bg-slate-800 rounded-lg p-4">
          <nav class="space-y-2">
            <NuxtLink 
              to="/dashboard" 
              class="flex items-center p-2 rounded-lg hover:bg-slate-700"
              activeClass="bg-blue-600"
            >
              <Icon name="heroicons:home" class="mr-2 w-5 h-5" />
              Tableau de bord
            </NuxtLink>
            <NuxtLink 
              to="/profile" 
              class="flex items-center p-2 rounded-lg hover:bg-slate-700"
              activeClass="bg-blue-600"
            >
              <Icon name="heroicons:user" class="mr-2 w-5 h-5" />
              Mon Profil
            </NuxtLink>
            <NuxtLink 
              to="/users" 
              class="flex items-center p-2 rounded-lg hover:bg-slate-700"
              activeClass="bg-blue-600"
            >
              <Icon name="heroicons:users" class="mr-2 w-5 h-5" />
              Utilisateurs
            </NuxtLink>
          </nav>
        </aside>

        <!-- Main Content -->
        <main class="md:col-span-3 space-y-6">
          <section class="bg-slate-800 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Mon Profil</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-slate-400 mb-1">Nom</label>
                <p class="p-2 bg-slate-700 rounded-lg">{{ user.lastname }}</p>
              </div>
              <div>
                <label class="block text-sm text-slate-400 mb-1">Prénom</label>
                <p class="p-2 bg-slate-700 rounded-lg">{{ user.firstname }}</p>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm text-slate-400 mb-1">Email</label>
                <p class="p-2 bg-slate-700 rounded-lg">{{ user.email }}</p>
              </div>
              <div>
                <label class="block text-sm text-slate-400 mb-1">Rôle</label>
                <p class="p-2 bg-slate-700 rounded-lg">{{ user.role }}</p>
              </div>
              <div>
                <label class="block text-sm text-slate-400 mb-1">Membre depuis</label>
                <p class="p-2 bg-slate-700 rounded-lg">{{ new Date(user.createdAt).toLocaleDateString() }}</p>
              </div>
            </div>
          </section>

          <section class="bg-slate-800 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Liste des Utilisateurs</h2>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-slate-700">
                  <tr>
                    <th class="p-3 text-left text-sm">Nom</th>
                    <th class="p-3 text-left text-sm">Email</th>
                    <th class="p-3 text-left text-sm">Rôle</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                  <tr v-for="u in users" :key="u.id" class="hover:bg-slate-700">
                    <td class="p-3">{{ u.firstname }} {{ u.lastname }}</td>
                    <td class="p-3">{{ u.email }}</td>
                    <td class="p-3">
                      <span class="px-2 py-1 text-xs rounded-full" 
                        :class="u.role === 'admin' ? 'bg-purple-600' : 'bg-blue-600'">
                        {{ u.role }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </main>
      </div>
    </div>
  </div>
</template>