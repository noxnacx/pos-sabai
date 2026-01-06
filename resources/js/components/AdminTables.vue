<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <admin-sidebar></admin-sidebar>

    <div class="lg:pl-64 transition-all duration-300">
        <div class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <h1 class="text-xl font-bold text-slate-800">จัดการโต๊ะอาหาร</h1>
                    <div class="flex gap-2">
                        <button @click="fetchTables" class="btn btn-ghost btn-sm text-slate-500">↻ รีเฟรช</button>
                        <button @click="addTable" class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none gap-2 shadow-md btn-sm">
                            + เพิ่มโต๊ะใหม่
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto p-6">

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">

                <div v-for="table in tables" :key="table.id"
                    :class="['card shadow-lg border-2 transition-all duration-200 hover:shadow-xl relative overflow-hidden', getBorderColor(table.status)]">

                    <div class="card-body p-6 items-center text-center relative z-10">

                        <div :class="['w-16 h-16 rounded-full flex items-center justify-center mb-2 text-3xl shadow-sm transition-all', getBgColor(table.status)]">
                            {{ getIcon(table.status) }}
                        </div>

                        <h2 class="card-title text-2xl font-black text-slate-700">{{ table.name }}</h2>

                        <div class="badge mt-2 font-bold p-3" :class="getBadgeColor(table.status)">
                            {{ getStatusText(table.status) }}
                        </div>

                        <div class="card-actions mt-6 w-full flex flex-col gap-2">

                            <button v-if="table.status === 'available' || table.status === 'hidden'"
                                @click="openTable(table)"
                                class="btn btn-primary w-full shadow-md text-white border-none bg-indigo-600 hover:bg-indigo-700">
                                🔓 เปิดโต๊ะรับลูกค้า
                            </button>

                            <template v-else-if="table.status === 'occupied'">
                                <button @click="showQR(table)" class="btn btn-outline btn-success w-full gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                    ดู QR Code
                                </button>
                                <button @click="forceCloseTable(table)" class="btn btn-ghost btn-xs text-red-400 hover:text-red-600 hover:bg-red-50">
                                    ยกเลิก/เช็คบิลมือ
                                </button>
                            </template>

                            <button v-if="table.status !== 'occupied'" @click="deleteTable(table.id)" class="btn btn-ghost btn-xs text-slate-400 hover:text-red-500 mt-2">
                                ลบโต๊ะนี้
                            </button>

                        </div>
                    </div>

                    <div v-if="table.status === 'hidden'" class="absolute inset-0 bg-slate-100/50 z-0 pointer-events-none"></div>
                </div>

            </div>

            <div v-if="tables.length === 0" class="text-center py-20 text-slate-400 border-2 border-dashed border-slate-200 rounded-xl">
                ยังไม่มีโต๊ะในระบบ กด "เพิ่มโต๊ะใหม่" มุมขวาบน
            </div>
        </div>
    </div>

    <dialog id="qr_table_modal" class="modal">
        <div class="modal-box text-center max-w-sm bg-white border-t-8 border-indigo-500 p-0 overflow-hidden">
            <div class="bg-indigo-50 p-6 pb-2">
                <h3 class="font-bold text-2xl mb-1 text-indigo-900">สแกนเพื่อสั่งอาหาร</h3>
                <p class="text-indigo-600 font-medium text-lg">โต๊ะ: <span class="font-black text-2xl">{{ currentTable?.name }}</span></p>
            </div>

            <div class="p-6">
                <div class="bg-white p-2 inline-block rounded-xl shadow-[0_0_20px_rgba(0,0,0,0.1)] border border-slate-100 mb-4">
                    <img :src="qrUrl" class="w-64 h-64 object-contain" alt="QR Code">
                </div>

                <div class="text-xs text-slate-400 mb-6 bg-slate-50 p-3 rounded-lg border border-slate-100 break-all font-mono select-all">
                    {{ customerLink }}
                </div>

                <form method="dialog">
                    <button class="btn btn-primary w-full bg-indigo-600 hover:bg-indigo-700 text-white border-none shadow-lg shadow-indigo-200 text-lg" @click="fetchTables">
                        เรียบร้อย / ปิดหน้าต่าง
                    </button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-black/60"><button>close</button></form>
    </dialog>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue';

const tables = ref([]);
const currentTable = ref(null);
const qrUrl = ref('');
const customerLink = ref('');

const fetchTables = async () => {
    try {
        const res = await axios.get('/api/admin/tables');
        tables.value = res.data;
    } catch (e) { console.error(e); }
}

const addTable = async () => {
    const name = prompt("กรุณาใส่ชื่อโต๊ะ (เช่น โต๊ะ 1, VIP 2):");
    if (!name) return;
    try {
        // สร้างโต๊ะใหม่ สถานะ default คือ 'hidden' (ปิดอยู่)
        await axios.post('/api/admin/tables', { name, status: 'hidden' });
        fetchTables();
    } catch (e) { alert('ไม่สามารถสร้างโต๊ะได้ (ชื่ออาจซ้ำ)'); }
}

const deleteTable = async (id) => {
    if(!confirm('ยืนยันลบโต๊ะนี้?')) return;
    try { await axios.delete(`/api/admin/tables/${id}`); fetchTables(); } catch (e) { alert('ลบไม่สำเร็จ'); }
}

// ✨ ฟังก์ชันเปิดโต๊ะ: เปลี่ยนสถานะ -> โชว์ QR
const openTable = async (table) => {
    try {
        // เรียก API เปลี่ยนสถานะเป็น occupied
        // หมายเหตุ: Backend ต้องรองรับ PUT /api/admin/tables/{id} {status: 'occupied'}
        await axios.put(`/api/admin/tables/${table.id}`, { status: 'occupied' });

        // อัปเดตหน้าจอทันที (Optimistic UI)
        table.status = 'occupied';

        // โชว์ QR Code ทันที
        showQR(table);

    } catch (e) {
        alert('เกิดข้อผิดพลาดในการเปิดโต๊ะ');
        console.error(e);
    }
}

const showQR = (table) => {
    currentTable.value = table;
    const baseUrl = window.location.origin;
    const url = `${baseUrl}/table/${table.id}`;
    customerLink.value = url;

    // ใช้ API สร้าง QR (ฟรี)
    qrUrl.value = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&color=333&bgcolor=fff&data=${encodeURIComponent(url)}`;

    document.getElementById('qr_table_modal').showModal();
}

const forceCloseTable = async (table) => {
    if(!confirm(`ต้องการบังคับปิดโต๊ะ ${table.name} ใช่ไหม?`)) return;
    try {
        await axios.put(`/api/admin/tables/${table.id}`, { status: 'hidden' }); // กลับไปเป็นปิด/ว่าง
        fetchTables();
    } catch (e) { alert('Error closing table'); }
}

// Helpers สีและไอคอน
const getBorderColor = (s) => {
    if (s === 'occupied') return 'border-orange-400 bg-white';
    return 'border-slate-200 bg-slate-50 opacity-75 grayscale';
}
const getBgColor = (s) => {
    if (s === 'occupied') return 'bg-orange-50 text-orange-500';
    return 'bg-slate-200 text-slate-400';
}
const getBadgeColor = (s) => {
    if (s === 'occupied') return 'badge-warning text-white';
    return 'badge-ghost text-slate-400';
}
const getStatusText = (s) => {
    const map = { available: 'ว่าง (ปิด)', occupied: 'มีลูกค้า', hidden: 'ปิดปรับปรุง' };
    return map[s] || 'ปิดอยู่';
}
const getIcon = (s) => {
    if (s === 'occupied') return '🧑‍🤝‍🧑';
    return '🔒'; // แม่กุญแจล็อค แสดงว่าปิดอยู่
}

onMounted(() => {
    fetchTables();
    // Auto-refresh ทุก 10 วิ เพื่อดูว่าโต๊ะไหนเช็คบิลไปแล้วบ้าง
    setInterval(fetchTables, 10000);
});
</script>
