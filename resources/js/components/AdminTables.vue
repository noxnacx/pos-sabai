<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <!-- 🟢 1. เรียกใช้ Sidebar -->
    <admin-sidebar></admin-sidebar>

    <!-- 🟡 2. เนื้อหาหลัก -->
    <div class="lg:pl-64 transition-all duration-300">

        <!-- Header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        </div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">จัดการโต๊ะอาหาร</h1>
                    </div>

                    <button
                        @click="addTable"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none gap-2 shadow-md"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        เพิ่มโต๊ะใหม่
                    </button>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-7xl mx-auto p-6 pb-20">

            <!-- ℹ️ ส่วนคำอธิบายสัญลักษณ์ (Legend) -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 mb-6 shadow-sm flex flex-wrap gap-4 items-center text-sm text-slate-600">
                <span class="font-bold text-slate-800 mr-2">สถานะ:</span>

                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> ว่าง
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-orange-500"></span> มีลูกค้า
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-slate-400"></span> ปิดปรับปรุง
                </div>

                <div class="h-4 w-px bg-slate-300 mx-2 hidden sm:block"></div>

                <span class="font-bold text-slate-800 mr-2 ml-2">ปุ่มกด:</span>

                <div class="flex items-center gap-2" title="กดเพื่อเปิด/ปิดการใช้งานโต๊ะ">
                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-xs">🚫</div>
                    = ปิด/เปิดโต๊ะ
                </div>
                <div class="flex items-center gap-2" title="ลิงก์สำหรับลูกค้าสแกน">
                    <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </div>
                    = หน้าลูกค้า
                </div>
                <div class="flex items-center gap-2" title="ลบโต๊ะนี้ออกจากระบบ">
                    <div class="w-6 h-6 rounded-full bg-red-50 text-red-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    = ลบโต๊ะ
                </div>
            </div>

            <!-- Grid แสดงโต๊ะ -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">

                <div
                    v-for="table in tables"
                    :key="table.id"
                    :class="['card bg-white shadow-sm border-2 transition-all hover:shadow-md group relative overflow-hidden', getBorderColor(table.status)]"
                >
                    <div class="card-body p-5 items-center text-center relative z-10">

                        <!-- Status Indicator (มุมขวาบน) -->
                        <div :class="['absolute top-3 right-3 w-3 h-3 rounded-full ring-2 ring-white', getBadgeColor(table.status)]"></div>

                        <!-- Icon/Status -->
                        <div :class="['w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-3 transition-colors shadow-sm', getBgColor(table.status)]">
                            {{ getIcon(table.status) }}
                        </div>

                        <h2 class="card-title text-slate-700 text-lg">{{ table.name }}</h2>

                        <div :class="['badge badge-sm font-bold border-none text-white mb-4 px-3 py-2 h-auto', getBadgeColor(table.status)]">
                            {{ getStatusText(table.status) }}
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 w-full justify-center mt-auto pt-3 border-t border-slate-100">

                            <!-- ปุ่ม Toggle เปิด/ปิด -->
                            <div class="tooltip tooltip-bottom" :data-tip="table.status === 'hidden' ? 'เปิดใช้งานโต๊ะนี้' : 'ปิดปรับปรุง (ลูกค้าจะเข้าไม่ได้)'">
                                <button
                                    @click="toggleStatus(table)"
                                    class="btn btn-sm btn-circle btn-ghost hover:bg-slate-100 transition-colors"
                                >
                                    <span v-if="table.status === 'hidden'" class="text-emerald-500 text-lg">✅</span>
                                    <span v-else class="text-slate-400 text-lg">🚫</span>
                                </button>
                            </div>

                            <!-- ปุ่ม QR Code -->
                            <div class="tooltip tooltip-bottom" data-tip="ไปที่หน้าสั่งอาหาร (จำลองสแกน QR)">
                                <a
                                    :href="`/table/${table.id}`"
                                    target="_blank"
                                    class="btn btn-sm btn-circle btn-ghost text-indigo-500 hover:bg-indigo-50 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </div>

                            <!-- ปุ่มลบ -->
                            <div class="tooltip tooltip-bottom" data-tip="ลบโต๊ะนี้ออกจากระบบ">
                                <button
                                    @click="deleteTable(table.id)"
                                    class="btn btn-sm btn-circle btn-ghost text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white to-slate-50 opacity-50 z-0"></div>
                </div>

            </div>

            <!-- Empty State -->
            <div v-if="tables.length === 0" class="text-center py-24 bg-white rounded-xl border border-dashed border-slate-300">
                <div class="text-6xl mb-4 grayscale opacity-50">🪑</div>
                <h3 class="text-lg font-bold text-slate-700">ยังไม่มีโต๊ะในระบบ</h3>
                <p class="text-slate-500 mt-2">กดปุ่มมุมขวาบนเพื่อเริ่มสร้างผังร้าน</p>
            </div>

        </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue';

const tables = ref([]);

const fetchTables = async () => {
    try {
        const res = await axios.get('/api/admin/tables');
        tables.value = res.data;
    } catch (e) { console.error(e); }
}

const addTable = async () => {
    const name = prompt("กรุณาใส่ชื่อโต๊ะ (เช่น โต๊ะ 10, VIP 1):");
    if (!name) return;

    try {
        await axios.post('/api/admin/tables', { name });
        fetchTables();
    } catch (e) { alert('ชื่อโต๊ะซ้ำ หรือเกิดข้อผิดพลาด'); }
}

const toggleStatus = async (table) => {
    const newStatus = table.status === 'hidden' ? 'available' : 'hidden';
    try {
        await axios.put(`/api/admin/tables/${table.id}`, { status: newStatus });
        fetchTables();
    } catch (e) { alert('Error updating status'); }
}

const deleteTable = async (id) => {
    if (!confirm('ต้องการลบโต๊ะนี้ใช่ไหม? (หากมีออเดอร์ค้างอยู่ อาจเกิดปัญหาได้)')) return;
    try {
        await axios.delete(`/api/admin/tables/${id}`);
        fetchTables();
    } catch (e) { alert('ลบไม่สำเร็จ'); }
}

// Helpers สีและไอคอน
const getBorderColor = (s) => {
    if (s === 'occupied') return 'border-orange-400 bg-orange-50/10';
    if (s === 'hidden') return 'border-slate-200 bg-slate-50 opacity-75 grayscale';
    return 'border-emerald-400 hover:border-emerald-500 bg-emerald-50/10';
}
const getBgColor = (s) => {
    if (s === 'occupied') return 'bg-white text-orange-500 border border-orange-100';
    if (s === 'hidden') return 'bg-slate-100 text-slate-400 border border-slate-200';
    return 'bg-white text-emerald-600 border border-emerald-100';
}
const getBadgeColor = (s) => {
    if (s === 'occupied') return 'bg-orange-500';
    if (s === 'hidden') return 'bg-slate-400';
    return 'bg-emerald-500';
}
const getStatusText = (s) => {
    const map = { available: 'ว่าง', occupied: 'มีลูกค้า', hidden: 'ปิดปรับปรุง' };
    return map[s] || s;
}
const getIcon = (s) => {
    if (s === 'occupied') return '🙇‍♂️';
    if (s === 'hidden') return '🚫';
    return '🪑';
}

onMounted(() => {
    fetchTables();
    setInterval(fetchTables, 5000);
});
</script>
