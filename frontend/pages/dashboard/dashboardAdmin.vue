<script lang="ts" setup>
const admin = ref({
  id: 1,
  lastname: 'Admin',
  firstname: 'Super',
  email: 'admin@example.com',
  role: 'admin',
  createdAt: '2023-01-10'
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
  },
  {
    id: 3,
    lastname: 'Admin',
    firstname: 'Super',
    email: 'admin@example.com',
    role: 'admin'
  }
])

const showModal = ref(false)
const currentUser = ref(null)
const isEditing = ref(false)

const openEditModal = (user = null) => {
  currentUser.value = user ? {...user} : { lastname: '', firstname: '', email: '', role: 'user', password: '' }
  isEditing.value = !!user
  showModal.value = true
}

const deleteUser = (userId) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
    users.value = users.value.filter(u => u.id !== userId)
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <!-- Header -->
    <header class="bg-slate-800 p-4 shadow-md">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Administration</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm">{{ admin.firstname }} {{ admin.lastname }}</span>
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
              to="/admin" 
              class="flex items-center p-2 rounded-lg hover:bg-slate-700"
              activeClass="bg-blue-600"
            >
              <Icon name="heroicons:chart-bar" class="mr-2 w-5 h-5" />
              Statistiques
            </NuxtLink>
            <NuxtLink 
              to="/admin/users" 
              class="flex items-center p-2 rounded-lg hover:bg-slate-700"
              activeClass="bg-blue-600"
            >
              <Icon name="heroicons:users" class="mr-2 w-5 h-5" />
              Gestion Utilisateurs
            </NuxtLink>
            <NuxtLink 
              to="/admin/settings" 
              class="flex items-center p-2 rounded-lg hover:bg-slate-700"
              activeClass="bg-blue-600"
            >
              <Icon name="heroicons:cog" class="mr-2 w-5 h-5" />
              Paramètres
            </NuxtLink>
          </nav>
        </aside>

        <!-- Main Content -->
        <main class="md:col-span-3 space-y-6">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Gestion des Utilisateurs</h2>
            <button 
              @click="openEditModal()"
              class="flex items-center bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg"
            >
              <Icon name="heroicons:plus" class="mr-1 w-4 h-4" />
              Ajouter
            </button>
          </div>

          <section class="bg-slate-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-slate-700">
                  <tr>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Rôle</th>
                    <th class="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                  <tr v-for="user in users" :key="user.id" class="hover:bg-slate-700">
                    <td class="p-3">{{ user.firstname }} {{ user.lastname }}</td>
                    <td class="p-3">{{ user.email }}</td>
                    <td class="p-3">
                      <span class="px-2 py-1 text-xs rounded-full" 
                        :class="user.role === 'admin' ? 'bg-purple-600' : 'bg-blue-600'">
                        {{ user.role }}
                      </span>
                    </td>
                    <td class="p-3 text-right">
                      <div class="flex justify-end space-x-2">
                        <button 
                          @click="openEditModal(user)"
                          class="p-1.5 text-blue-400 hover:bg-blue-900/50 rounded-lg"
                        >
                          <Icon name="heroicons:pencil-square" class="w-5 h-5" />
                        </button>
                        <button 
                          @click="deleteUser(user.id)"
                          class="p-1.5 text-red-400 hover:bg-red-900/50 rounded-lg"
                        >
                          <Icon name="heroicons:trash" class="w-5 h-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </main>
      </div>
    </div>

    <!-- Modal CRUD -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
      <div class="bg-slate-800 rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">
            {{ isEditing ? 'Modifier Utilisateur' : 'Nouvel Utilisateur' }}
          </h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-white">
            <Icon name="heroicons:x-mark" class="w-6 h-6" />
          </button>
        </div>

        <form class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-slate-400 mb-1">Nom</label>
              <input 
                v-model="currentUser.lastname"
                type="text"
                class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm text-slate-400 mb-1">Prénom</label>
              <input 
                v-model="currentUser.firstname"
                type="text"
                class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm text-slate-400 mb-1">Email</label>
            <input 
              v-model="currentUser.email"
              type="email"
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-400 mb-1">Rôle</label>
            <select 
              v-model="currentUser.role"
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="user">Utilisateur</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>

          <div v-if="!isEditing">
            <label class="block text-sm text-slate-400 mb-1">Mot de passe</label>
            <input 
              v-model="currentUser.password"
              type="password"
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button 
              type="button"
              @click="showModal = false"
              class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg"
            >
              Annuler
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg"
            >
              {{ isEditing ? 'Modifier' : 'Créer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>