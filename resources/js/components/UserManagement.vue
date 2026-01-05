<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <!-- 🟢 1. เรียกใช้ Sidebar (สำคัญมาก!) -->
    <admin-sidebar></admin-sidebar>

    <!-- 🟡 2. เนื้อหาหลัก (ขยับขวาเพื่อหลบ Sidebar) -->
    <div class="lg:pl-64 transition-all duration-300">

        <!-- Header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">จัดการพนักงาน</h1>
                    </div>

                    <button
                        @click="openModal()"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none gap-2 shadow-md hover:shadow-lg transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        เพิ่มพนักงานใหม่
                    </button>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-7xl mx-auto p-6 pb-20">

            <!-- ตารางรายชื่อพนักงาน -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <span class="text-sm font-medium text-slate-500">พนักงานทั้งหมด {{ users.length }} คน</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th class="pl-6 py-4">ชื่อ-นามสกุล</th>
                                <th>Username (เข้าสู่ระบบ)</th>
                                <th>ตำแหน่ง (Role)</th>
                                <th>วันที่เริ่มงาน</th>
                                <th class="text-center w-32">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-indigo-50/30 transition-colors group">
                                <!-- ชื่อ -->
                                <td class="pl-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-indigo-100 text-indigo-600 rounded-full w-10 h-10 flex items-center justify-center font-bold text-lg border-2 border-white shadow-sm">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-700 text-base">{{ user.name }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Username -->
                                <td>
                                    <div class="font-mono text-slate-600 bg-slate-100 px-3 py-1 rounded-md inline-block text-xs font-bold tracking-wide border border-slate-200">
                                        @{{ user.username }}
                                    </div>
                                </td>

                                <!-- Role Badge -->
                                <td>
                                    <span :class="['px-3 py-1 rounded-full text-xs font-bold border', getRoleColor(user.role)]">
                                        {{ getRoleName(user.role) }}
                                    </span>
                                </td>

                                <!-- วันที่ -->
                                <td class="text-slate-500 text-sm font-medium">
                                    {{ new Date(user.created_at).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: '2-digit' }) }}
                                </td>

                                <!-- ปุ่มจัดการ -->
                                <td class="text-center">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            @click="openModal(user)"
                                            class="btn btn-sm btn-square btn-ghost text-amber-500 hover:bg-amber-50 hover:border-amber-200 border border-transparent transition-all"
                                            title="แก้ไข"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="btn btn-sm btn-square btn-ghost text-rose-500 hover:bg-rose-50 hover:border-rose-200 border border-transparent transition-all"
                                            :disabled="user.role === 'owner'"
                                            title="ลบ"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal เพิ่ม/แก้ไข -->
    <dialog class="modal backdrop-blur-sm" :class="{ 'modal-open': showModal }">
      <div class="modal-box w-11/12 max-w-lg bg-white p-0 overflow-hidden shadow-2xl rounded-2xl border border-slate-100">

        <!-- Modal Header -->
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                <span class="text-2xl">{{ isEditing ? '✏️' : '👤' }}</span>
                {{ isEditing ? 'แก้ไขข้อมูลพนักงาน' : 'เพิ่มพนักงานใหม่' }}
            </h3>
            <button @click="showModal = false" class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div class="p-6 space-y-5">
            <form @submit.prevent="submitUser">

                <div class="form-control w-full">
                    <label class="label pt-0 pb-1"><span class="label-text font-semibold text-slate-600">ชื่อ-นามสกุล <span class="text-red-500">*</span></span></label>
                    <input v-model="form.name" type="text" placeholder="เช่น สมชาย ใจดี" class="input input-bordered w-full bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-slate-700" required />
                </div>

                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text font-semibold text-slate-600">Username (สำหรับเข้าระบบ) <span class="text-red-500">*</span></span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-bold">@</span>
                        </div>
                        <input v-model="form.username" type="text" placeholder="staff01" class="input input-bordered w-full pl-8 bg-white focus:border-indigo-500 font-mono text-slate-700" required />
                    </div>
                </div>

                <div class="form-control w-full">
                    <label class="label pb-1">
                        <span class="label-text font-semibold text-slate-600">
                            รหัสผ่าน
                            <span v-if="isEditing" class="text-xs font-normal text-slate-400 ml-1">(เว้นว่างไว้ถ้าไม่เปลี่ยน)</span>
                            <span v-else class="text-red-500">*</span>
                        </span>
                    </label>
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="input input-bordered w-full pr-10 bg-white focus:border-indigo-500 text-slate-700"
                            :required="!isEditing"
                            placeholder="ตั้งรหัสผ่านใหม่"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                        >
                            <span v-if="showPassword">🙈</span>
                            <span v-else>👁️</span>
                        </button>
                    </div>
                </div>

                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text font-semibold text-slate-600">ตำแหน่ง (Role) <span class="text-red-500">*</span></span></label>
                    <select v-model="form.role" class="select select-bordered w-full bg-white focus:border-indigo-500 text-slate-700" required>
                        <option value="waiter">🤵 เด็กเสิร์ฟ (Waiter)</option>
                        <option value="kitchen">🍳 พ่อครัว (Kitchen)</option>
                        <option value="owner">👑 เจ้าของร้าน (Owner)</option>
                    </select>
                </div>

                <div class="mt-8 flex gap-3 justify-end border-t border-slate-100 pt-5">
                    <button type="button" @click="showModal = false" class="btn btn-ghost text-slate-500 hover:bg-slate-100">ยกเลิก</button>
                    <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none px-6 shadow-md hover:shadow-lg transition-all" :disabled="loading">
                        <span v-if="loading" class="loading loading-spinner loading-xs"></span>
                        {{ loading ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
                    </button>
                </div>

            </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-slate-900/20"><button @click="showModal = false">close</button></form>
    </dialog>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue'; // 🟢 Import Sidebar เข้ามา

const users = ref([]);
const showModal = ref(false);
const loading = ref(false);
const isEditing = ref(false);
const showPassword = ref(false);

const form = ref({
    id: null,
    name: '',
    username: '',
    password: '',
    role: 'waiter'
});

const fetchUsers = async () => {
    try {
        const res = await axios.get('/api/admin/users');
        users.value = res.data;
    } catch (e) { console.error(e); }
}

const openModal = (user = null) => {
    showPassword.value = false;
    if (user) {
        isEditing.value = true;
        form.value = {
            id: user.id,
            name: user.name,
            username: user.username,
            role: user.role,
            password: ''
        };
    } else {
        isEditing.value = false;
        form.value = { name: '', username: '', password: '', role: 'waiter' };
    }
    showModal.value = true;
}

const submitUser = async () => {
    loading.value = true;
    try {
        if (isEditing.value) {
            await axios.put(`/api/admin/users/${form.value.id}`, form.value);
            // alert('✅ แก้ไขข้อมูลเรียบร้อย');
        } else {
            await axios.post('/api/admin/users', form.value);
            // alert('✅ เพิ่มพนักงานเรียบร้อย');
        }

        showModal.value = false;
        fetchUsers();
    } catch (e) {
        alert('❌ เกิดข้อผิดพลาด (Username อาจซ้ำ)');
    } finally {
        loading.value = false;
    }
}

const deleteUser = async (id) => {
    if(!confirm('ต้องการลบบัญชีนี้ใช่ไหม?')) return;
    try {
        await axios.delete(`/api/admin/users/${id}`);
        fetchUsers();
    } catch (e) { alert('ลบไม่สำเร็จ'); }
}

// Helper แสดงผล Role + สี
const getRoleName = (role) => {
    const map = { owner: 'เจ้าของร้าน', kitchen: 'พ่อครัว', waiter: 'เด็กเสิร์ฟ' };
    return map[role] || role;
}
const getRoleColor = (role) => {
    const map = {
        owner: 'bg-indigo-50 text-indigo-700 border-indigo-200',
        kitchen: 'bg-orange-50 text-orange-700 border-orange-200',
        waiter: 'bg-emerald-50 text-emerald-700 border-emerald-200'
    };
    return map[role] || 'bg-slate-100 text-slate-600 border-slate-200';
}

onMounted(() => {
    fetchUsers();
});
</script>
