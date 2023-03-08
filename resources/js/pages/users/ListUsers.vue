<script setup>
	import axios from 'axios';
	import { ref, onMounted, reactive } from 'vue';
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
	const form = reactive({name: '', email: '', password: ''});
	const getUsers = () => {
		axios.get('/api/users')
			.then((response) => {
				users.value = response.data;
			})
	}
	const createUser = () => {
		axios.post('/api/users', form)
				.then((response) => {
					// Чтобы изменения сразу отобразились на экране, добавить данные в массив users:
					users.value.push(response.data);
					form.name = ''; form.email = ''; form.password = '';
					$('#createUserModal').modal('hide');
				});
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
						<li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
						<li class="breadcrumb-item active">Users</li>
					</ol>
				</div>
			</div>
		</div>
	</div>


	<div class="content">
		<div class="container-fluid">

			<button type="button" class="mb-2 btn btn-primary" data-toggle="modal" data-target="#createUserModal"><i class="fa fa-plus-circle mr-2"></i>
				Add New User
			</button>

			<div class="card">
				<div class="card-body">
					<table class="table table-bordered">
						<thead>
						<tr>
							<th style="width:20px">№</th>
							<th style="width:20px">ID</th>
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
							<td>{{ user.id }}</td>
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


	<!-- Модальная форма -->
	<div class="modal fade" id="createUserModal" data-backdrop="static" tabindex="-1" role="dialog"
			 aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Add New User</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
					<div class="modal-body">
						<form autocomplete="off">
						<div class="form-group">
							<label for="name">Name</label>
							<input name="name" v-model="form.name" type="text" id="name" aria-describedby="nameHelp" placeholder="Enter full name" class="form-control"/>
						</div>

						<div class="form-group">
							<label for="email">Email</label>
							<input name="email" v-model="form.email" type="email" id="email" aria-describedby="nameHelp" placeholder="Enter full name" class="form-control"/>
						</div>

						<div class="form-group">
							<label for="email">Password</label>
							<input name="password" v-model="form.password" type="password" id="password" aria-describedby="nameHelp" placeholder="Enter password" class="form-control"/>
						</div>

						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button @click="createUser" type="submit" class="btn btn-primary">Save</button>
					</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
			 aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">
						<span>Delete User</span>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<h5>Are you sure you want to delete this user ?</h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button @click.prevent="deleteUser" type="button" class="btn btn-primary">Delete User</button>
				</div>
			</div>
		</div>
	</div>


</template>