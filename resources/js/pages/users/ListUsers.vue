<script setup>
	import axios from 'axios';
	import { ref, onMounted, reactive } from 'vue';
	import { Form, Field } from 'vee-validate';
	import * as yup from 'yup';
	// import { useToastr } from '../../toastr.js';
	import { useToastr } from "@/toastr";
	import { formatDate } from '../../helper.js';

	const toastr = useToastr();
	// const users1 = [{id: 1, name: 'John Doe', email: 'john@example.com'}, {id: 2, name: 'Dima Lazarev', email: 'dima@mail.com'}, ];
	const users = ref([]);
	// const form = reactive({name: '', email: '', password: ''});
	const editing = ref(false);
	const formValues = ref();
	const form = ref(null);

	const getUsers = () => {
		axios.get('/api/users')
			.then((response) => {
				users.value = response.data;
			})
	}
	const createUserSchema = yup.object({
		name: yup.string().required(),
		email: yup.string().email().required(),
		password: yup.string().required().min(6),
	});
	const editUserSchema = yup.object({
		name: yup.string().required(),
		email: yup.string().email().required(),
		password: yup.string().when((password, schema) => {
			//console.log(password);	// console.log("="+password+"=");
			return password[0] ? schema.required().min(6) : schema;
		}),
	});
	const createUser = (values, {resetForm, setErrors}) => {	// setFieldError
		axios.post('/api/users', values)
				.then((response) => {
					users.value.push(response.data);	// Чтобы изменения сразу отобразились на экране, добавить данные в массив users
					$('#userFormModal').modal('hide');
					resetForm();	// Очисткой полей теперь занимается эта функция, требует добавления в аргументах
					toastr.success('User created successfully');
				})
				.catch((error) => {
					//setFieldError('email', error.response.data.errors.email[0]);	//	'error'
					if (error.response.data.errors)
						setErrors(error.response.data.errors);
				})
	}
	// const createUser = () => {
	// 	axios.post('/api/users', form)
	// 			.then((response) => {
	// 				// Чтобы изменения сразу отобразились на экране, добавить данные в массив users:
	// 				users.value.push(response.data);
	// 				form.name = ''; form.email = ''; form.password = '';
	// 				$('#createUserModal').modal('hide');
	// 			});
	// }
	const addUser = () => {
		editing.value = false;
		formValues.value = {}		// Сам собрал, способ очищать данные формы, которые могут оставаться после редактирования данных. Всё равно, не всегда очищает
		//form.value.resetForm();
		//form.name = ''; form.email = ''; form.password = '';
		//formValues.value = { id: '', name: '', email: ''};
		$('#userFormModal').modal('show');
	};
	const editUser = (user) => {
		editing.value = true;
		form.value.resetForm();
		$('#userFormModal').modal('show');
		formValues.value = {
			id: user.id,
			name: user.name,
			email: user.email,
		};
		// Можно и так, но это даёт лишние параметры: время создания и т.д.:
		//formValues.value = user;
	};
	const updateUser = (values, {setErrors}) => {
		axios.put('/api/users/' + formValues.value.id, values)
				.then((response) => {
					const index = users.value.findIndex(user => user.id === response.data.id);
					users.value[index] = response.data;
					$('#userFormModal').modal('hide');
					toastr.success('User update successfully');
				}).catch((error) => {
					setErrors(error.response.data.errors);
					console.log(error);
				})
				// Учитель зачем-то убрал это: Почему не надо, не пойму, без этого - данные не очищаются, и при нажатии "Add New User" показывает старые данные
				.finally(() => {
					form.value.resetForm();
				});
	}
	const handleSubmit = (values, actions) => {
		// console.log(actions);
		if (editing.value)
			updateUser(values, actions)
		else
			createUser(values, actions)
	}

	const userIdBeingDeleted = ref(null);
	const confirmUserDeletion = (user) => {
		userIdBeingDeleted.value = user.id;
		$('#deleteUserModal').modal('show');
	};
	const deleteUser = () => {
		axios.delete(`/api/users/${userIdBeingDeleted.value}`)
				.then(() => {
					$('#deleteUserModal').modal('hide');
					toastr.success('User deleted successfully!');
					// В обучении на видео показывали так:
					users.value = users.value.filter(user => user.id !== userIdBeingDeleted.value);
					// А в коде Гитхаба так:
					// users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value);
				})
	};

	onMounted(() => {
		getUsers();
		toastr.info('Open list of Users');
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

			<button @click="addUser" type="button" class="mb-2 btn btn-primary"><i class="fa fa-plus-circle mr-2"></i>
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
							<td>{{ formatDate(user.created_at) }}</td>
							<td>{{ user.role }}</td>
							<td>
								<a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></a>
								<a href="#" @click.prevent="confirmUserDeletion(user)"><i class="fa fa-trash text-danger ml-2"></i></a>
							</td>
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
	<div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
			 aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">
						<span v-if="editing">Edit User</span>
						<span v-else>Add New User</span>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
							v-slot="{ errors }" :initial-values="formValues">
					<div class="modal-body">
						<div class="form-group">
							<label for="name">Name</label>
							<Field name="name" type="text" id="name" aria-describedby="nameHelp" :class="{ 'is-invalid': errors.name }" placeholder="Enter full name" class="form-control"/>
							<span class="invalid-feedback">{{ errors.name }}</span>
						</div>

						<div class="form-group">
							<label for="email">Email</label>
							<Field name="email" type="email" id="email" aria-describedby="nameHelp" :class="{ 'is-invalid': errors.email }" placeholder="Enter full name" class="form-control"/>
							<span class="invalid-feedback">{{ errors.email }}</span>
						</div>

						<div class="form-group">
							<label for="password">Password</label>
							<Field name="password" type="password" id="password" aria-describedby="nameHelp" :class="{ 'is-invalid': errors.password }" placeholder="Enter password" class="form-control"/>
							<span class="invalid-feedback">{{ errors.password }}</span>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</Form>
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