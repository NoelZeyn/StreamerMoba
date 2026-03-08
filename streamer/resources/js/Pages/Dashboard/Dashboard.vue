<template>

<div class="p-10">

<h1 class="text-2xl font-bold">
Dashboard
</h1>

<button
@click="logout"
class="bg-red-500 text-white px-4 py-2 mt-4 rounded"
>

Logout

</button>

</div>

</template>

<script>

export default{

methods:{

logout(){

localStorage.removeItem("auth_token")

this.$router.push("/login")

}

}

}

</script>