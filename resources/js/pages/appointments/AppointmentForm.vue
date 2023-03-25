<script setup>
	import axios from 'axios';
	import {onMounted, reactive, ref} from "vue";
	import {useRouter, useRoute} from 'vue-router';
	import { useToastr } from "@/toastr";
	import { Form } from 'vee-validate';
	import flatpickr from "flatpickr";
	import 'flatpickr/dist/themes/light.css';
	import {Russian} from "flatpickr/dist/l10n/ru.js";

	const router = useRouter();
	const route = useRoute();
	const toastr = useToastr();
	const form = reactive({
		title: '',
		client_id: '',
		start_time: '',
		end_time: '',
		description: '',
		status: ''
	});

	const handleSubmit = (values, actions) => {
		if (editMode.value)
			editAppointment(values, actions)
		else
			createAppointment(values, actions)
	};

	const createAppointment = (values, actions) => {
		axios.post('/api/appointments/create', form)
				.then((response) => {
					//alert('Saved good')
					router.push('/admin/appointments');	// Затем, перенаправить на страницу списка задач
					toastr.success('Appointment created successfully')	// Вывести сообщение на экран
				})
				.catch((error) => {
					//toastr.warning('Not created')
					actions.setErrors(error.response.data.errors);
				})
	};

	const editAppointment = (values, actions) => {
		axios.put(`/api/appointments/${route.params.id}/edit`, form)
				.then((response) => {
					//alert('Saved good')
					router.push('/admin/appointments');	// Затем, перенаправить на страницу списка задач
					toastr.success('Appointment updated successfully')	// Вывести сообщение на экран
				})
				.catch((error) => {
					actions.setErrors(error.response.data.errors);
				})
	}

	const clients = ref();
	const getClients = () => {
		axios.get('/api/clients')
				.then((response) => {
					clients.value = response.data;
				})
	};

	const editMode = ref(false);
	const getAppointment = () => {
		axios.get(`/api/appointments/${route.params.id}/edit`)
				.then(({data}) => {	// Заполнение формы данными:	(response)
					form.title = data.title //response.data.title
					form.client_id = data.client_id
					form.start_time = data.formatted_start_time
					form.end_time = data.formatted_end_time
					form.description = data.description
					form.status = data.status
					flatpickr(".strdate", {
						enableTime: true,
						dateFormat: "d-m-Y H:i",
						locale: Russian,
						defaultDate: form.start_time
					});
					flatpickr(".enddate", {
						enableTime: true,
						dateFormat: "d-m-Y H:i",
						locale: Russian,
						defaultDate: form.end_time
					});
					//document.getElementById('end-time').flatpickr.defaultDate = form.end_time
					// let calendar = flatpickr(document.getElementById('end-time'));
					//calendar.changeMonth(0, false);
					// calendar.setDate(form.end_time, false, "d-m-Y H:i");
				})
	}

	//const statuses = [{id: 1, value: 'SCHEDULED'}, {id: 2, value: 'CONFIRMED'}, {id: 3, value: 'CANCELLED'}, ];
	const appointmentStatus = ref([]);
	// const appointmentStatus = ref({'data': []});
	const getAppointmentStatus = () => {
		//axios.get('/api/appointment-status')
		axios.get('/api/appointment/status')
				.then((response) => {
					appointmentStatus.value = response.data;
				})
	};

	onMounted(() => {
		if (route.name === 'admin.appointments.edit') {
			editMode.value = true;
			getAppointment()
		}
		// Настройки отображения даты:
		flatpickr(".flatpickr", {
			enableTime: true,
			dateFormat: "d-m-Y H:i",
			defaultHour: 10, // Начальное время предлагается с 10:00
			locale: Russian,
			//defaultDate: "20-10-2016 13:45"
		});
		//flatpickr.defaultDate = "20-10-2016 14:20"
		getClients();
		getAppointmentStatus();
	});
</script>

<template>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">
						<span v-if="editMode">Edit</span>
						<span v-else>Create</span>
						Appointment</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item">
							<router-link to="/admin/dashboard">Home</router-link>
						</li>
						<li class="breadcrumb-item">
							<router-link to="/admin/appointments">Appointments</router-link>
						</li>
						<li class="breadcrumb-item active">
							<span v-if="editMode">Edit</span>
							<span v-else>Create</span>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</div>

	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<Form @submit="handleSubmit" v-slot:default="{ errors }">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="title">Title</label>
											<input v-model="form.title" type="text" :class="{'is-invalid': errors.title}" class="form-control" id="title" placeholder="Enter Title">
											<span class="invalid-feedback">{{ errors.title }}</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="client">Имя клиента</label>
											<select v-model="form.client_id" :class="{'is-invalid': errors.client_id}" id="client" class="form-control">
												<option v-for="client in clients" :value="client.id" :key="client.id">{{client.first_name}} {{client.last_name}}</option>
											</select>
											<span class="invalid-feedback">{{ errors.client_id }}</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="start-time">Start time</label>
											<input v-model="form.start_time" type="text" class="form-control flatpickr strdate" :class="{'is-invalid': errors.start_time}" id="start-time">
											<span class="invalid-feedback">{{ errors.start_time }}</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="end-time">End Time</label>
											<input v-model="form.end_time" type="text" class="form-control flatpickr enddate" :class="{'is-invalid': errors.end_time}" id="end-time">
											<span class="invalid-feedback">{{ errors.end_time }}</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="description">Description</label>
											<textarea v-model="form.description" :class="{'is-invalid': errors.description}" class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
											<span class="invalid-feedback">{{ errors.description }}</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="client">Статус</label>
											<select v-model="form.status" :class="{'is-invalid': errors.status}" id="status" class="form-control">
												<option v-for="status in appointmentStatus" :value="status.value" :key="status.value">{{status.name}}</option>
											</select>
											<span class="invalid-feedback">{{ errors.status }}</span>
										</div>
									</div>
								</div>
								<button type="submit" class="btn btn-primary">Отправить</button>
							</Form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>