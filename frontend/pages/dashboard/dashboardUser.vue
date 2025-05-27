<script lang="ts" setup>
// Mock data for demonstration. In a real app, this would come from an API.
// We'll simulate fetching this data in a real scenario later.
const user = ref({
  id: 1,
  lastname: 'Dupont',
  firstname: 'Jean',
  email: 'jean.dupont@example.com',
  role: 'user',
  createdAt: '2023-05-15T10:00:00Z' // ISO 8601 format is better for dates
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
    role: 'admin' // Added an admin user for better role display testing
  },
  {
    id: 3,
    lastname: 'Dubois',
    firstname: 'Paul',
    email: 'paul.dubois@example.com',
    role: 'user'
  }
])

// You might fetch real user data here in a production app
// For example, on component mount or data refresh
// onMounted(async () => {
//   try {
//     const response = await $fetch('/api/user/current', { credentials: 'include' });
//     user.value = response.user; // Assuming your API returns user data
//
//     const usersResponse = await $fetch('/api/users', { credentials: 'include' });
//     users.value = usersResponse.users; // Assuming your API returns a list of users
//   } catch (e) {
//     console.error('Failed to load user data:', e);
//     // Handle error, e.g., redirect to login
//   }
// });
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-950 to-slate-800 text-slate-100 font-sans">
    <header class="bg-slate-800 p-4 shadow-xl">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-extrabold text-blue-400 tracking-wide">Dashboard</h1>
        <div class="flex items-center space-x-4">
          <span class="text-base text-slate-300 font-semibold hidden md:block">{{ user.firstname }} {{ user.lastname }}</span>
          <button
            class="p-2 rounded-full bg-slate-700 hover:bg-blue-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800"
            title="User Profile Options"
          >
            <Icon name="heroicons:user-circle" class="w-6 h-6 text-slate-300 hover:text-white" />
          </button>
        </div>
      </div>
    </header>

    <div class="container mx-auto p-6 md:p-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1 bg-slate-800 rounded-xl p-4 shadow-lg h-fit sticky top-8">
          <nav class="space-y-3">
            <NuxtLink
              to="/dashboard"
              class="flex items-center p-3 rounded-lg text-lg font-medium hover:bg-slate-700 transition-colors duration-200 group"
              activeClass="bg-blue-600 text-white shadow-md"
            >
              <Icon name="heroicons:home" class="mr-3 w-6 h-6 text-slate-300 group-hover:text-white group-[.router-link-active]:text-white transition-colors duration-200" />
              Tableau de bord
            </NuxtLink>
            <NuxtLink
              to="/profile"
              class="flex items-center p-3 rounded-lg text-lg font-medium hover:bg-slate-700 transition-colors duration-200 group"
              activeClass="bg-blue-600 text-white shadow-md"
            >
              <Icon name="heroicons:user" class="mr-3 w-6 h-6 text-slate-300 group-hover:text-white group-[.router-link-active]:text-white transition-colors duration-200" />
              Mon Profil
            </NuxtLink>
            <NuxtLink
              to="/users"
              class="flex items-center p-3 rounded-lg text-lg font-medium hover:bg-slate-700 transition-colors duration-200 group"
              activeClass="bg-blue-600 text-white shadow-md"
            >
              <Icon name="heroicons:users" class="mr-3 w-6 h-6 text-slate-300 group-hover:text-white group-[.router-link-active]:text-white transition-colors duration-200" />
              Utilisateurs
            </NuxtLink>
            <button
              class="flex items-center p-3 rounded-lg text-lg font-medium hover:bg-red-600 text-red-300 hover:text-white transition-colors duration-200 w-full text-left"
              title="Déconnexion"
            >
              <Icon name="heroicons:arrow-left-on-rectangle" class="mr-3 w-6 h-6 text-red-300 hover:text-white transition-colors duration-200" />
              Déconnexion
            </button>
          </nav>
        </aside>

        <main class="lg:col-span-3 space-y-6">
          <section class="bg-slate-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-blue-300">Mon Profil</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm text-slate-400 font-medium mb-1">Nom</label>
                <p class="p-3 bg-slate-700 rounded-lg text-lg text-white font-semibold">{{ user.lastname }}</p>
              </div>
              <div>
                <label class="block text-sm text-slate-400 font-medium mb-1">Prénom</label>
                <p class="p-3 bg-slate-700 rounded-lg text-lg text-white font-semibold">{{ user.firstname }}</p>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm text-slate-400 font-medium mb-1">Email</label>
                <p class="p-3 bg-slate-700 rounded-lg text-lg text-white font-semibold">{{ user.email }}</p>
              </div>
              <div>
                <label class="block text-sm text-slate-400 font-medium mb-1">Rôle</label>
                <p class="p-3 bg-slate-700 rounded-lg text-lg text-white font-semibold capitalize">{{ user.role }}</p>
              </div>
              <div>
                <label class="block text-sm text-slate-400 font-medium mb-1">Membre depuis</label>
                <p class="p-3 bg-slate-700 rounded-lg text-lg text-white font-semibold">{{ new Date(user.createdAt).toLocaleDateString() }}</p>
              </div>
            </div>
          </section>

          <section class="bg-slate-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-blue-300">Liste des Utilisateurs</h2>
            <div class="overflow-x-auto rounded-lg border border-slate-700">
              <table class="min-w-full divide-y divide-slate-700">
                <thead class="bg-slate-700">
                  <tr>
                    <th class="p-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Nom</th>
                    <th class="p-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Email</th>
                    <th class="p-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Rôle</th>
                  </tr>
                </thead>
                <tbody class="bg-slate-800 divide-y divide-slate-700">
                  <tr v-for="u in users" :key="u.id" class="hover:bg-slate-700 transition-colors duration-200">
                    <td class="p-4 whitespace-nowrap text-white">{{ u.firstname }} {{ u.lastname }}</td>
                    <td class="p-4 whitespace-nowrap text-slate-300">{{ u.email }}</td>
                    <td class="p-4 whitespace-nowrap">
                      <span
                        class="px-3 py-1 text-xs rounded-full font-semibold capitalize"
                        :class="u.role === 'admin' ? 'bg-purple-600 text-purple-100' : 'bg-blue-600 text-blue-100'"
                      >
                        {{ u.role }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="users.length === 0" class="text-center text-slate-400 py-8">
                Aucun utilisateur trouvé.
            </div>
          </section>
        </main>
      </div>
    </div>
  </div>
</template>