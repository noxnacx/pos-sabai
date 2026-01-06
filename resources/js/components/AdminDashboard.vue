<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <admin-sidebar></admin-sidebar>

    <div class="lg:pl-64 transition-all duration-300">

        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">👋 สวัสดี, ผู้จัดการร้าน</h1>
                    <p class="text-sm text-slate-500">ภาพรวมของร้านประจำวันที่ {{ currentDate }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-slate-700">Admin Owner</div>
                        <div class="text-xs text-slate-400">Super Admin</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border-2 border-white shadow-sm">
                        AD
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 font-medium mb-1">ยอดขายวันนี้</p>
                            <h2 class="text-3xl font-black text-slate-800">฿{{ stats.today_sales?.toLocaleString() || 0 }}</h2>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <a href="/admin/reports/sales" class="mt-4 inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer">
                        ดูรายงานผลประกอบการ >
                    </a>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 font-medium mb-1">ออเดอร์วันนี้</p>
                            <h2 class="text-3xl font-black text-slate-800">{{ stats.today_orders || 0 }} <span class="text-lg text-slate-400 font-normal">รายการ</span></h2>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-xl text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded w-fit font-bold">รอเสิร์ฟ/กำลังทาน</div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-500 font-medium mb-1">ลูกค้าในร้าน</p>
                            <h2 class="text-3xl font-black text-slate-800">{{ activeTableCount }} <span class="text-lg text-slate-400 font-normal">โต๊ะ</span></h2>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded w-fit font-bold">กำลังใช้บริการ</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">🔥 สินค้าขายดี (Top 5)</h3>
                            <a href="/admin/reports/products" class="text-indigo-600 text-sm font-bold hover:underline">ดูทั้งหมด ></a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead class="bg-slate-50 text-slate-500 text-xs font-bold">
                                    <tr>
                                        <th class="pl-6 py-3 w-10">#</th>
                                        <th>ชื่อเมนู</th>
                                        <th class="text-right pr-6">ยอดขาย</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-slate-50">
                                    <tr v-for="(item, index) in topSellingProducts" :key="index" class="hover:bg-slate-50">
                                        <td class="pl-6 font-bold text-slate-400">{{ index + 1 }}</td>
                                        <td class="font-medium text-slate-700">{{ item.product_name }}</td>
                                        <td class="text-right pr-6"><span class="badge badge-primary badge-outline font-bold">{{ item.total_qty }} จาน</span></td>
                                    </tr>
                                    <tr v-if="topSellingProducts.length === 0">
                                        <td colspan="3" class="text-center py-6 text-slate-400">ไม่มีข้อมูลการขาย</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">❄️ สินค้าขายไม่ดี / เคลื่อนไหวน้อย (5 อันดับ)</h3>
                            <span class="text-xs text-slate-400">ควรจัดโปรโมชั่น</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead class="bg-slate-50 text-slate-500 text-xs font-bold">
                                    <tr>
                                        <th class="pl-6 py-3 text-left">ชื่อเมนู</th>
                                        <th class="text-right pr-6">ยอดขายรวม</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-slate-50">
                                    <tr v-for="item in worstSellingProducts" :key="item.id" class="hover:bg-slate-50">
                                        <td class="pl-6 py-3 font-medium text-slate-600">{{ item.name }}</td>
                                        <td class="text-right pr-6 text-slate-400">{{ item.sold_qty || 0 }} จาน</td>
                                    </tr>
                                    <tr v-if="worstSellingProducts.length === 0">
                                        <td colspan="2" class="text-center py-6 text-slate-400">ข้อมูลไม่เพียงพอ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col h-fit">
                    <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                        <h3 class="font-bold text-lg text-slate-800">สถานะโต๊ะล่าสุด 🕒</h3>
                        <p class="text-xs text-slate-500 mt-1">อัปเดตแบบ Real-time</p>
                    </div>

                    <div class="p-4 space-y-3 max-h-[500px] overflow-y-auto">
                        <div v-for="table in displayTables" :key="table.id"
                             class="flex items-center justify-between p-3 rounded-xl border transition-all"
                             :class="table.status === 'occupied' ? 'bg-emerald-50 border-emerald-200 shadow-sm' : 'bg-white border-slate-100 opacity-60'">

                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shadow-sm"
                                     :class="table.status === 'occupied' ? 'bg-white text-emerald-600 ring-2 ring-emerald-100' : 'bg-slate-100 text-slate-400'">
                                    {{ table.status === 'occupied' ? '🧑‍🤝‍🧑' : '🪑' }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-700 text-sm">{{ table.name }}</h4>
                                    <p class="text-[10px] font-bold uppercase tracking-wider"
                                       :class="table.status === 'occupied' ? 'text-emerald-600' : 'text-slate-400'">
                                        {{ table.status === 'occupied' ? 'กำลังใช้งาน' : 'ว่าง / ปิด' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <span class="flex h-3 w-3 relative">
                                  <span v-if="table.status === 'occupied'" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3" :class="table.status === 'occupied' ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-b-2xl text-center border-t border-slate-100">
                        <a href="/admin/tables" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                            จัดการผังโต๊ะทั้งหมด →
                        </a>
                    </div>
                </div>

            </div>

        </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue';

const stats = ref({ today_sales: 0, today_orders: 0, top_products: [] });
const tables = ref([]);
const products = ref([]); // เก็บสินค้าทั้งหมดเพื่อหาตัวขายไม่ดี

const currentDate = new Date().toLocaleDateString('th-TH', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

// Mockup โต๊ะ (กรณี Database ยังไม่มีข้อมูล หรือพึ่งเริ่มระบบ)
const mockTables = [
    { id: 1, name: 'Table 1', status: 'available' },
    { id: 2, name: 'Table 2', status: 'occupied' },
    { id: 3, name: 'Table 3', status: 'available' },
    { id: 4, name: 'VIP 1', status: 'available' },
    { id: 5, name: 'Garden', status: 'available' },
];

const fetchData = async () => {
    try {
        const statsRes = await axios.get('/api/admin/stats');
        stats.value = statsRes.data;

        const tablesRes = await axios.get('/api/admin/tables');
        tables.value = tablesRes.data;

        const productsRes = await axios.get('/api/products');
        products.value = Array.isArray(productsRes.data) ? productsRes.data : productsRes.data.data;

    } catch (e) { console.error("Error fetching dashboard data:", e); }
}

// ✨ Computed: ตัดข้อมูลสินค้าขายดีให้เหลือแค่ 5 อันดับแรก
const topSellingProducts = computed(() => {
    // ถ้า API ส่งมาเยอะ ก็ตัดเหลือ 5
    return stats.value.top_products?.slice(0, 5) || [];
});

// ✨ Computed: หาสินค้าขายไม่ดี (5 อันดับท้ายสุด)
const worstSellingProducts = computed(() => {
    if (!products.value.length) return [];

    // กรองสินค้าที่เปิดขาย (active) -> เรียงตาม sold_qty น้อยไปมาก -> ตัดมา 5 ตัว
    return [...products.value]
        .filter(p => p.is_active)
        .map(p => ({ ...p, sold_qty: p.sold_qty || 0 }))
        .sort((a, b) => a.sold_qty - b.sold_qty)
        .slice(0, 5); // ✅ ตัดเหลือ 5 อันดับ
});

const displayTables = computed(() => {
    if (!tables.value || tables.value.length === 0) return mockTables;
    return tables.value;
});

const activeTableCount = computed(() => {
    return displayTables.value.filter(t => t.status === 'occupied').length;
});

onMounted(() => {
    fetchData();
    setInterval(fetchData, 30000); // Auto-refresh 30s
});
</script>
