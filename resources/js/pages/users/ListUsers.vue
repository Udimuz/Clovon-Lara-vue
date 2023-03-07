<script setup>
	import axios from 'axios';
	import { ref, onMounted } from 'vue';
	// const users1 = [{
	// 	id: 1,
	// 	name: 'John Doe',
	// 	email: 'john@example.com'
	// },
	// 	{
	// 		id: 2,
	// 		name: 'Dima Lazarev',
	// 		email: 'dima@mail.com'
	// 	},
	// ];
	const users = ref([]);
	const getUsers = () => {
		axios.get(`/api/users`)
			.then((response) => {
				users.value = response.data;
			})
	}
	onMounted(() => {
		getUsers();
	});
</script>

<template>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Users</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Users</li>
					</ol>
				</div>
			</div>
		</div>
	</div>


	<div class="content">
		<div class="container-fluid">
			<div class="card">
				<div class="card-body">
					<table class="table table-bordered">
						<thead>
						<tr>
							<th style="width: 10px">#</th>
							<th>Name</th>
							<th>Email</th>
							<th>Registered Date</th>
							<th>Role</th>
							<th>Options</th>
						</tr>
						</thead>
						<tbody>
						<tr v-if="users.length > 0" v-for="(user, index) in users" :key="user.id">
							<td>{{ index + 1 }}</td>
							<td>{{ user.name }}</td>
							<td>{{ user.email }}</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
						</tr>
						</tbody>
<!--						<tbody v-if="users.data.length > 0">
						<UserListItem v-for="(user, index) in users.data"
													:key="user.id"
													:user=user :index=index
													@edit-user="editUser"
													@confirm-user-deletion="confirmUserDeletion"
													@toggle-selection="toggleSelection"
													:select-all="selectAll" />
						</tbody>
						<tbody v-else>
						<tr>
							<td colspan="6" class="text-center">No results found...</td>
						</tr>
						</tbody>-->
					</table>
				</div>
			</div>
		</div>
	</div>

</template>