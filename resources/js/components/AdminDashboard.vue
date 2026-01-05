<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <!-- 🟢 1. เรียกใช้ Sidebar ตรงนี้ -->
    <admin-sidebar></admin-sidebar>

    <!-- 🟡 2. เนื้อหาหลัก (ขยับขวาเพื่อหลบ Sidebar) -->
    <div class="lg:pl-64 transition-all duration-300">

        <!-- Topbar -->
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-slate-100">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
                    <p class="text-sm text-slate-400">ภาพรวมร้านอาหารประจำวัน</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-slate-700">Admin Owner</div>
                        <div class="text-xs text-slate-400">{{ currentDate }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border-2 border-white shadow-sm">
                        AD
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-8">

            <!-- 📊 Stats Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <!-- Card 1: Sales -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-400 mb-1">ยอดขายวันนี้</p>
                            <h3 class="text-3xl font-black text-slate-800">฿{{ stats.today_sales?.toLocaleString() || 0 }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-xs text-emerald-600 font-bold bg-emerald-50 w-fit px-2 py-1 rounded-lg">
                        <span>+ รายได้เข้าแล้ว</span>
                    </div>
                </div>

                <!-- Card 2: Orders -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-400 mb-1">ออเดอร์วันนี้</p>
                            <h3 class="text-3xl font-black text-slate-800">{{ stats.today_orders || 0 }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-xs text-blue-600 font-bold bg-blue-50 w-fit px-2 py-1 rounded-lg">
                        <span>บิลที่ชำระเงินแล้ว</span>
                    </div>
                </div>

                <!-- Card 3: Active Tables -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-400 mb-1">กำลังใช้บริการ</p>
                            <h3 class="text-3xl font-black text-slate-800">{{ stats.active_orders || 0 }} <span class="text-lg font-normal text-slate-400">โต๊ะ</span></h3>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-xs text-orange-600 font-bold bg-orange-50 w-fit px-2 py-1 rounded-lg">
                        <span>Active Orders</span>
                    </div>
                </div>

            </div>

            <!-- 🏆 Top Products Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-800">สินค้าขายดี (Top 5)</h2>
                    <button class="text-indigo-600 text-sm font-bold hover:underline">ดูรายงานเต็ม ></button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th class="pl-8 py-4 w-16">#</th>
                                <th>ชื่อเมนู</th>
                                <th class="text-right pr-8">จำนวนที่ขาย</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(item, index) in stats.top_products" :key="index" class="hover:bg-slate-50/50">
                                <td class="pl-8 font-bold text-slate-400">{{ index + 1 }}</td>
                                <td class="font-bold text-slate-700 text-base">{{ item.product_name }}</td>
                                <td class="text-right pr-8">
                                    <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-bold text-sm">
                                        {{ item.total_qty }} ชิ้น
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!stats.top_products?.length">
                                <td colspan="3" class="text-center py-8 text-slate-400">ยังไม่มีข้อมูลการขายวันนี้</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue'; // 🟢 Import Sidebar เข้ามา

const stats = ref({});
const currentDate = new Date().toLocaleDateString('th-TH', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

const fetchStats = async () => {
    try {
        const res = await axios.get('/api/admin/stats');
        stats.value = res.data;
    } catch (e) { console.error(e); }
}

onMounted(() => {
    fetchStats();
    setInterval(fetchStats, 60000);
});
</script>
