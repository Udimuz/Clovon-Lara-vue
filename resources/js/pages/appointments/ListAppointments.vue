<script setup>
import axios from "axios";
	import { onMounted, ref, computed } from 'vue';
	import Swal from 'sweetalert2';
	import { useToastr } from "@/toastr";
	import { Bootstrap4Pagination } from 'laravel-vue-pagination';

	const toastr = useToastr();

	const selectedStatus = ref();
	const appointmentStatus = ref([]);
	const getAppointmentStatus = () => {
		axios.get('/api/appointment-status')
				.then((response) => {
					appointmentStatus.value = response.data;
				})
				.finally(() => {
					toastr.info('Open list of Appointments ('+appointmentCount.value+')');
				});
	};
	//const appointmentStatus = {'scheduled': 1, 'confirmed': 2, 'cancelled': 3};
	const appointments = ref([]);
	const getAppointments = (status) => {
		selectedStatus.value = status;
		const params = {};
		if (status) {
			params.status = status;
			const stv = appointmentStatus.value;
			for (let key in appointmentStatus.value) {
				if (stv[key].value === status)
					toastr.success(stv[key].name+' ('+stv[key].count+')');
			}
			// toastr.success(appointmentStatus.value[status-1].name);
		}
		axios.get('/api/appointments', {
			params: params,
		})
			.then((response) => {
				appointments.value = response.data;
			})
	}

	const appointmentCount = computed(() => {
		return appointmentStatus.value.map(status => status.count).reduce((acc, value) => acc + value, 0);
	});

	const deleteAppointment = (id) => {
		Swal.fire({
			title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
			showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.isConfirmed) {
				axios.delete(`/api/appointments/${id}`)
					.then(() => {
						appointments.value.data = appointments.value.data.filter(appointment => appointment.id !== id)
						getAppointmentStatus();
						Swal.fire('Deleted!','Your file has been deleted.','success')
					});
			}
		})
	};

	const navAppointments = (page = 1) => {
		const params = {page: page};
		if (typeof selectedStatus.value !== 'undefined')
			params.status = selectedStatus.value;
		axios.get('/api/appointments', {
				params: params
		})
				.then((response) => {
					appointments.value = response.data;
				})
	};

	onMounted(() => {
		getAppointments();
		getAppointmentStatus();
		//navAppointments();
		//toastr.info('Open list of Appointments');
	});
</script>


<template>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Appointments</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
						<li class="breadcrumb-item active">Appointments</li>
					</ol>
				</div>
			</div>
		</div>
	</div>


	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="d-flex justify-content-between mb-2">
						<div>
							<router-link to="/admin/appointments/create">
								<button class="btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> Add New Appointment</button>
							</router-link>
						</div>
						<div class="btn-group">
							<button @click="getAppointments()" type="button"
											class="btn" :class="[typeof selectedStatus === 'undefined' ? 'btn-secondary' : 'btn-default']">
								<span class="mr-1">All</span>
								<span class="badge badge-pill badge-info">{{appointmentCount}}</span>
							</button>

							<button v-for="status in appointmentStatus" @click="getAppointments(status.value)" type="button"
											class="btn" :class="[selectedStatus === status.value ? 'btn-secondary' : 'btn-default']">
								<span class="mr-1">{{status.name}}</span>
								<span class="badge badge-pill" :class="`badge-${status.color}`">{{status.count}}</span>
							</button>

<!--								<button @click="getAppointments(appointmentStatus.confirmed)" type="button" class="btn btn-default">
									<span class="mr-1">Confirmed</span>
									<span class="badge badge-pill badge-success">0</span>
								</button>

								<button @click="getAppointments(appointmentStatus.cancelled)" type="button" class="btn btn-default">
									<span class="mr-1">Cancelled</span>
									<span class="badge badge-pill badge-danger">0</span>
								</button>-->
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<table class="table table-bordered">
								<thead>
								<tr>
									<th scope="col">№</th>
									<th scope="col">ID</th>
									<th scope="col">Title</th>
									<th scope="col">Client Name</th>
									<th scope="col">Date</th>
									<th scope="col">Time</th>
									<th scope="col">Status</th>
									<th scope="col">Options</th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
									<td>{{ index + 1 }}</td>
									<td>{{ appointment.id }}</td>
									<td>{{ appointment.title }}</td>
									<td>{{appointment.client.first_name}} {{appointment.client.last_name}}</td>
									<td>{{appointment.start_time}}</td>
									<td>{{appointment.end_time}}</td>
									<td>
										<span class="badge" :class="`badge-${appointment.status.color}`">{{appointment.status.name}}</span>
									</td>
									<td>
										<router-link :to="`/admin/appointments/${appointment.id}/edit`"><i class="fa fa-edit mr-2"></i></router-link>
										<a href="#" @click.prevent="$event => deleteAppointment(appointment.id)"><i class="fa fa-trash text-danger"></i></a>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<Bootstrap4Pagination :data="appointments" @pagination-change-page="navAppointments"/>
		</div>
	</div>

</template>