<script setup>
	import { formatDate } from '../../helper.js';
	import {ref} from "vue";
	import axios from "axios";
	import { useToastr } from "@/toastr";

	const toastr = useToastr();

	//const props =
			defineProps({
		user: Object,
		index: Number,
	});
	const emit = defineEmits(['editUser', 'confirmUserDeletion']);	// 'userDeleted'

	// Это уже не нужно:	Можно напрямую указывать функцию в ссылках таблицы	@click.prevent="$emit('editUser', user)"
	// const editUser = (user) => {
	// 	emit('editUser', user)
	// };

	const roles = ref([
		{
			name: 'ADMIN',
			value: 1
		},
		{
			name: 'USER',
			value: 2,
		}
	]);

	const changeRole = (user, role) => {
		axios.patch(`/api/users/${user.id}/change-role`, {
			role: role,
		})
		.then(() => {
			toastr.success('Role changed successfully!');
		})
	};
</script>

<template>
	<tr>
		<td>{{ index + 1 }}</td>
		<td>{{ user.id }}</td>
		<td>{{ user.name }}</td>
		<td>{{ user.email }}</td>
		<td>{{ formatDate(user.created_at) }}</td>
		<td>
			<select class="form-control" @change="changeRole(user, $event.target.value)">
				<option v-for="role in roles" :value="role.value" :selected="(user.role === role.name)">{{ role.name }}</option>
			</select>
		</td>
		<td>
			<!--a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></a-->
			<a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
			<a href="#" @click.prevent="$emit('confirmUserDeletion', user.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
		</td>
	</tr>

</template>
